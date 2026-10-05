<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SecurityDepositModel;
use App\Models\AuditLogModel;

class SecurityDepositController extends BaseController
{
    protected $depositModel;
    protected $auditModel;

    public function __construct()
    {
        $this->depositModel = new SecurityDepositModel();
        $this->auditModel   = new AuditLogModel();
    }

    public function index()
    {
        $status = trim($this->request->getGet('refund_status') ?? '');
        $search = trim($this->request->getGet('search') ?? '');

        $builder = $this->depositModel->select('security_deposits.*, t.full_name as tenant_name, t.tenant_code, l.agreement_number, p.title as property_title, u.unit_number')
            ->join('tenants t', 't.id = security_deposits.tenant_id')
            ->join('lease_agreements l', 'l.id = security_deposits.lease_id')
            ->join('properties p', 'p.id = l.property_id')
            ->join('property_units u', 'u.id = l.property_unit_id', 'left');

        if ($status !== '') {
            $builder->where('security_deposits.refund_status', $status);
        }

        if ($search !== '') {
            $builder->groupStart()
                ->like('security_deposits.deposit_number', $search)
                ->orLike('t.full_name', $search)
                ->orLike('t.tenant_code', $search)
                ->orLike('l.agreement_number', $search)
                ->groupEnd();
        }

        $deposits = $builder->orderBy('security_deposits.id', 'DESC')->paginate(15);
        $pager    = $this->depositModel->pager;

        $db = \Config\Database::connect();
        $totalHeld = $db->table('security_deposits')->where('refund_status', 'held')->selectSum('amount')->get()->getRow()->amount ?? 0;
        $totalRefunded = $db->table('security_deposits')->whereIn('refund_status', ['refunded', 'partially_refunded'])->selectSum('amount')->get()->getRow()->amount ?? 0;
        $totalAdjusted = $db->table('security_deposits')->selectSum('adjusted_amount')->get()->getRow()->adjusted_amount ?? 0;

        $kpi = [
            'total_count'    => $db->table('security_deposits')->countAllResults(),
            'total_held'     => (float)$totalHeld,
            'total_refunded' => (float)$totalRefunded,
            'total_adjusted' => (float)$totalAdjusted,
        ];

        return view('agreements/deposit_index', [
            'title'    => 'Security Deposits - Real Estate ERP',
            'deposits' => $deposits,
            'pager'    => $pager,
            'filters'  => ['refund_status' => $status, 'search' => $search],
            'kpi'      => $kpi,
        ]);
    }

    public function processRefund(int $id)
    {
        $deposit = $this->depositModel->find($id);
        if (!$deposit) {
            return redirect()->to(base_url('deposits'))->with('error', 'Deposit record not found.');
        }

        $rules = [
            'refund_status'   => 'required|in_list[refunded,partially_refunded,forfeited]',
            'refund_amount'   => 'required|numeric|greater_than_equal_to[0]',
            'adjusted_amount' => 'permit_empty|numeric|greater_than_equal_to[0]',
            'refund_date'     => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $status         = $this->request->getPost('refund_status');
        $refundAmount   = (float)$this->request->getPost('refund_amount');
        $adjustedAmount = (float)($this->request->getPost('adjusted_amount') ?? 0);
        $refundDate     = $this->request->getPost('refund_date');
        $reason         = trim($this->request->getPost('adjustment_reason') ?? '');
        $remarks        = trim($this->request->getPost('remarks') ?? '');

        if ($refundAmount + $adjustedAmount > (float)$deposit['amount']) {
            return redirect()->back()->with('error', "Sum of Refunded (₹{$refundAmount}) and Adjusted (₹{$adjustedAmount}) cannot exceed total deposit (₹{$deposit['amount']}).");
        }

        $this->depositModel->update($id, [
            'refundable_amount' => $refundAmount,
            'adjusted_amount'   => $adjustedAmount,
            'refund_date'       => $refundDate,
            'refund_status'     => $status,
            'adjustment_reason' => $reason ?: $deposit['adjustment_reason'],
            'remarks'           => $remarks ?: $deposit['remarks'],
        ]);

        $userId = session()->get('user_id');
        $this->auditModel->record(
            $userId,
            'DEPOSIT_SETTLED',
            'Deposits',
            $id,
            "Settled deposit {$deposit['deposit_number']} status: {$status}, Refund: ₹{$refundAmount}, Adjusted: ₹{$adjustedAmount}"
        );

        return redirect()->back()->with('success', "Security deposit {$deposit['deposit_number']} settled successfully.");
    }
}
