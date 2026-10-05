<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RentDemandModel;
use App\Models\LeaseAgreementModel;
use App\Models\RentCollectionModel;
use App\Models\AuditLogModel;
use App\Models\CompanyModel;

class RentDemandController extends BaseController
{
    protected $demandModel;
    protected $leaseModel;
    protected $collectionModel;
    protected $auditModel;
    protected $companyModel;

    public function __construct()
    {
        $this->demandModel     = new RentDemandModel();
        $this->leaseModel      = new LeaseAgreementModel();
        $this->collectionModel = new RentCollectionModel();
        $this->auditModel      = new AuditLogModel();
        $this->companyModel    = new CompanyModel();
    }

    public function index()
    {
        // Update overdue status and late fees automatically
        $this->demandModel->applyLateFees();

        $search  = trim($this->request->getGet('search') ?? '');
        $status  = trim($this->request->getGet('status') ?? '');
        $period  = trim($this->request->getGet('billing_period') ?? '');

        $builder = $this->demandModel->select('rent_demands.*, t.full_name as tenant_name, t.tenant_code, l.agreement_number, p.title as property_title, u.unit_number')
            ->join('tenants t', 't.id = rent_demands.tenant_id')
            ->join('lease_agreements l', 'l.id = rent_demands.lease_id')
            ->join('properties p', 'p.id = rent_demands.property_id')
            ->join('property_units u', 'u.id = rent_demands.property_unit_id', 'left');

        if ($search !== '') {
            $builder->groupStart()
                ->like('rent_demands.demand_number', $search)
                ->orLike('t.full_name', $search)
                ->orLike('t.tenant_code', $search)
                ->orLike('l.agreement_number', $search)
                ->orLike('p.title', $search)
                ->groupEnd();
        }

        if ($status !== '') {
            $builder->where('rent_demands.status', $status);
        }

        if ($period !== '') {
            $builder->where('rent_demands.billing_period', $period);
        }

        $demands = $builder->orderBy('rent_demands.id', 'DESC')->paginate(15);
        $pager   = $this->demandModel->pager;

        $db = \Config\Database::connect();
        $totalDemanded = $db->table('rent_demands')->selectSum('total_amount')->get()->getRow()->total_amount ?? 0;
        $totalCollected = $db->table('rent_demands')->selectSum('paid_amount')->get()->getRow()->paid_amount ?? 0;
        $totalBalance   = $db->table('rent_demands')->selectSum('balance_amount')->get()->getRow()->balance_amount ?? 0;
        $overdueCount   = $db->table('rent_demands')->where('status', 'overdue')->countAllResults();

        $kpi = [
            'total_demanded'  => (float)$totalDemanded,
            'total_collected' => (float)$totalCollected,
            'total_balance'   => (float)$totalBalance,
            'overdue_count'   => $overdueCount,
        ];

        // Active leases for quick demand generation dropdown
        $activeLeases = $this->leaseModel->select('lease_agreements.*, t.full_name as tenant_name, p.title as property_title, u.unit_number')
            ->join('tenants t', 't.id = lease_agreements.tenant_id')
            ->join('properties p', 'p.id = lease_agreements.property_id')
            ->join('property_units u', 'u.id = lease_agreements.property_unit_id', 'left')
            ->whereIn('lease_agreements.status', ['active', 'expiring_soon'])
            ->findAll();

        return view('agreements/demand_index', [
            'title'        => 'Rent Demands & Billing - Real Estate ERP',
            'demands'      => $demands,
            'pager'        => $pager,
            'filters'      => ['search' => $search, 'status' => $status, 'billing_period' => $period],
            'kpi'          => $kpi,
            'activeLeases' => $activeLeases,
        ]);
    }

    public function generate()
    {
        $rules = [
            'lease_id'       => 'required|is_natural_no_zero',
            'billing_period' => 'required|regex_match[/^[0-9]{4}-(0[1-9]|1[0-2])$/]',
            'due_date'       => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $leaseId = (int)$this->request->getPost('lease_id');
        $period  = trim($this->request->getPost('billing_period'));

        // Prevent duplicate rent demand for the same lease and period
        $existing = $this->demandModel->where('lease_id', $leaseId)->where('billing_period', $period)->first();
        if ($existing) {
            return redirect()->back()->with('error', "A rent demand for Lease #{$leaseId} for period {$period} already exists ({$existing['demand_number']}).");
        }

        $lease = $this->leaseModel->find($leaseId);
        if (!$lease) {
            return redirect()->back()->with('error', 'Lease not found.');
        }

        $baseRent    = (float)$lease['monthly_rent'];
        $maintenance = (float)($this->request->getPost('maintenance') ?? $lease['maintenance_charges'] ?? 0);
        $other       = (float)($this->request->getPost('other_charges') ?? 0);
        $taxRate     = 18.0; // Standard 18% GST if commercial
        $tax         = $lease['agreement_type'] === 'commercial' ? round(($baseRent + $maintenance + $other) * ($taxRate / 100), 2) : 0.00;
        $total       = $baseRent + $maintenance + $other + $tax;

        $number = $this->demandModel->generateDemandNumber();
        $userId = session()->get('user_id');

        $demandData = [
            'demand_number'    => $number,
            'tenant_id'        => $lease['tenant_id'],
            'lease_id'         => $leaseId,
            'property_id'      => $lease['property_id'],
            'property_unit_id' => $lease['property_unit_id'],
            'billing_period'   => $period,
            'base_rent'        => $baseRent,
            'maintenance'      => $maintenance,
            'other_charges'    => $other,
            'late_fee'         => 0.00,
            'tax'              => $tax,
            'total_amount'     => $total,
            'paid_amount'      => 0.00,
            'balance_amount'   => $total,
            'due_date'         => $this->request->getPost('due_date'),
            'status'           => 'unpaid',
            'generated_by'     => $userId,
        ];

        $demandId = $this->demandModel->insert($demandData);

        $this->auditModel->record(
            $userId,
            'RENT_DEMAND_GENERATED',
            'Demands',
            $demandId,
            "Generated rent demand {$number} for period {$period} - ₹{$total}"
        );

        return redirect()->to(base_url("rent-demands/view/{$demandId}"))->with('success', "Rent demand {$number} generated successfully.");
    }

    public function view(int $id)
    {
        $demand = $this->demandModel->getDemandWithDetails($id);
        if (!$demand) {
            return redirect()->to(base_url('rent-demands'))->with('error', 'Rent demand not found.');
        }

        $collections = $this->collectionModel->where('rent_demand_id', $id)->findAll();
        $company     = $this->companyModel->first() ?? [
            'name'    => 'Real Estate ERP Enterprise Ltd.',
            'email'   => 'info@realestate-erp.local',
            'phone'   => '+91 98765 43210',
            'address' => 'Corporate Tower, Financial District',
            'city'    => 'Mumbai',
            'state'   => 'Maharashtra',
        ];

        return view('agreements/demand_view', [
            'title'       => "Rent Demand {$demand['demand_number']}",
            'demand'      => $demand,
            'collections' => $collections,
            'company'     => $company,
        ]);
    }
}
