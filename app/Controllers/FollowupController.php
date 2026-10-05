<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LeadFollowupModel;
use App\Models\LeadModel;
use App\Models\AuditLogModel;
use App\Libraries\LeadAssignmentService;

class FollowupController extends BaseController
{
    protected $followupModel;
    protected $leadModel;
    protected $auditModel;

    public function __construct()
    {
        $this->followupModel = new LeadFollowupModel();
        $this->leadModel     = new LeadModel();
        $this->auditModel    = new AuditLogModel();
    }

    /**
     * Follow-ups dashboard with Today, Overdue, and Upcoming views
     */
    public function index()
    {
        $viewType = $this->request->getGet('tab') ?? 'today';
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd   = date('Y-m-d 23:59:59');

        $builder = $this->followupModel->select('lead_followups.*, 
            leads.lead_code, 
            leads.first_name, 
            leads.last_name, 
            leads.phone as lead_phone,
            leads.priority as lead_priority,
            users.name as assigned_to_name')
            ->join('leads', 'leads.id = lead_followups.lead_id', 'left')
            ->join('users', 'users.id = lead_followups.assigned_to', 'left');

        if ($viewType === 'today') {
            $builder->where('lead_followups.status', 'Pending')
                ->where('lead_followups.scheduled_at >=', $todayStart)
                ->where('lead_followups.scheduled_at <=', $todayEnd)
                ->orderBy('lead_followups.scheduled_at', 'ASC');
        } elseif ($viewType === 'overdue') {
            $builder->where('lead_followups.status', 'Pending')
                ->where('lead_followups.scheduled_at <', $todayStart)
                ->orderBy('lead_followups.scheduled_at', 'ASC');
        } elseif ($viewType === 'upcoming') {
            $builder->where('lead_followups.status', 'Pending')
                ->where('lead_followups.scheduled_at >', $todayEnd)
                ->orderBy('lead_followups.scheduled_at', 'ASC');
        } elseif ($viewType === 'completed') {
            $builder->where('lead_followups.status', 'Completed')
                ->orderBy('lead_followups.completed_at', 'DESC');
        } else {
            $builder->orderBy('lead_followups.scheduled_at', 'DESC');
        }

        $followups = $builder->paginate(15);

        // Counts
        $kpi = [
            'today'     => $this->followupModel->getTodayPendingCount(),
            'overdue'   => $this->followupModel->getOverdueCount(),
            'upcoming'  => $this->followupModel->where('status', 'Pending')->where('scheduled_at >', $todayEnd)->countAllResults(),
            'completed' => $this->followupModel->where('status', 'Completed')->countAllResults(),
        ];

        $data = [
            'title'     => 'CRM Follow-up Management - Real Estate ERP',
            'followups' => $followups,
            'pager'     => $this->followupModel->pager,
            'viewType'  => $viewType,
            'kpi'       => $kpi,
        ];

        return view('followups/index', $data);
    }

    /**
     * Schedule a new follow-up
     */
    public function store()
    {
        $leadId = (int)$this->request->getPost('lead_id');
        $lead   = $this->leadModel->find($leadId);
        if (!$lead) {
            return redirect()->back()->with('error', 'Valid lead is required.');
        }

        $type        = $this->request->getPost('followup_type') ?: 'Phone Call';
        $scheduledAt = $this->request->getPost('scheduled_at');
        $assignedTo  = $this->request->getPost('assigned_to') ?: ($lead['assigned_user_id'] ?: session()->get('user_id'));
        $notes       = trim($this->request->getPost('notes') ?? '');

        if (empty($scheduledAt)) {
            return redirect()->back()->with('error', 'Scheduled date and time are required.');
        }

        $fuId = $this->followupModel->insert([
            'lead_id'          => $leadId,
            'assigned_to'      => $assignedTo,
            'followup_type'    => $type,
            'scheduled_at'     => $scheduledAt,
            'completed_at'     => null,
            'status'           => 'Pending',
            'notes'            => $notes,
            'created_by'       => session()->get('user_id'),
        ]);

        $this->auditModel->record(
            session()->get('user_id'),
            'FOLLOWUP_SCHEDULED',
            'Followups',
            $fuId,
            "Scheduled {$type} follow-up for Lead {$lead['lead_code']} on {$scheduledAt}"
        );

        $redirectUrl = $this->request->getPost('redirect_to') ?: "/leads/view/{$leadId}";
        return redirect()->to($redirectUrl)->with('success', 'Follow-up scheduled successfully.');
    }

    /**
     * Mark follow-up as completed with outcome & optional next date
     */
    public function complete($id)
    {
        $fu = $this->followupModel->find($id);
        if (!$fu) {
            return redirect()->back()->with('error', 'Follow-up not found.');
        }

        $outcome        = trim($this->request->getPost('outcome') ?? '');
        $notes          = trim($this->request->getPost('notes') ?? '');
        $nextFollowupAt = $this->request->getPost('next_followup_at');

        $this->followupModel->update($id, [
            'status'           => 'Completed',
            'completed_at'     => date('Y-m-d H:i:s'),
            'outcome'          => $outcome,
            'notes'            => $notes ? ($fu['notes'] . " \n[Completion Note: {$notes}]") : $fu['notes'],
            'next_followup_at' => $nextFollowupAt ?: null,
        ]);

        // If next follow-up date was provided, automatically schedule the next task
        if (!empty($nextFollowupAt)) {
            $this->followupModel->insert([
                'lead_id'       => $fu['lead_id'],
                'assigned_to'   => $fu['assigned_to'],
                'followup_type' => $this->request->getPost('next_followup_type') ?: 'Phone Call',
                'scheduled_at'  => $nextFollowupAt,
                'status'        => 'Pending',
                'notes'         => 'Follow-up created following completion of ' . $fu['followup_type'],
                'created_by'    => session()->get('user_id'),
            ]);
        }

        $this->auditModel->record(
            session()->get('user_id'),
            'FOLLOWUP_COMPLETED',
            'Followups',
            $id,
            "Completed follow-up for Lead #{$fu['lead_id']} [Outcome: {$outcome}]"
        );

        return redirect()->back()->with('success', 'Follow-up marked as completed.');
    }

    /**
     * Reschedule follow-up
     */
    public function reschedule($id)
    {
        $fu = $this->followupModel->find($id);
        if (!$fu) {
            return redirect()->back()->with('error', 'Follow-up not found.');
        }

        $newDate = $this->request->getPost('scheduled_at');
        $reason  = trim($this->request->getPost('reason') ?? '');

        if (empty($newDate)) {
            return redirect()->back()->with('error', 'Please provide a valid new date and time.');
        }

        $this->followupModel->update($id, [
            'scheduled_at' => $newDate,
            'notes'        => $fu['notes'] . ($reason ? " \n[Rescheduled to {$newDate}: {$reason}]" : ''),
        ]);

        $this->auditModel->record(
            session()->get('user_id'),
            'FOLLOWUP_RESCHEDULED',
            'Followups',
            $id,
            "Rescheduled follow-up to {$newDate}"
        );

        return redirect()->back()->with('success', 'Follow-up rescheduled.');
    }

    /**
     * Cancel follow-up
     */
    public function cancel($id)
    {
        $fu = $this->followupModel->find($id);
        if (!$fu) {
            return redirect()->back()->with('error', 'Follow-up not found.');
        }

        $reason = trim($this->request->getPost('reason') ?? '');

        $this->followupModel->update($id, [
            'status' => 'Cancelled',
            'notes'  => $fu['notes'] . ($reason ? " \n[Cancelled: {$reason}]" : ''),
        ]);

        $this->auditModel->record(
            session()->get('user_id'),
            'FOLLOWUP_CANCELLED',
            'Followups',
            $id,
            "Cancelled follow-up for Lead #{$fu['lead_id']}"
        );

        return redirect()->back()->with('success', 'Follow-up cancelled.');
    }
}
