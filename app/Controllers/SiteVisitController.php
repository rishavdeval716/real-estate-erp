<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SiteVisitModel;
use App\Models\LeadModel;
use App\Models\ProjectModel;
use App\Models\PropertyModel;
use App\Models\PropertyUnitModel;
use App\Models\AuditLogModel;
use App\Libraries\LeadAssignmentService;

class SiteVisitController extends BaseController
{
    protected $visitModel;
    protected $leadModel;
    protected $auditModel;

    public function __construct()
    {
        $this->visitModel = new SiteVisitModel();
        $this->leadModel  = new LeadModel();
        $this->auditModel = new AuditLogModel();
    }

    /**
     * List all site visits with status filtering
     */
    public function index()
    {
        $status = trim($this->request->getGet('status') ?? '');

        $builder = $this->visitModel->select('site_visits.*, 
            leads.lead_code, 
            leads.first_name, 
            leads.last_name, 
            leads.phone as lead_phone,
            projects.name as project_name, 
            properties.title as property_title, 
            property_units.unit_number,
            users.name as assigned_agent_name')
            ->join('leads', 'leads.id = site_visits.lead_id', 'left')
            ->join('projects', 'projects.id = site_visits.project_id', 'left')
            ->join('properties', 'properties.id = site_visits.property_id', 'left')
            ->join('property_units', 'property_units.id = site_visits.property_unit_id', 'left')
            ->join('users', 'users.id = site_visits.assigned_user_id', 'left');

        if (!empty($status)) {
            $builder->where('site_visits.status', $status);
        }

        $visits = $builder->orderBy('site_visits.scheduled_at', 'DESC')->paginate(15);

        $kpi = [
            'total'     => $this->visitModel->countAllResults(),
            'scheduled' => $this->visitModel->whereIn('status', ['Scheduled', 'Confirmed'])->countAllResults(),
            'today'     => $this->visitModel->getTodayVisitsCount(),
            'completed' => $this->visitModel->where('status', 'Completed')->countAllResults(),
        ];

        $data = [
            'title'    => 'Site Visit Management - Real Estate CRM',
            'visits'   => $visits,
            'pager'    => $this->visitModel->pager,
            'status'   => $status,
            'kpi'      => $kpi,
            'statuses' => ['Scheduled', 'Confirmed', 'Completed', 'Cancelled', 'No Show', 'Rescheduled'],
        ];

        return view('site_visits/index', $data);
    }

    /**
     * Schedule a site visit
     */
    public function store()
    {
        $leadId = (int)$this->request->getPost('lead_id');
        $lead   = $this->leadModel->find($leadId);
        if (!$lead) {
            return redirect()->back()->with('error', 'Valid lead is required.');
        }

        $scheduledAt = $this->request->getPost('scheduled_at');
        if (empty($scheduledAt)) {
            return redirect()->back()->with('error', 'Visit date and time are required.');
        }

        $code = $this->visitModel->generateVisitCode();
        $visitType = $this->request->getPost('visit_type') ?: 'Property Visit';
        $assignedTo = $this->request->getPost('assigned_user_id') ?: ($lead['assigned_user_id'] ?: session()->get('user_id'));

        $visitId = $this->visitModel->insert([
            'visit_code'        => $code,
            'lead_id'           => $leadId,
            'project_id'        => $this->request->getPost('project_id') ?: null,
            'property_id'       => $this->request->getPost('property_id') ?: null,
            'property_unit_id'  => $this->request->getPost('property_unit_id') ?: null,
            'assigned_user_id'  => $assignedTo,
            'scheduled_at'      => $scheduledAt,
            'visit_type'        => $visitType,
            'status'            => 'Scheduled',
            'visitor_count'     => (int)($this->request->getPost('visitor_count') ?: 1),
            'remarks'           => trim($this->request->getPost('remarks') ?? ''),
        ]);

        // Advance lead stage to 'Site Visit Scheduled' if currently in early stages
        if (in_array($lead['lead_stage'], ['New', 'Contacted', 'Qualified'], true)) {
            $this->leadModel->update($leadId, [
                'lead_stage'          => 'Site Visit Scheduled',
                'lead_status'         => 'Qualified',
                'site_visit_required' => 'Scheduled',
            ]);
        }

        $this->auditModel->record(
            session()->get('user_id'),
            'SITE_VISIT_SCHEDULED',
            'SiteVisits',
            $visitId,
            "Scheduled site visit {$code} for Lead {$lead['lead_code']} on {$scheduledAt}"
        );

        $redirectUrl = $this->request->getPost('redirect_to') ?: "/leads/view/{$leadId}";
        return redirect()->to($redirectUrl)->with('success', "Site Visit {$code} booked successfully.");
    }

    /**
     * Complete a site visit with rating, feedback, and next action
     */
    public function complete($id)
    {
        $visit = $this->visitModel->find($id);
        if (!$visit) {
            return redirect()->back()->with('error', 'Site visit not found.');
        }

        $rating         = $this->request->getPost('rating') ? (int)$this->request->getPost('rating') : null;
        $interestLevel  = $this->request->getPost('interest_level');
        $feedback       = trim($this->request->getPost('feedback') ?? '');
        $agentObs       = trim($this->request->getPost('agent_observation') ?? '');
        $preferredUnit  = trim($this->request->getPost('preferred_unit') ?? '');
        $priceFeedback  = trim($this->request->getPost('price_feedback') ?? '');
        $nextAction     = $this->request->getPost('next_action');

        $this->visitModel->update($id, [
            'status'            => 'Completed',
            'rating'            => $rating,
            'interest_level'    => $interestLevel,
            'feedback'          => $feedback,
            'agent_observation' => $agentObs,
            'preferred_unit'    => $preferredUnit,
            'price_feedback'    => $priceFeedback,
            'next_action'       => $nextAction,
        ]);

        // Advance lead stage to 'Site Visit Completed' or 'Negotiation'
        $lead = $this->leadModel->find($visit['lead_id']);
        if ($lead && in_array($lead['lead_stage'], ['New', 'Contacted', 'Qualified', 'Site Visit Scheduled'], true)) {
            $targetStage = ($nextAction === 'Negotiation') ? 'Negotiation' : 'Site Visit Completed';
            $this->leadModel->update($lead['id'], [
                'lead_stage'          => $targetStage,
                'site_visit_required' => 'Completed',
            ]);
        }

        $this->auditModel->record(
            session()->get('user_id'),
            'SITE_VISIT_COMPLETED',
            'SiteVisits',
            $id,
            "Completed site visit {$visit['visit_code']}. Rating: {$rating}/5, Next Action: {$nextAction}"
        );

        return redirect()->back()->with('success', "Site visit {$visit['visit_code']} marked as completed with feedback recorded.");
    }

    /**
     * Reschedule site visit
     */
    public function reschedule($id)
    {
        $visit = $this->visitModel->find($id);
        if (!$visit) {
            return redirect()->back()->with('error', 'Site visit not found.');
        }

        $newDate = $this->request->getPost('scheduled_at');
        if (empty($newDate)) {
            return redirect()->back()->with('error', 'New date/time required.');
        }

        $this->visitModel->update($id, [
            'scheduled_at' => $newDate,
            'status'       => 'Rescheduled',
        ]);

        $this->auditModel->record(
            session()->get('user_id'),
            'SITE_VISIT_RESCHEDULED',
            'SiteVisits',
            $id,
            "Rescheduled site visit {$visit['visit_code']} to {$newDate}"
        );

        return redirect()->back()->with('success', 'Site visit rescheduled.');
    }

    /**
     * Cancel site visit
     */
    public function cancel($id)
    {
        $visit = $this->visitModel->find($id);
        if (!$visit) {
            return redirect()->back()->with('error', 'Site visit not found.');
        }

        $reason = trim($this->request->getPost('reason') ?? '');

        $this->visitModel->update($id, [
            'status'  => 'Cancelled',
            'remarks' => $visit['remarks'] . ($reason ? " \n[Cancelled: {$reason}]" : ''),
        ]);

        $this->auditModel->record(
            session()->get('user_id'),
            'SITE_VISIT_CANCELLED',
            'SiteVisits',
            $id,
            "Cancelled site visit {$visit['visit_code']}"
        );

        return redirect()->back()->with('success', 'Site visit cancelled.');
    }
}
