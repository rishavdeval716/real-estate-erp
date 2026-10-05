<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RentCollectionModel;
use App\Models\RentDemandModel;
use App\Models\AuditLogModel;
use App\Models\CompanyModel;

class RentCollectionController extends BaseController
{
    protected $collectionModel;
    protected $demandModel;
    protected $auditModel;
    protected $companyModel;

    public function __construct()
    {
        $this->collectionModel = new RentCollectionModel();
        $this->demandModel     = new RentDemandModel();
        $this->auditModel      = new AuditLogModel();
        $this->companyModel    = new CompanyModel();
    }

    public function index()
    {
        $search = trim($this->request->getGet('search') ?? '');

        $builder = $this->collectionModel->select('rent_collections.*, rd.demand_number, rd.billing_period, t.full_name as tenant_name, t.tenant_code, l.agreement_number')
            ->join('rent_demands rd', 'rd.id = rent_collections.rent_demand_id')
            ->join('tenants t', 't.id = rent_collections.tenant_id')
            ->join('lease_agreements l', 'l.id = rent_collections.lease_id');

        if ($search !== '') {
            $builder->groupStart()
                ->like('rent_collections.collection_number', $search)
                ->orLike('rd.demand_number', $search)
                ->orLike('t.full_name', $search)
                ->orLike('t.tenant_code', $search)
                ->orLike('rent_collections.transaction_reference', $search)
                ->groupEnd();
        }

        $collections = $builder->orderBy('rent_collections.id', 'DESC')->paginate(15);
        $pager       = $this->collectionModel->pager;

        $db = \Config\Database::connect();
        $totalCollected = $db->table('rent_collections')->selectSum('amount')->get()->getRow()->amount ?? 0;
        $totalCount     = $db->table('rent_collections')->countAllResults();

        $kpi = [
            'total_collections' => (float)$totalCollected,
            'total_count'       => $totalCount,
        ];

        return view('agreements/collection_index', [
            'title'       => 'Rent Collections & Receipts - Real Estate ERP',
            'collections' => $collections,
            'pager'       => $pager,
            'filters'     => ['search' => $search],
            'kpi'         => $kpi,
        ]);
    }

    public function store()
    {
        $rules = [
            'rent_demand_id'        => 'required|is_natural_no_zero',
            'amount'                => 'required|numeric|greater_than[0]',
            'payment_date'          => 'required|valid_date',
            'payment_method'        => 'required|in_list[Cash,Bank Transfer,NEFT,RTGS,IMPS,UPI,Cheque,Online Gateway,Other]',
            'transaction_reference' => 'permit_empty|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $demandId = (int)$this->request->getPost('rent_demand_id');
        $demand   = $this->demandModel->find($demandId);

        if (!$demand) {
            return redirect()->back()->with('error', 'Rent demand not found.');
        }

        $amount = (float)$this->request->getPost('amount');
        if ($amount > (float)$demand['balance_amount'] + 0.01) {
            return redirect()->back()->with('error', "Collection amount (₹{$amount}) exceeds current balance due (₹{$demand['balance_amount']}).");
        }

        $number = $this->collectionModel->generateCollectionNumber();
        $userId = session()->get('user_id');

        $collectionData = [
            'collection_number'     => $number,
            'rent_demand_id'        => $demandId,
            'tenant_id'             => $demand['tenant_id'],
            'lease_id'              => $demand['lease_id'],
            'amount'                => $amount,
            'payment_date'          => $this->request->getPost('payment_date'),
            'payment_method'        => $this->request->getPost('payment_method'),
            'transaction_reference' => trim($this->request->getPost('transaction_reference') ?? '') ?: null,
            'remarks'               => trim($this->request->getPost('remarks') ?? '') ?: null,
            'received_by'           => $userId,
            'created_at'            => date('Y-m-d H:i:s'),
        ];

        $colId = $this->collectionModel->insert($collectionData);

        // Update Demand Balance and Status
        $newPaid    = (float)$demand['paid_amount'] + $amount;
        $newBalance = max(0.00, (float)$demand['total_amount'] - $newPaid);
        $newStatus  = $newBalance <= 0.01 ? 'paid' : 'partially_paid';

        $this->demandModel->update($demandId, [
            'paid_amount'    => $newPaid,
            'balance_amount' => $newBalance,
            'status'         => $newStatus,
        ]);

        $this->auditModel->record(
            $userId,
            'RENT_COLLECTION_RECORDED',
            'Demands',
            $demandId,
            "Recorded rent collection {$number} of ₹{$amount} against Demand #{$demand['demand_number']}"
        );

        return redirect()->to(base_url("rent-collections/receipt/{$colId}"))->with('success', "Rent collection {$number} recorded successfully.");
    }

    public function receipt(int $id)
    {
        $collection = $this->collectionModel->getCollectionWithDetails($id);
        if (!$collection) {
            return redirect()->to(base_url('rent-collections'))->with('error', 'Collection receipt not found.');
        }

        $company = $this->companyModel->first() ?? [
            'name'    => 'Real Estate ERP Enterprise Ltd.',
            'email'   => 'info@realestate-erp.local',
            'phone'   => '+91 98765 43210',
            'address' => 'Corporate Tower, Financial District',
            'city'    => 'Mumbai',
            'state'   => 'Maharashtra',
        ];

        return view('agreements/collection_receipt', [
            'title'      => "Rent Receipt {$collection['collection_number']}",
            'collection' => $collection,
            'company'    => $company,
        ]);
    }
}
