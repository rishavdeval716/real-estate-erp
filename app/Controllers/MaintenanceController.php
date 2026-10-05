<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MaintenanceRequestModel;
use App\Models\ComplaintModel;
use App\Models\PreventiveMaintenanceModel;
use App\Models\CamChargeModel;
use App\Models\FacilityAssetModel;
use App\Models\TechnicianModel;
use App\Models\SlaRuleModel;
use App\Models\PropertyModel;
use App\Models\PropertyUnitModel;
use App\Models\TenantModel;
use App\Models\AuditLogModel;
use App\Models\CompanyModel;

class MaintenanceController extends BaseController
{
    protected $requestModel;
    protected $complaintModel;
    protected $preventiveModel;
    protected $camModel;
    protected $assetModel;
    protected $techModel;
    protected $slaModel;
    protected $propertyModel;
    protected $unitModel;
    protected $tenantModel;
    protected $auditModel;
    protected $companyModel;

    public function __construct()
    {
        $this->requestModel    = new MaintenanceRequestModel();
        $this->complaintModel  = new ComplaintModel();
        $this->preventiveModel = new PreventiveMaintenanceModel();
        $this->camModel        = new CamChargeModel();
        $this->assetModel      = new FacilityAssetModel();
        $this->techModel       = new TechnicianModel();
        $this->slaModel        = new SlaRuleModel();
        $this->propertyModel   = new PropertyModel();
        $this->unitModel       = new PropertyUnitModel();
        $this->tenantModel     = new TenantModel();
        $this->auditModel      = new AuditLogModel();
        $this->companyModel    = new CompanyModel();
    }

    // ==========================================
    // 1. WORK ORDERS / MAINTENANCE REQUESTS
    // ==========================================

    public function index()
    {
        $status       = trim($this->request->getGet('status') ?? '');
        $priority     = trim($this->request->getGet('priority') ?? '');
        $propertyId   = (int)$this->request->getGet('property_id');
        $technicianId = (int)$this->request->getGet('technician_id');
        $search       = trim($this->request->getGet('search') ?? '');

        $builder = $this->requestModel->select('maintenance_requests.*, t.full_name as tenant_name, p.title as property_title, u.unit_number, fa.name as asset_name, tech.name as technician_name')
            ->join('tenants t', 't.id = maintenance_requests.tenant_id', 'left')
            ->join('properties p', 'p.id = maintenance_requests.property_id')
            ->join('property_units u', 'u.id = maintenance_requests.property_unit_id', 'left')
            ->join('facility_assets fa', 'fa.id = maintenance_requests.asset_id', 'left')
            ->join('technicians tech', 'tech.id = maintenance_requests.assigned_technician_id', 'left');

        if ($status !== '') {
            $builder->where('maintenance_requests.status', $status);
        }
        if ($priority !== '') {
            $builder->where('maintenance_requests.priority', $priority);
        }
        if ($propertyId > 0) {
            $builder->where('maintenance_requests.property_id', $propertyId);
        }
        if ($technicianId > 0) {
            $builder->where('maintenance_requests.assigned_technician_id', $technicianId);
        }
        if ($search !== '') {
            $builder->groupStart()
                ->like('maintenance_requests.ticket_number', $search)
                ->orLike('maintenance_requests.description', $search)
                ->orLike('t.full_name', $search)
                ->orLike('p.title', $search)
                ->orLike('tech.name', $search)
                ->groupEnd();
        }

        $requests = $builder->orderBy('maintenance_requests.id', 'DESC')->paginate(15);
        $pager    = $this->requestModel->pager;

        $db = \Config\Database::connect();
        $kpi = [
            'total'       => $db->table('maintenance_requests')->countAllResults(),
            'open'        => $db->table('maintenance_requests')->where('status', 'open')->countAllResults(),
            'assigned'    => $db->table('maintenance_requests')->where('status', 'assigned')->countAllResults(),
            'in_progress' => $db->table('maintenance_requests')->where('status', 'in_progress')->countAllResults(),
            'resolved'    => $db->table('maintenance_requests')->whereIn('status', ['resolved', 'closed'])->countAllResults(),
        ];

        $properties  = $this->propertyModel->findAll();
        $technicians = $this->techModel->where('status', 'active')->findAll();

        return view('maintenance/request_index', [
            'title'       => 'Maintenance Work Orders & Tickets - Real Estate ERP',
            'requests'    => $requests,
            'pager'       => $pager,
            'filters'     => ['status' => $status, 'priority' => $priority, 'property_id' => $propertyId, 'technician_id' => $technicianId, 'search' => $search],
            'kpi'         => $kpi,
            'properties'  => $properties,
            'technicians' => $technicians,
        ]);
    }

    public function create()
    {
        $properties  = $this->propertyModel->findAll();
        $units       = $this->unitModel->findAll();
        $tenants     = $this->tenantModel->where('deleted_at', null)->findAll();
        $assets      = $this->assetModel->where('status', 'operational')->findAll();
        $technicians = $this->techModel->where('status', 'active')->findAll();

        return view('maintenance/request_create', [
            'title'       => 'Create Maintenance Ticket - Real Estate ERP',
            'properties'  => $properties,
            'units'       => $units,
            'tenants'     => $tenants,
            'assets'      => $assets,
            'technicians' => $technicians,
        ]);
    }

    public function store()
    {
        $rules = [
            'property_id' => 'required|is_natural_no_zero',
            'category'    => 'required|max_length[100]',
            'priority'    => 'required|in_list[low,medium,high,urgent]',
            'description' => 'required|min_length[5]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $category = $this->request->getPost('category');
        $priority = $this->request->getPost('priority');
        $techId   = $this->request->getPost('assigned_technician_id') ? (int)$this->request->getPost('assigned_technician_id') : null;

        // SLA resolution due date calculation
        $slaHours   = $this->slaModel->getResolutionHours($category, $priority);
        $slaDueDate = date('Y-m-d H:i:s', strtotime("+{$slaHours} hours"));

        $ticketNumber = $this->requestModel->generateTicketNumber();
        $userId       = session()->get('user_id');

        $data = [
            'ticket_number'          => $ticketNumber,
            'tenant_id'              => $this->request->getPost('tenant_id') ?: null,
            'property_id'            => (int)$this->request->getPost('property_id'),
            'property_unit_id'       => $this->request->getPost('property_unit_id') ?: null,
            'asset_id'               => $this->request->getPost('asset_id') ?: null,
            'category'               => $category,
            'subcategory'            => trim($this->request->getPost('subcategory') ?? '') ?: null,
            'priority'               => $priority,
            'description'            => trim($this->request->getPost('description')),
            'created_date'           => date('Y-m-d H:i:s'),
            'assigned_technician_id' => $techId,
            'sla_due_date'           => $slaDueDate,
            'status'                 => $techId ? 'assigned' : 'open',
            'created_by'             => $userId,
        ];

        $requestId = $this->requestModel->insert($data);

        // Update technician availability if assigned
        if ($techId) {
            $this->techModel->update($techId, ['availability' => 'busy']);
        }

        $this->auditModel->record(
            $userId,
            'WORK_ORDER_CREATED',
            'Maintenance',
            $requestId,
            "Created maintenance ticket {$ticketNumber} ({$priority} priority, SLA due {$slaDueDate})"
        );

        return redirect()->to(base_url("maintenance/view/{$requestId}"))->with('success', "Ticket {$ticketNumber} created successfully.");
    }

    public function view(int $id)
    {
        $ticket = $this->requestModel->getRequestWithDetails($id);
        if (!$ticket) {
            return redirect()->to(base_url('maintenance'))->with('error', 'Ticket not found.');
        }

        $technicians = $this->techModel->where('status', 'active')->findAll();

        return view('maintenance/request_view', [
            'title'       => "Work Order {$ticket['ticket_number']}",
            'ticket'      => $ticket,
            'technicians' => $technicians,
        ]);
    }

    public function workOrderVoucher(int $id)
    {
        $ticket = $this->requestModel->getRequestWithDetails($id);
        if (!$ticket) {
            return redirect()->to(base_url('maintenance'))->with('error', 'Work order not found.');
        }

        $company = $this->companyModel->first() ?? [
            'name'    => 'Real Estate ERP Enterprise Ltd.',
            'email'   => 'info@realestate-erp.local',
            'phone'   => '+91 98765 43210',
            'address' => 'Corporate Tower, Financial District',
            'city'    => 'Mumbai',
            'state'   => 'Maharashtra',
        ];

        return view('maintenance/request_voucher', [
            'title'   => "Work Order Sheet - {$ticket['ticket_number']}",
            'ticket'  => $ticket,
            'company' => $company,
        ]);
    }

    public function updateStatus(int $id)
    {
        $ticket = $this->requestModel->find($id);
        if (!$ticket) {
            return redirect()->back()->with('error', 'Ticket not found.');
        }

        $status     = $this->request->getPost('status');
        $resolution = trim($this->request->getPost('resolution') ?? '');
        $userId     = session()->get('user_id');

        $updateData = [
            'status'     => $status,
            'resolution' => $resolution ?: $ticket['resolution'],
        ];

        if (in_array($status, ['resolved', 'closed'])) {
            $updateData['closed_date'] = date('Y-m-d H:i:s');
            // Free up assigned technician
            if (!empty($ticket['assigned_technician_id'])) {
                $this->techModel->update($ticket['assigned_technician_id'], ['availability' => 'available']);
            }
        }

        $this->requestModel->update($id, $updateData);

        $this->auditModel->record(
            $userId,
            'WORK_ORDER_STATUS_CHANGED',
            'Maintenance',
            $id,
            "Updated ticket {$ticket['ticket_number']} status to {$status}"
        );

        return redirect()->back()->with('success', "Ticket status updated to {$status}.");
    }

    public function assignTechnician(int $id)
    {
        $ticket = $this->requestModel->find($id);
        if (!$ticket) {
            return redirect()->back()->with('error', 'Ticket not found.');
        }

        $techId = (int)$this->request->getPost('assigned_technician_id');
        $tech   = $this->techModel->find($techId);
        if (!$tech) {
            return redirect()->back()->with('error', 'Technician not found.');
        }

        $this->requestModel->update($id, [
            'assigned_technician_id' => $techId,
            'status'                 => $ticket['status'] === 'open' ? 'assigned' : $ticket['status'],
        ]);

        $this->techModel->update($techId, ['availability' => 'busy']);

        $this->auditModel->record(
            session()->get('user_id'),
            'WORK_ORDER_ASSIGNED',
            'Maintenance',
            $id,
            "Assigned technician {$tech['name']} to ticket {$ticket['ticket_number']}"
        );

        return redirect()->back()->with('success', "Technician {$tech['name']} assigned to ticket.");
    }

    // ==========================================
    // 2. RESIDENT / TENANT COMPLAINTS
    // ==========================================

    public function complaints()
    {
        $status     = trim($this->request->getGet('status') ?? '');
        $priority   = trim($this->request->getGet('priority') ?? '');
        $propertyId = (int)$this->request->getGet('property_id');
        $search     = trim($this->request->getGet('search') ?? '');

        $builder = $this->complaintModel->select('complaints.*, t.full_name as tenant_name, p.title as property_title, u.unit_number, u_as.name as assignee_name')
            ->join('tenants t', 't.id = complaints.tenant_id', 'left')
            ->join('properties p', 'p.id = complaints.property_id')
            ->join('property_units u', 'u.id = complaints.property_unit_id', 'left')
            ->join('users u_as', 'u_as.id = complaints.assigned_user_id', 'left');

        if ($status !== '') {
            $builder->where('complaints.status', $status);
        }
        if ($priority !== '') {
            $builder->where('complaints.priority', $priority);
        }
        if ($propertyId > 0) {
            $builder->where('complaints.property_id', $propertyId);
        }
        if ($search !== '') {
            $builder->groupStart()
                ->like('complaints.complaint_code', $search)
                ->orLike('complaints.description', $search)
                ->orLike('t.full_name', $search)
                ->orLike('p.title', $search)
                ->groupEnd();
        }

        $complaints = $builder->orderBy('complaints.id', 'DESC')->paginate(15);
        $pager      = $this->complaintModel->pager;

        $db = \Config\Database::connect();
        $kpi = [
            'total'       => $db->table('complaints')->countAllResults(),
            'submitted'   => $db->table('complaints')->where('status', 'submitted')->countAllResults(),
            'in_review'   => $db->table('complaints')->where('status', 'in_review')->countAllResults(),
            'in_progress' => $db->table('complaints')->where('status', 'in_progress')->countAllResults(),
            'resolved'    => $db->table('complaints')->where('status', 'resolved')->countAllResults(),
        ];

        $properties = $this->propertyModel->findAll();
        $units      = $this->unitModel->findAll();
        $tenants    = $this->tenantModel->where('deleted_at', null)->findAll();

        return view('maintenance/complaint_index', [
            'title'      => 'Resident Complaints Management - Real Estate ERP',
            'complaints' => $complaints,
            'pager'      => $pager,
            'filters'    => ['status' => $status, 'priority' => $priority, 'property_id' => $propertyId, 'search' => $search],
            'kpi'        => $kpi,
            'properties' => $properties,
            'units'      => $units,
            'tenants'    => $tenants,
        ]);
    }

    public function storeComplaint()
    {
        $rules = [
            'property_id'    => 'required|is_natural_no_zero',
            'complaint_type' => 'required|max_length[100]',
            'priority'       => 'required|in_list[low,medium,high,urgent]',
            'description'    => 'required|min_length[5]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $code   = $this->complaintModel->generateComplaintCode();
        $userId = session()->get('user_id');

        $data = [
            'complaint_code'   => $code,
            'complaint_type'   => trim($this->request->getPost('complaint_type')),
            'property_id'      => (int)$this->request->getPost('property_id'),
            'property_unit_id' => $this->request->getPost('property_unit_id') ?: null,
            'tenant_id'        => $this->request->getPost('tenant_id') ?: null,
            'description'      => trim($this->request->getPost('description')),
            'priority'         => $this->request->getPost('priority'),
            'assigned_user_id' => $userId,
            'status'           => 'submitted',
        ];

        $complaintId = $this->complaintModel->insert($data);

        $this->auditModel->record(
            $userId,
            'COMPLAINT_CREATED',
            'Complaints',
            $complaintId,
            "Logged complaint {$code} ({$data['complaint_type']})"
        );

        return redirect()->back()->with('success', "Complaint {$code} recorded successfully.");
    }

    public function updateComplaintStatus(int $id)
    {
        $complaint = $this->complaintModel->find($id);
        if (!$complaint) {
            return redirect()->back()->with('error', 'Complaint not found.');
        }

        $status     = $this->request->getPost('status');
        $resolution = trim($this->request->getPost('resolution') ?? '');
        $userId     = session()->get('user_id');

        $this->complaintModel->update($id, [
            'status'     => $status,
            'resolution' => $resolution ?: $complaint['resolution'],
        ]);

        $this->auditModel->record(
            $userId,
            'COMPLAINT_STATUS_UPDATED',
            'Complaints',
            $id,
            "Updated complaint {$complaint['complaint_code']} to {$status}"
        );

        return redirect()->back()->with('success', "Complaint status updated to {$status}.");
    }

    public function submitFeedback(int $id)
    {
        $complaint = $this->complaintModel->find($id);
        if (!$complaint) {
            return redirect()->back()->with('error', 'Complaint not found.');
        }

        $rating   = (int)$this->request->getPost('feedback_rating');
        $comments = trim($this->request->getPost('feedback_comments') ?? '');

        $this->complaintModel->update($id, [
            'feedback_rating'   => min(5, max(1, $rating)),
            'feedback_comments' => $comments ?: null,
        ]);

        return redirect()->back()->with('success', 'Resident feedback submitted successfully.');
    }

    // ==========================================
    // 3. PREVENTIVE MAINTENANCE / AMC SCHEDULES
    // ==========================================

    public function preventive()
    {
        $status  = trim($this->request->getGet('status') ?? '');
        $assetId = (int)$this->request->getGet('asset_id');

        $builder = $this->preventiveModel->select('preventive_maintenance.*, fa.name as asset_name, fa.asset_code, fa.category as asset_category, p.title as property_title, tech.name as technician_name')
            ->join('facility_assets fa', 'fa.id = preventive_maintenance.asset_id')
            ->join('properties p', 'p.id = fa.property_id')
            ->join('technicians tech', 'tech.id = preventive_maintenance.assigned_technician_id', 'left');

        if ($status !== '') {
            $builder->where('preventive_maintenance.status', $status);
        }
        if ($assetId > 0) {
            $builder->where('preventive_maintenance.asset_id', $assetId);
        }

        $schedules = $builder->orderBy('preventive_maintenance.next_service_date', 'ASC')->paginate(15);
        $pager     = $this->preventiveModel->pager;

        $db = \Config\Database::connect();
        $kpi = [
            'total'     => $db->table('preventive_maintenance')->countAllResults(),
            'scheduled' => $db->table('preventive_maintenance')->where('status', 'scheduled')->countAllResults(),
            'completed' => $db->table('preventive_maintenance')->where('status', 'completed')->countAllResults(),
            'overdue'   => $db->table('preventive_maintenance')->where('status', 'overdue')->countAllResults(),
        ];

        $assets      = $this->assetModel->findAll();
        $technicians = $this->techModel->where('status', 'active')->findAll();

        return view('maintenance/preventive_index', [
            'title'       => 'Preventive Maintenance & AMC - Real Estate ERP',
            'schedules'   => $schedules,
            'pager'       => $pager,
            'filters'     => ['status' => $status, 'asset_id' => $assetId],
            'kpi'         => $kpi,
            'assets'      => $assets,
            'technicians' => $technicians,
        ]);
    }

    public function storePreventive()
    {
        $rules = [
            'asset_id'          => 'required|is_natural_no_zero',
            'maintenance_type'  => 'required|max_length[100]',
            'frequency'         => 'required|in_list[daily,weekly,monthly,quarterly,semi_annual,annual]',
            'next_service_date' => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $code   = $this->preventiveModel->generateScheduleCode();
        $userId = session()->get('user_id');

        $data = [
            'schedule_code'          => $code,
            'asset_id'               => (int)$this->request->getPost('asset_id'),
            'maintenance_type'       => trim($this->request->getPost('maintenance_type')),
            'frequency'              => $this->request->getPost('frequency'),
            'next_service_date'      => $this->request->getPost('next_service_date'),
            'assigned_technician_id' => $this->request->getPost('assigned_technician_id') ?: null,
            'status'                 => 'scheduled',
            'remarks'                => trim($this->request->getPost('remarks') ?? '') ?: null,
        ];

        $schedId = $this->preventiveModel->insert($data);

        $this->auditModel->record(
            $userId,
            'PREVENTIVE_SCHEDULED',
            'Maintenance',
            $schedId,
            "Scheduled preventive service {$code} for Asset #{$data['asset_id']}"
        );

        return redirect()->back()->with('success', "Preventive maintenance schedule {$code} created.");
    }

    public function completePreventive(int $id)
    {
        $schedule = $this->preventiveModel->find($id);
        if (!$schedule) {
            return redirect()->back()->with('error', 'Schedule not found.');
        }

        $today = date('Y-m-d');
        // Recalculate next service date based on frequency
        $nextDate = match ($schedule['frequency']) {
            'daily'       => date('Y-m-d', strtotime('+1 day')),
            'weekly'      => date('Y-m-d', strtotime('+1 week')),
            'monthly'     => date('Y-m-d', strtotime('+1 month')),
            'quarterly'   => date('Y-m-d', strtotime('+3 months')),
            'semi_annual' => date('Y-m-d', strtotime('+6 months')),
            'annual'      => date('Y-m-d', strtotime('+1 year')),
            default       => date('Y-m-d', strtotime('+1 month')),
        };

        $this->preventiveModel->update($id, [
            'last_service_date' => $today,
            'next_service_date' => $nextDate,
            'status'            => 'completed',
            'remarks'           => 'Service completed on ' . $today . '. Next scheduled for ' . $nextDate,
        ]);

        $this->auditModel->record(
            session()->get('user_id'),
            'PREVENTIVE_COMPLETED',
            'Maintenance',
            $id,
            "Completed preventive service {$schedule['schedule_code']}, next date {$nextDate}"
        );

        return redirect()->back()->with('success', "Preventive service recorded. Next service auto-scheduled for {$nextDate}.");
    }

    // ==========================================
    // 4. COMMON AREA MAINTENANCE (CAM) CHARGES
    // ==========================================

    public function cam()
    {
        $status     = trim($this->request->getGet('status') ?? '');
        $propertyId = (int)$this->request->getGet('property_id');

        $builder = $this->camModel->select('cam_charges.*, p.title as property_title, u.unit_number, t.full_name as tenant_name')
            ->join('properties p', 'p.id = cam_charges.property_id')
            ->join('property_units u', 'u.id = cam_charges.property_unit_id', 'left')
            ->join('tenants t', 't.id = cam_charges.tenant_id', 'left');

        if ($status !== '') {
            $builder->where('cam_charges.status', $status);
        }
        if ($propertyId > 0) {
            $builder->where('cam_charges.property_id', $propertyId);
        }

        $charges = $builder->orderBy('cam_charges.id', 'DESC')->paginate(15);
        $pager   = $this->camModel->pager;

        $db = \Config\Database::connect();
        $totalBilled = $db->table('cam_charges')->selectSum('total')->get()->getRow()->total ?? 0;
        $totalPaid   = $db->table('cam_charges')->where('status', 'paid')->selectSum('total')->get()->getRow()->total ?? 0;

        $kpi = [
            'total_billed' => (float)$totalBilled,
            'total_paid'   => (float)$totalPaid,
            'total_count'  => $db->table('cam_charges')->countAllResults(),
            'unbilled'     => $db->table('cam_charges')->where('status', 'unbilled')->countAllResults(),
        ];

        $properties = $this->propertyModel->findAll();
        $units      = $this->unitModel->findAll();
        $tenants    = $this->tenantModel->where('deleted_at', null)->findAll();

        return view('maintenance/cam_index', [
            'title'      => 'Common Area Maintenance (CAM) Billing - Real Estate ERP',
            'charges'    => $charges,
            'pager'      => $pager,
            'filters'    => ['status' => $status, 'property_id' => $propertyId],
            'kpi'        => $kpi,
            'properties' => $properties,
            'units'      => $units,
            'tenants'    => $tenants,
        ]);
    }

    public function storeCam()
    {
        $rules = [
            'property_id'   => 'required|is_natural_no_zero',
            'area_sqft'     => 'required|numeric|greater_than[0]',
            'billing_model' => 'required|in_list[per_sqft,flat_rate]',
            'rate'          => 'required|numeric|greater_than[0]',
            'period'        => 'required|max_length[20]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $area    = (float)$this->request->getPost('area_sqft');
        $rate    = (float)$this->request->getPost('rate');
        $model   = $this->request->getPost('billing_model');
        $amount  = $model === 'per_sqft' ? round($area * $rate, 2) : $rate;
        $tax     = round($amount * 0.18, 2); // 18% GST
        $total   = $amount + $tax;

        $code   = $this->camModel->generateCamCode();
        $userId = session()->get('user_id');

        $data = [
            'cam_code'         => $code,
            'property_id'      => (int)$this->request->getPost('property_id'),
            'property_unit_id' => $this->request->getPost('property_unit_id') ?: null,
            'tenant_id'        => $this->request->getPost('tenant_id') ?: null,
            'area_sqft'        => $area,
            'billing_model'    => $model,
            'rate'             => $rate,
            'period'           => trim($this->request->getPost('period')),
            'amount'           => $amount,
            'tax'              => $tax,
            'total'            => $total,
            'status'           => 'unbilled',
        ];

        $camId = $this->camModel->insert($data);

        $this->auditModel->record(
            $userId,
            'CAM_CHARGED',
            'Maintenance',
            $camId,
            "Created CAM charge {$code} for period {$data['period']} - ₹{$total}"
        );

        return redirect()->back()->with('success', "CAM charge {$code} generated successfully.");
    }

    public function updateCamStatus(int $id)
    {
        $cam = $this->camModel->find($id);
        if (!$cam) {
            return redirect()->back()->with('error', 'CAM charge not found.');
        }

        $status = $this->request->getPost('status');
        $this->camModel->update($id, ['status' => $status]);

        return redirect()->back()->with('success', "CAM charge {$cam['cam_code']} marked as {$status}.");
    }
}
