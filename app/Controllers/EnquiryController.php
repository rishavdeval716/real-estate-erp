<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EnquiryModel;
use App\Models\LeadModel;
use App\Models\ProjectModel;
use App\Models\PropertyModel;
use App\Models\PropertyUnitModel;
use App\Models\AuditLogModel;

class EnquiryController extends BaseController
{
    protected $enquiryModel;
    protected $leadModel;
    protected $auditModel;

    public function __construct()
    {
        $this->enquiryModel = new EnquiryModel();
        $this->leadModel    = new LeadModel();
        $this->auditModel   = new AuditLogModel();
    }

    /**
     * List all enquiries
     */
    public function index()
    {
        $search = trim($this->request->getGet('search') ?? '');
        $status = trim($this->request->getGet('status') ?? '');
        $type   = trim($this->request->getGet('type') ?? '');

        $builder = $this->enquiryModel->select('enquiries.*, 
            leads.lead_code, 
            leads.first_name, 
            leads.last_name, 
            leads.phone as lead_phone,
            projects.name as project_name, 
            properties.title as property_title, 
            property_units.unit_number')
            ->join('leads', 'leads.id = enquiries.lead_id', 'left')
            ->join('projects', 'projects.id = enquiries.project_id', 'left')
            ->join('properties', 'properties.id = enquiries.property_id', 'left')
            ->join('property_units', 'property_units.id = enquiries.property_unit_id', 'left');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('enquiries.enquiry_code', $search)
                ->orLike('leads.lead_code', $search)
                ->orLike('leads.first_name', $search)
                ->orLike('leads.phone', $search)
                ->orLike('enquiries.requirement', $search)
                ->groupEnd();
        }

        if (!empty($status)) {
            $builder->where('enquiries.status', $status);
        }

        if (!empty($type)) {
            $builder->where('enquiries.enquiry_type', $type);
        }

        $enquiries = $builder->orderBy('enquiries.created_at', 'DESC')->paginate(15);

        $data = [
            'title'     => 'Property Enquiries - Real Estate CRM',
            'enquiries' => $enquiries,
            'pager'     => $this->enquiryModel->pager,
            'search'    => $search,
            'status'    => $status,
            'type'      => $type,
            'types'     => ['Purchase', 'Investment', 'Rent', 'Commercial', 'Plot/Land', 'Other'],
            'statuses'  => ['Open', 'In Progress', 'Qualified', 'Closed', 'Cancelled'],
        ];

        return view('enquiries/index', $data);
    }

    /**
     * Store new enquiry
     */
    public function store()
    {
        $leadId = (int)$this->request->getPost('lead_id');
        $lead   = $this->leadModel->find($leadId);
        if (!$lead) {
            return redirect()->back()->with('error', 'Valid lead is required.');
        }

        $code = $this->enquiryModel->generateEnquiryCode();
        $type = $this->request->getPost('enquiry_type') ?: 'Purchase';

        $enqId = $this->enquiryModel->insert([
            'enquiry_code'            => $code,
            'lead_id'                 => $leadId,
            'project_id'              => $this->request->getPost('project_id') ?: null,
            'property_id'             => $this->request->getPost('property_id') ?: null,
            'property_unit_id'        => $this->request->getPost('property_unit_id') ?: null,
            'enquiry_type'            => $type,
            'requirement'             => trim($this->request->getPost('requirement') ?? ''),
            'budget'                  => $this->request->getPost('budget') ? (float)$this->request->getPost('budget') : null,
            'preferred_location'      => trim($this->request->getPost('preferred_location') ?? ''),
            'preferred_property_type' => trim($this->request->getPost('preferred_property_type') ?? ''),
            'status'                  => 'Open',
            'remarks'                 => trim($this->request->getPost('remarks') ?? ''),
            'created_by'              => session()->get('user_id'),
        ]);

        $this->auditModel->record(
            session()->get('user_id'),
            'ENQUIRY_CREATED',
            'Enquiries',
            $enqId,
            "Created enquiry {$code} ({$type}) for Lead {$lead['lead_code']}"
        );

        $redirectUrl = $this->request->getPost('redirect_to') ?: "/leads/view/{$leadId}";
        return redirect()->to($redirectUrl)->with('success', "Enquiry {$code} logged successfully.");
    }

    /**
     * Update status
     */
    public function updateStatus($id)
    {
        $enquiry = $this->enquiryModel->find($id);
        if (!$enquiry) {
            return redirect()->back()->with('error', 'Enquiry not found.');
        }

        $newStatus = $this->request->getPost('status');
        $remarks   = trim($this->request->getPost('remarks') ?? '');

        if (!in_array($newStatus, ['Open', 'In Progress', 'Qualified', 'Closed', 'Cancelled'], true)) {
            return redirect()->back()->with('error', 'Invalid enquiry status.');
        }

        $this->enquiryModel->update($id, [
            'status'  => $newStatus,
            'remarks' => $remarks ?: $enquiry['remarks'],
        ]);

        $this->auditModel->record(
            session()->get('user_id'),
            'ENQUIRY_STATUS_UPDATED',
            'Enquiries',
            $id,
            "Updated enquiry {$enquiry['enquiry_code']} status to {$newStatus}"
        );

        return redirect()->back()->with('success', "Enquiry status updated to '{$newStatus}'.");
    }

    /**
     * Delete enquiry
     */
    public function delete($id)
    {
        $enquiry = $this->enquiryModel->find($id);
        if (!$enquiry) {
            return redirect()->back()->with('error', 'Enquiry not found.');
        }

        $this->enquiryModel->delete($id);

        $this->auditModel->record(
            session()->get('user_id'),
            'ENQUIRY_DELETED',
            'Enquiries',
            $id,
            "Deleted enquiry {$enquiry['enquiry_code']}"
        );

        return redirect()->back()->with('success', 'Enquiry removed successfully.');
    }
}
