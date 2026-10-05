<?php

namespace App\Controllers;

use App\Models\CommissionModel;
use App\Models\CommissionRuleModel;
use App\Models\BookingModel;
use App\Models\UserModel;
use App\Models\AuditLogModel;

class CommissionController extends BaseController
{
    protected CommissionModel $commissionModel;
    protected CommissionRuleModel $ruleModel;
    protected BookingModel $bookingModel;
    protected UserModel $userModel;

    public function __construct()
    {
        $this->commissionModel = new CommissionModel();
        $this->ruleModel       = new CommissionRuleModel();
        $this->bookingModel    = new BookingModel();
        $this->userModel       = new UserModel();
    }

    /**
     * List commissions and active rules
     */
    public function index()
    {
        $filters = [
            'search' => trim((string)$this->request->getGet('search')),
            'status' => trim((string)$this->request->getGet('status')),
            'sort'   => $this->request->getGet('sort') ?? 'commissions.created_at',
            'order'  => $this->request->getGet('order') ?? 'DESC',
        ];

        $result = $this->commissionModel->getCommissionsWithDetails($filters, 15);
        $rules  = $this->ruleModel->orderBy('name', 'ASC')->findAll();
        $agents = $this->userModel->where('deleted_at', null)->where('status', 'active')->orderBy('name', 'ASC')->findAll();
        
        $bookings = $this->bookingModel->select('bookings.id, bookings.booking_number, bookings.final_amount, customers.first_name, customers.last_name')
            ->join('customers', 'customers.id = bookings.customer_id', 'left')
            ->whereIn('bookings.booking_status', ['Draft', 'Pending Confirmation', 'Confirmed'])
            ->where('bookings.deleted_at', null)
            ->orderBy('bookings.id', 'DESC')
            ->findAll();

        return view('commissions/index', [
            'title'       => 'Broker & Agent Commissions',
            'commissions' => $result['commissions'],
            'pager'       => $result['pager'],
            'filters'     => $filters,
            'rules'       => $rules,
            'agents'      => $agents,
            'bookings'    => $bookings,
        ]);
    }

    /**
     * Store new commission rule
     */
    public function storeRule()
    {
        $rules = [
            'name'             => 'required|min_length[3]|max_length[100]',
            'commission_type'  => 'required|in_list[Percentage,Fixed]',
            'commission_value' => 'required|numeric|greater_than[0]',
            'applicable_to'    => 'required|in_list[All,Direct,Broker,Channel Partner]',
            'status'           => 'required|in_list[Active,Inactive]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name'             => trim((string)$this->request->getPost('name')),
            'commission_type'  => $this->request->getPost('commission_type'),
            'commission_value' => (float)$this->request->getPost('commission_value'),
            'applicable_to'    => $this->request->getPost('applicable_to'),
            'status'           => $this->request->getPost('status'),
        ];

        $ruleId = $this->ruleModel->insert($data);
        $userId = session()->get('user_id');

        AuditLogModel::record(
            'Commission Rule Created',
            'commission_rules',
            $ruleId,
            "Created commission rule '{$data['name']}' ({$data['commission_type']}: {$data['commission_value']})",
            $userId
        );

        return redirect()->to(base_url('commissions'))->with('success', 'Commission rule created successfully.');
    }

    /**
     * Assign commission to booking
     */
    public function store()
    {
        $rules = [
            'booking_id'         => 'required|is_not_unique[bookings.id]',
            'agent_user_id'      => 'required|is_not_unique[users.id]',
            'commission_rule_id' => 'permit_empty|is_not_unique[commission_rules.id]',
            'commission_amount'  => 'permit_empty|numeric|greater_than_equal_to[0]',
            'payable_date'       => 'permit_empty|valid_date',
            'remarks'            => 'permit_empty|max_length[500]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $bookingId = (int)$this->request->getPost('booking_id');
        $booking   = $this->bookingModel->find($bookingId);
        if (!$booking) {
            return redirect()->back()->withInput()->with('error', 'Booking record not found.');
        }

        $ruleId = (int)($this->request->getPost('commission_rule_id') ?: 0);
        $bookingAmount = (float)$booking['final_amount'];
        $commissionAmount = (float)($this->request->getPost('commission_amount') ?: 0);

        if ($ruleId > 0) {
            $rule = $this->ruleModel->find($ruleId);
            if ($rule) {
                if ($rule['commission_type'] === 'Percentage') {
                    $commissionAmount = round(($bookingAmount * (float)$rule['commission_value']) / 100, 2);
                } else {
                    $commissionAmount = (float)$rule['commission_value'];
                }
            }
        }

        $userId = session()->get('user_id');
        $data = [
            'booking_id'         => $bookingId,
            'agent_user_id'      => (int)$this->request->getPost('agent_user_id'),
            'commission_rule_id' => $ruleId > 0 ? $ruleId : null,
            'booking_amount'     => $bookingAmount,
            'commission_amount'  => $commissionAmount,
            'status'             => 'Pending',
            'payable_date'       => $this->request->getPost('payable_date') ?: null,
            'remarks'            => $this->request->getPost('remarks'),
        ];

        $commId = $this->commissionModel->insert($data);

        AuditLogModel::record(
            'Commission Created',
            'commissions',
            $commId,
            "Calculated commission of ₹" . number_format($commissionAmount, 2) . " for agent on Booking #{$booking['booking_number']}",
            $userId
        );

        return redirect()->to(base_url("bookings/view/{$bookingId}"))
            ->with('success', "Commission of ₹" . number_format($commissionAmount, 2) . " calculated and assigned successfully.");
    }

    /**
     * Approve commission
     */
    public function approve(int $id)
    {
        $comm = $this->commissionModel->find($id);
        if (!$comm) {
            return redirect()->back()->with('error', 'Commission record not found.');
        }

        $userId = session()->get('user_id');
        $this->commissionModel->update($id, [
            'status' => 'Approved',
        ]);

        AuditLogModel::record(
            'Commission Approved',
            'commissions',
            $id,
            "Approved commission record #{$id} of ₹" . number_format($comm['commission_amount'], 2),
            $userId
        );

        return redirect()->back()->with('success', "Commission #{$id} approved.");
    }

    /**
     * Mark commission as Paid
     */
    public function markPaid(int $id)
    {
        $comm = $this->commissionModel->find($id);
        if (!$comm) {
            return redirect()->back()->with('error', 'Commission record not found.');
        }

        $userId = session()->get('user_id');
        $this->commissionModel->update($id, [
            'status'    => 'Paid',
            'paid_date' => date('Y-m-d'),
        ]);

        AuditLogModel::record(
            'Commission Paid',
            'commissions',
            $id,
            "Marked commission record #{$id} as Paid on " . date('Y-m-d'),
            $userId
        );

        return redirect()->back()->with('success', "Commission #{$id} marked as Paid.");
    }
}
