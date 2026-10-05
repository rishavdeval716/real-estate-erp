<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LeadModel;
use App\Models\ProspectModel;
use App\Models\LeadSourceModel;
use App\Models\LeadPropertyInterestModel;
use App\Models\LeadAssignmentModel;
use App\Models\LeadFollowupModel;
use App\Models\SiteVisitModel;
use App\Models\UnitHoldModel;
use App\Models\EnquiryModel;
use App\Models\ProjectModel;
use App\Models\PropertyModel;
use App\Models\PropertyUnitModel;
use App\Models\PropertyTypeModel;
use App\Models\UserModel;
use App\Models\AuditLogModel;
use App\Libraries\LeadStatus;
use App\Libraries\LeadAssignmentService;

class LeadController extends BaseController
{
    protected $leadModel;
    protected $prospectModel;
    protected $leadSourceModel;
    protected $interestModel;
    protected $assignmentModel;
    protected $followupModel;
    protected $visitModel;
    protected $holdModel;
    protected $enquiryModel;
    protected $auditModel;

    public function __construct()
    {
        $this->leadModel       = new LeadModel();
        $this->prospectModel   = new ProspectModel();
        $this->leadSourceModel = new LeadSourceModel();
        $this->interestModel   = new LeadPropertyInterestModel();
        $this->assignmentModel = new LeadAssignmentModel();
        $this->followupModel   = new LeadFollowupModel();
        $this->visitModel      = new SiteVisitModel();
        $this->holdModel       = new UnitHoldModel();
        $this->enquiryModel    = new EnquiryModel();
        $this->auditModel      = new AuditLogModel();
    }

    /**
     * Leads directory with search, multi-field filtering, and dynamic KPI counters
     */
    public function index()
    {
        $filters = [
            'search'           => trim($this->request->getGet('search') ?? ''),
            'lead_status'      => trim($this->request->getGet('lead_status') ?? ''),
            'lead_stage'       => trim($this->request->getGet('lead_stage') ?? ''),
            'priority'         => trim($this->request->getGet('priority') ?? ''),
            'lead_source_id'   => $this->request->getGet('lead_source_id'),
            'assigned_user_id' => $this->request->getGet('assigned_user_id'),
            'project_id'       => $this->request->getGet('project_id'),
            'property_type_id' => $this->request->getGet('property_type_id'),
            'min_budget'       => $this->request->getGet('min_budget'),
            'max_budget'       => $this->request->getGet('max_budget'),
            'sort'             => $this->request->getGet('sort') ?? 'leads.created_at',
            'order'            => $this->request->getGet('order') ?? 'DESC',
        ];

        $dataLeads = $this->leadModel->getLeadsWithDetails($filters, 15);

        // KPI Counts
        $db = \Config\Database::connect();
        $kpi = [
            'total'     => $db->table('leads')->where('deleted_at', null)->countAllResults(),
            'new'       => $db->table('leads')->where('lead_status', 'New')->where('deleted_at', null)->countAllResults(),
            'contacted' => $db->table('leads')->where('lead_status', 'Contacted')->where('deleted_at', null)->countAllResults(),
            'qualified' => $db->table('leads')->where('lead_status', 'Qualified')->where('deleted_at', null)->countAllResults(),
            'won'       => $db->table('leads')->where('lead_stage', 'Won')->where('deleted_at', null)->countAllResults(),
            'lost'      => $db->table('leads')->where('lead_status', 'Lost')->where('deleted_at', null)->countAllResults(),
        ];

        // Dropdown reference data
        $sources    = $this->leadSourceModel->getActiveSources();
        $executives = LeadAssignmentService::getEligibleExecutives();
        $projects   = (new ProjectModel())->where('deleted_at', null)->orderBy('name', 'ASC')->findAll();
        $propTypes  = (new PropertyTypeModel())->where('deleted_at', null)->where('status', 'active')->orderBy('name', 'ASC')->findAll();

        $data = [
            'title'      => 'Lead Management - Real Estate CRM',
            'leads'      => $dataLeads['leads'],
            'pager'      => $dataLeads['pager'],
            'filters'    => $filters,
            'kpi'        => $kpi,
            'sources'    => $sources,
            'executives' => $executives,
            'projects'   => $projects,
            'propTypes'  => $propTypes,
            'statuses'   => LeadStatus::getStatuses(),
            'stages'     => LeadStatus::getStages(),
            'priorities' => LeadStatus::getPriorities(),
        ];

        return view('leads/index', $data);
    }

    /**
     * Show lead creation form
     */
    public function create()
    {
        $sources    = $this->leadSourceModel->getActiveSources();
        $executives = LeadAssignmentService::getEligibleExecutives();
        $projects   = (new ProjectModel())->where('deleted_at', null)->orderBy('name', 'ASC')->findAll();
        $properties = (new PropertyModel())->where('deleted_at', null)->orderBy('title', 'ASC')->findAll();
        $propTypes  = (new PropertyTypeModel())->where('deleted_at', null)->where('status', 'active')->orderBy('name', 'ASC')->findAll();
        $units      = (new PropertyUnitModel())->where('deleted_at', null)->where('availability_status', 'Available')->orderBy('unit_number', 'ASC')->findAll();

        $data = [
            'title'      => 'Add New Lead - Real Estate CRM',
            'sources'    => $sources,
            'executives' => $executives,
            'projects'   => $projects,
            'properties' => $properties,
            'propTypes'  => $propTypes,
            'units'      => $units,
            'statuses'   => LeadStatus::getStatuses(),
            'stages'     => LeadStatus::getStages(),
            'priorities' => LeadStatus::getPriorities(),
        ];

        return view('leads/create', $data);
    }

    /**
     * Store new lead and initialize prospect, assignment, and interest records
     */
    public function store()
    {
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[100]',
            'phone'      => 'required|min_length[8]|max_length[30]',
            'email'      => 'permit_empty|valid_email|max_length[150]',
            'priority'   => 'required|in_list[Low,Medium,High,Urgent]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $leadCode = $this->leadModel->generateLeadCode();
        $assignOption = $this->request->getPost('assign_option') ?? 'manual';
        $assignedToId = (int)$this->request->getPost('assigned_user_id');

        $leadData = [
            'lead_code'           => $leadCode,
            'first_name'          => trim($this->request->getPost('first_name')),
            'last_name'           => trim($this->request->getPost('last_name') ?? ''),
            'email'               => trim($this->request->getPost('email') ?? ''),
            'phone'               => trim($this->request->getPost('phone')),
            'alternate_phone'     => trim($this->request->getPost('alternate_phone') ?? ''),
            'lead_source_id'      => $this->request->getPost('lead_source_id') ?: null,
            'assigned_user_id'    => null, // Will be set after assignment logic
            'branch_id'           => session()->get('branch_id') ?: 1,
            'lead_status'         => $this->request->getPost('lead_status') ?: 'New',
            'lead_stage'          => $this->request->getPost('lead_stage') ?: 'New',
            'priority'            => $this->request->getPost('priority') ?: 'Medium',
            'budget_min'          => $this->request->getPost('budget_min') ? (float)$this->request->getPost('budget_min') : null,
            'budget_max'          => $this->request->getPost('budget_max') ? (float)$this->request->getPost('budget_max') : null,
            'preferred_location'  => trim($this->request->getPost('preferred_location') ?? ''),
            'property_type_id'    => $this->request->getPost('property_type_id') ?: null,
            'project_id'          => $this->request->getPost('project_id') ?: null,
            'property_id'         => $this->request->getPost('property_id') ?: null,
            'property_unit_id'    => $this->request->getPost('property_unit_id') ?: null,
            'purchase_purpose'    => trim($this->request->getPost('purchase_purpose') ?? ''),
            'purchase_timeline'   => trim($this->request->getPost('purchase_timeline') ?? ''),
            'financing_required'  => $this->request->getPost('financing_required') ?: 'Undecided',
            'site_visit_required' => $this->request->getPost('site_visit_required') ?: 'No',
            'remarks'             => trim($this->request->getPost('remarks') ?? ''),
        ];

        $leadId = $this->leadModel->insert($leadData);

        // Handle Assignment
        $currentUserId = session()->get('user_id');
        if ($assignOption === 'round_robin') {
            LeadAssignmentService::assignRoundRobin($leadId, $currentUserId, 'Initial Round-Robin automatic assignment');
        } elseif (!empty($assignedToId)) {
            LeadAssignmentService::assignLead($leadId, $assignedToId, $currentUserId, 'Direct manual assignment', false);
        }

        // Create Prospect profile
        $this->prospectModel->insert([
            'lead_id'                  => $leadId,
            'name'                     => trim($leadData['first_name'] . ' ' . $leadData['last_name']),
            'email'                    => $leadData['email'],
            'phone'                    => $leadData['phone'],
            'alternate_phone'          => $leadData['alternate_phone'],
            'address'                  => trim($this->request->getPost('address') ?? ''),
            'city'                     => trim($this->request->getPost('city') ?? ''),
            'state'                    => trim($this->request->getPost('state') ?? ''),
            'pincode'                  => trim($this->request->getPost('pincode') ?? ''),
            'occupation'               => trim($this->request->getPost('occupation') ?? ''),
            'preferred_contact_method' => $this->request->getPost('preferred_contact_method') ?: 'Phone',
            'notes'                    => 'Profile auto-initialized upon lead creation.',
        ]);

        // Record Initial Property Interest if specified
        if (!empty($leadData['property_id']) || !empty($leadData['project_id'])) {
            $this->interestModel->insert([
                'lead_id'          => $leadId,
                'project_id'       => $leadData['project_id'],
                'property_id'      => $leadData['property_id'],
                'property_unit_id' => $leadData['property_unit_id'],
                'interest_level'   => 'Primary',
                'remarks'          => 'Shortlisted at initial intake',
            ]);
        }

        $this->auditModel->record(
            $currentUserId,
            'LEAD_CREATED',
            'Leads',
            $leadId,
            "Created lead {$leadCode} ({$leadData['first_name']} {$leadData['last_name']})"
        );

        return redirect()->to("/leads/view/{$leadId}")->with('success', "Lead {$leadCode} created successfully.");
    }

    /**
     * View comprehensive lead details with unified timeline and submodules
     */
    public function view($id)
    {
        $lead = $this->leadModel->getLeadWithFullDetails($id);
        if (!$lead) {
            return redirect()->to('/leads')->with('error', 'Lead not found.');
        }

        $prospect    = $this->prospectModel->getByLeadId($id);
        $interests   = $this->interestModel->getInterestsForLead($id);
        $enquiries   = $this->enquiryModel->getEnquiriesWithDetails($id);
        $followups   = $this->followupModel->getFollowupsForLead($id);
        $visits      = $this->visitModel->getVisitsForLead($id);
        $holds       = $this->holdModel->getHoldsForLead($id);
        $assignments = $this->assignmentModel->getHistoryForLead($id);

        // Fetch eligible executives, projects, properties, and available units for quick actions
        $executives  = LeadAssignmentService::getEligibleExecutives();
        $projects    = (new ProjectModel())->where('deleted_at', null)->orderBy('name', 'ASC')->findAll();
        $properties  = (new PropertyModel())->where('deleted_at', null)->orderBy('title', 'ASC')->findAll();
        $units       = (new PropertyUnitModel())->where('deleted_at', null)->where('availability_status', 'Available')->orderBy('unit_number', 'ASC')->findAll();

        // Build Unified Activity Timeline
        $timeline = [];

        // 1. Lead creation
        $timeline[] = [
            'datetime'    => $lead['created_at'],
            'type'        => 'LEAD_CREATED',
            'title'       => 'Lead Ingested',
            'description' => "Lead {$lead['lead_code']} entered CRM via {$lead['source_name']}",
            'user'        => 'System / Intake',
            'badge'       => 'primary',
        ];

        // 2. Assignments
        foreach ($assignments as $as) {
            $timeline[] = [
                'datetime'    => $as['created_at'],
                'type'        => 'LEAD_ASSIGNED',
                'title'       => $as['assignment_type'] . ' Assignment',
                'description' => "Assigned to {$as['assigned_to_name']}" . ($as['remarks'] ? " — {$as['remarks']}" : ''),
                'user'        => $as['assigned_by_name'] ?: 'System',
                'badge'       => 'info',
            ];
        }

        // 3. Enquiries
        foreach ($enquiries as $enq) {
            $timeline[] = [
                'datetime'    => $enq['created_at'],
                'type'        => 'ENQUIRY_CREATED',
                'title'       => "Enquiry {$enq['enquiry_code']}",
                'description' => "Logged {$enq['enquiry_type']} enquiry (" . ($enq['property_title'] ?: ($enq['project_name'] ?: 'General')) . ")",
                'user'        => $enq['creator_name'] ?: 'Sales Agent',
                'badge'       => 'secondary',
            ];
        }

        // 4. Follow-ups
        foreach ($followups as $fu) {
            $timeline[] = [
                'datetime'    => $fu['completed_at'] ?: $fu['scheduled_at'],
                'type'        => 'FOLLOWUP',
                'title'       => "Follow-up ({$fu['followup_type']}) — " . $fu['status'],
                'description' => $fu['notes'] ? $fu['notes'] . ($fu['outcome'] ? " [Outcome: {$fu['outcome']}]" : '') : "Scheduled for {$fu['scheduled_at']}",
                'user'        => $fu['assigned_to_name'],
                'badge'       => ($fu['status'] === 'Completed' ? 'success' : ($fu['status'] === 'Cancelled' ? 'danger' : 'warning')),
            ];
        }

        // 5. Site Visits
        foreach ($visits as $sv) {
            $timeline[] = [
                'datetime'    => $sv['scheduled_at'],
                'type'        => 'SITE_VISIT',
                'title'       => "Site Visit {$sv['visit_code']} ({$sv['status']})",
                'description' => "Visit to " . ($sv['property_title'] ?: $sv['project_name']) . ($sv['feedback'] ? " — Feedback: {$sv['feedback']}" : ''),
                'user'        => $sv['assigned_agent_name'] ?: 'Agent',
                'badge'       => ($sv['status'] === 'Completed' ? 'success' : 'info'),
            ];
        }

        // 6. Unit Holds
        foreach ($holds as $h) {
            $timeline[] = [
                'datetime'    => $h['started_at'],
                'type'        => 'UNIT_HOLD',
                'title'       => "Unit Hold {$h['hold_code']} ({$h['hold_status']})",
                'description' => "Temporary hold on Unit {$h['unit_number']} (Expires: {$h['expires_at']})",
                'user'        => $h['held_by_name'] ?: 'Manager',
                'badge'       => ($h['hold_status'] === 'Active' ? 'warning' : 'secondary'),
            ];
        }

        // Sort timeline chronological DESC
        usort($timeline, function ($a, $b) {
            return strtotime($b['datetime']) <=> strtotime($a['datetime']);
        });

        $data = [
            'title'       => "Lead Details: {$lead['lead_code']} - Real Estate CRM",
            'lead'        => $lead,
            'prospect'    => $prospect,
            'interests'   => $interests,
            'enquiries'   => $enquiries,
            'followups'   => $followups,
            'visits'      => $visits,
            'holds'       => $holds,
            'assignments' => $assignments,
            'timeline'    => $timeline,
            'executives'  => $executives,
            'projects'    => $projects,
            'properties'  => $properties,
            'units'       => $units,
            'stages'      => LeadStatus::getStages(),
            'statuses'    => LeadStatus::getStatuses(),
            'priorities'  => LeadStatus::getPriorities(),
        ];

        return view('leads/view', $data);
    }

    /**
     * Edit form
     */
    public function edit($id)
    {
        $lead = $this->leadModel->find($id);
        if (!$lead) {
            return redirect()->to('/leads')->with('error', 'Lead not found.');
        }

        $prospect   = $this->prospectModel->getByLeadId($id);
        $sources    = $this->leadSourceModel->getActiveSources();
        $executives = LeadAssignmentService::getEligibleExecutives();
        $projects   = (new ProjectModel())->where('deleted_at', null)->orderBy('name', 'ASC')->findAll();
        $properties = (new PropertyModel())->where('deleted_at', null)->orderBy('title', 'ASC')->findAll();
        $propTypes  = (new PropertyTypeModel())->where('deleted_at', null)->where('status', 'active')->orderBy('name', 'ASC')->findAll();

        $data = [
            'title'      => "Edit Lead {$lead['lead_code']} - Real Estate CRM",
            'lead'       => $lead,
            'prospect'   => $prospect,
            'sources'    => $sources,
            'executives' => $executives,
            'projects'   => $projects,
            'properties' => $properties,
            'propTypes'  => $propTypes,
            'statuses'   => LeadStatus::getStatuses(),
            'stages'     => LeadStatus::getStages(),
            'priorities' => LeadStatus::getPriorities(),
        ];

        return view('leads/edit', $data);
    }

    /**
     * Update lead
     */
    public function update($id)
    {
        $lead = $this->leadModel->find($id);
        if (!$lead) {
            return redirect()->to('/leads')->with('error', 'Lead not found.');
        }

        $rules = [
            'first_name' => 'required|min_length[2]|max_length[100]',
            'phone'      => 'required|min_length[8]|max_length[30]',
            'email'      => 'permit_empty|valid_email|max_length[150]',
            'priority'   => 'required|in_list[Low,Medium,High,Urgent]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'first_name'          => trim($this->request->getPost('first_name')),
            'last_name'           => trim($this->request->getPost('last_name') ?? ''),
            'email'               => trim($this->request->getPost('email') ?? ''),
            'phone'               => trim($this->request->getPost('phone')),
            'alternate_phone'     => trim($this->request->getPost('alternate_phone') ?? ''),
            'lead_source_id'      => $this->request->getPost('lead_source_id') ?: null,
            'lead_status'         => $this->request->getPost('lead_status') ?: $lead['lead_status'],
            'lead_stage'          => $this->request->getPost('lead_stage') ?: $lead['lead_stage'],
            'priority'            => $this->request->getPost('priority') ?: $lead['priority'],
            'budget_min'          => $this->request->getPost('budget_min') ? (float)$this->request->getPost('budget_min') : null,
            'budget_max'          => $this->request->getPost('budget_max') ? (float)$this->request->getPost('budget_max') : null,
            'preferred_location'  => trim($this->request->getPost('preferred_location') ?? ''),
            'property_type_id'    => $this->request->getPost('property_type_id') ?: null,
            'project_id'          => $this->request->getPost('project_id') ?: null,
            'property_id'         => $this->request->getPost('property_id') ?: null,
            'purchase_purpose'    => trim($this->request->getPost('purchase_purpose') ?? ''),
            'purchase_timeline'   => trim($this->request->getPost('purchase_timeline') ?? ''),
            'financing_required'  => $this->request->getPost('financing_required') ?: 'Undecided',
            'site_visit_required' => $this->request->getPost('site_visit_required') ?: 'No',
            'remarks'             => trim($this->request->getPost('remarks') ?? ''),
        ];

        $this->leadModel->update($id, $updateData);

        // Update prospect profile
        $prospect = $this->prospectModel->getByLeadId($id);
        if ($prospect) {
            $this->prospectModel->update($prospect['id'], [
                'name'                     => trim($updateData['first_name'] . ' ' . $updateData['last_name']),
                'email'                    => $updateData['email'],
                'phone'                    => $updateData['phone'],
                'alternate_phone'          => $updateData['alternate_phone'],
                'address'                  => trim($this->request->getPost('address') ?? $prospect['address']),
                'city'                     => trim($this->request->getPost('city') ?? $prospect['city']),
                'state'                    => trim($this->request->getPost('state') ?? $prospect['state']),
                'pincode'                  => trim($this->request->getPost('pincode') ?? $prospect['pincode']),
                'occupation'               => trim($this->request->getPost('occupation') ?? $prospect['occupation']),
                'preferred_contact_method' => $this->request->getPost('preferred_contact_method') ?: $prospect['preferred_contact_method'],
            ]);
        }

        $this->auditModel->record(
            session()->get('user_id'),
            'LEAD_UPDATED',
            'Leads',
            $id,
            "Updated lead {$lead['lead_code']}"
        );

        return redirect()->to("/leads/view/{$id}")->with('success', "Lead {$lead['lead_code']} updated successfully.");
    }

    /**
     * Assign or reassign lead
     */
    public function assign($id)
    {
        $lead = $this->leadModel->find($id);
        if (!$lead) {
            return redirect()->to('/leads')->with('error', 'Lead not found.');
        }

        $assignedToId = (int)$this->request->getPost('assigned_user_id');
        $remarks      = trim($this->request->getPost('remarks') ?? '');
        $currentUserId = session()->get('user_id');

        if (empty($assignedToId)) {
            return redirect()->back()->with('error', 'Please select an executive.');
        }

        $isReassignment = !empty($lead['assigned_user_id']);
        LeadAssignmentService::assignLead($id, $assignedToId, $currentUserId, $remarks, $isReassignment);

        return redirect()->to("/leads/view/{$id}")->with('success', 'Lead assigned successfully.');
    }

    /**
     * Change lead pipeline stage
     */
    public function changeStage($id)
    {
        $lead = $this->leadModel->find($id);
        if (!$lead) {
            return redirect()->to('/leads')->with('error', 'Lead not found.');
        }

        $newStage = $this->request->getPost('lead_stage');
        $remarks  = trim($this->request->getPost('remarks') ?? '');

        if (!in_array($newStage, LeadStatus::getStages(), true)) {
            return redirect()->back()->with('error', 'Invalid pipeline stage.');
        }

        $update = ['lead_stage' => $newStage];
        if ($newStage === 'Won') {
            $update['lead_status'] = 'Converted';
        } elseif ($newStage === 'Lost') {
            $update['lead_status'] = 'Lost';
        } elseif (in_array($newStage, ['Qualified', 'Site Visit Scheduled', 'Site Visit Completed', 'Negotiation', 'Token Pending', 'Ready for Booking'])) {
            $update['lead_status'] = 'Qualified';
        }

        $this->leadModel->update($id, $update);

        $this->auditModel->record(
            session()->get('user_id'),
            'LEAD_STAGE_CHANGED',
            'Leads',
            $id,
            "Lead {$lead['lead_code']} stage transitioned from {$lead['lead_stage']} to {$newStage}" . ($remarks ? " [Remarks: {$remarks}]" : '')
        );

        return redirect()->to("/leads/view/{$id}")->with('success', "Pipeline stage updated to '{$newStage}'.");
    }

    /**
     * Add property interest for lead
     */
    public function addInterest($id)
    {
        $lead = $this->leadModel->find($id);
        if (!$lead) {
            return redirect()->to('/leads')->with('error', 'Lead not found.');
        }

        $projectId  = $this->request->getPost('project_id') ?: null;
        $propertyId = $this->request->getPost('property_id') ?: null;
        $unitId     = $this->request->getPost('property_unit_id') ?: null;
        $level      = $this->request->getPost('interest_level') ?: 'Interested';
        $remarks    = trim($this->request->getPost('remarks') ?? '');

        if (empty($projectId) && empty($propertyId) && empty($unitId)) {
            return redirect()->back()->with('error', 'Please select at least a project, property, or unit.');
        }

        $this->interestModel->insert([
            'lead_id'          => $id,
            'project_id'       => $projectId,
            'property_id'      => $propertyId,
            'property_unit_id' => $unitId,
            'interest_level'   => $level,
            'remarks'          => $remarks,
        ]);

        $this->auditModel->record(
            session()->get('user_id'),
            'PROPERTY_INTEREST_ADDED',
            'Leads',
            $id,
            "Added {$level} property interest to lead {$lead['lead_code']}"
        );

        return redirect()->to("/leads/view/{$id}")->with('success', 'Property interest recorded successfully.');
    }

    /**
     * Soft delete lead
     */
    public function delete($id)
    {
        $lead = $this->leadModel->where('deleted_at', null)->find($id);
        if (!$lead) {
            return redirect()->to('/leads')->with('error', 'Lead not found.');
        }

        $this->leadModel->delete($id);

        $this->auditModel->record(
            session()->get('user_id'),
            'LEAD_DELETED',
            'Leads',
            $id,
            "Soft deleted lead {$lead['lead_code']}"
        );

        return redirect()->to('/leads')->with('success', "Lead {$lead['lead_code']} deleted successfully.");
    }

    /**
     * Qualify lead with structured criteria
     */
    public function qualify($id)
    {
        $lead = $this->leadModel->find($id);
        if (!$lead) {
            return redirect()->to('/leads')->with('error', 'Lead not found.');
        }

        $result    = $this->request->getPost('qualification_result') ?: 'Qualified';
        $budgetMin = $this->request->getPost('budget_min') ? (float)$this->request->getPost('budget_min') : $lead['budget_min'];
        $budgetMax = $this->request->getPost('budget_max') ? (float)$this->request->getPost('budget_max') : $lead['budget_max'];
        $prefLoc   = trim($this->request->getPost('preferred_location') ?? $lead['preferred_location']);
        $purpose   = trim($this->request->getPost('purchase_purpose') ?? $lead['purchase_purpose']);
        $timeline  = trim($this->request->getPost('expected_purchase_timeline') ?? ($this->request->getPost('purchase_timeline') ?? $lead['purchase_timeline']));
        $financing = $this->request->getPost('financing_required') ?: ($this->request->getPost('financing_requirement') ?: 'Undecided');
        $siteVisit = $this->request->getPost('site_visit_required') ?: ($this->request->getPost('site_visit_requirement') ?: 'No');
        $remarks   = trim($this->request->getPost('qualification_remarks') ?? ($this->request->getPost('remarks') ?? ''));

        $update = [
            'budget_min'          => $budgetMin,
            'budget_max'          => $budgetMax,
            'preferred_location'  => $prefLoc,
            'purchase_purpose'    => $purpose,
            'purchase_timeline'   => $timeline,
            'financing_required'  => in_array($financing, ['Yes', 'No', 'Undecided'], true) ? $financing : 'Undecided',
            'site_visit_required' => in_array($siteVisit, ['Yes', 'No', 'Scheduled', 'Completed'], true) ? $siteVisit : (str_contains(strtolower($siteVisit), 'sched') ? 'Scheduled' : 'No'),
        ];

        if ($this->request->getPost('property_type_id')) {
            $update['property_type_id'] = $this->request->getPost('property_type_id');
        }
        if ($this->request->getPost('project_id')) {
            $update['project_id'] = $this->request->getPost('project_id');
        }
        if ($this->request->getPost('property_id')) {
            $update['property_id'] = $this->request->getPost('property_id');
        }

        if ($result === 'Qualified') {
            $update['lead_status'] = 'Qualified';
            if ($lead['lead_stage'] === 'New' || $lead['lead_stage'] === 'Contacted') {
                $update['lead_stage'] = 'Qualified';
            }
        } elseif ($result === 'Unqualified') {
            $update['lead_status'] = 'Unqualified';
            $update['lead_stage']  = 'Lost';
        } elseif ($result === 'Needs Follow-up') {
            $update['lead_status'] = 'Contacted';
            $update['lead_stage']  = 'Contacted';
        }

        if ($remarks) {
            $update['remarks'] = ($lead['remarks'] ? $lead['remarks'] . "\n" : '') . "[Qualification Note: {$remarks}]";
        }

        $this->leadModel->update($id, $update);

        $this->auditModel->record(
            session()->get('user_id'),
            'LEAD_QUALIFIED',
            'Leads',
            $id,
            "Lead {$lead['lead_code']} evaluated as '{$result}'. Budget: ₹" . number_format($budgetMin) . " - ₹" . number_format($budgetMax)
        );

        return redirect()->to("/leads/view/{$id}")->with('success', "Lead {$lead['lead_code']} qualification updated ({$result}).");
    }

    /**
     * Round-robin automatic executive distribution
     */
    public function assignRoundRobin($id)
    {
        $lead = $this->leadModel->find($id);
        if (!$lead) {
            return redirect()->to('/leads')->with('error', 'Lead not found.');
        }

        $assignedUserId = LeadAssignmentService::assignRoundRobin($id, session()->get('user_id'), 'Auto round-robin distribution');

        if ($assignedUserId) {
            return redirect()->to("/leads/view/{$id}")->with('success', 'Lead assigned via round-robin distribution.');
        }

        return redirect()->to("/leads/view/{$id}")->with('error', 'No eligible sales executives available for assignment.');
    }
}

