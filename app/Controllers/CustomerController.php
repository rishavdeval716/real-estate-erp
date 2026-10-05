<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\CustomerDocumentModel;
use App\Models\LeadModel;
use App\Models\AuditLogModel;

class CustomerController extends BaseController
{
    protected $customerModel;
    protected $docModel;
    protected $leadModel;
    protected $auditModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        $this->docModel      = new CustomerDocumentModel();
        $this->leadModel     = new LeadModel();
        $this->auditModel    = new AuditLogModel();
    }

    /**
     * Customer directory with filters and search
     */
    public function index()
    {
        $filters = [
            'search'     => trim($this->request->getGet('search') ?? ''),
            'kyc_status' => trim($this->request->getGet('kyc_status') ?? ''),
            'status'     => trim($this->request->getGet('status') ?? ''),
            'sort'       => $this->request->getGet('sort') ?? 'customers.created_at',
            'order'      => $this->request->getGet('order') ?? 'DESC',
        ];

        $dataCustomers = $this->customerModel->getCustomersWithDetails($filters, 15);

        // Dynamic KPI counters
        $db = \Config\Database::connect();
        $kpi = [
            'total'    => $db->table('customers')->where('deleted_at', null)->countAllResults(),
            'verified' => $db->table('customers')->where('kyc_status', 'Verified')->where('deleted_at', null)->countAllResults(),
            'pending'  => $db->table('customers')->where('kyc_status', 'Pending')->where('deleted_at', null)->countAllResults(),
            'rejected' => $db->table('customers')->where('kyc_status', 'Rejected')->where('deleted_at', null)->countAllResults(),
        ];

        $data = [
            'title'     => 'Customer Directory - Real Estate ERP',
            'customers' => $dataCustomers['customers'],
            'pager'     => $dataCustomers['pager'],
            'filters'   => $filters,
            'kpi'       => $kpi,
        ];

        return view('customers/index', $data);
    }

    /**
     * Show create customer form (optional lead conversion)
     */
    public function create()
    {
        $leadId = (int)$this->request->getGet('lead_id');
        $lead = null;

        if ($leadId > 0) {
            $lead = $this->leadModel->find($leadId);
        }

        $data = [
            'title' => 'Register New Customer - Real Estate ERP',
            'lead'  => $lead,
        ];

        return view('customers/create', $data);
    }

    /**
     * Store new customer
     */
    public function store()
    {
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[100]',
            'phone'      => 'required|min_length[8]|max_length[30]',
            'email'      => 'permit_empty|valid_email|max_length[150]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $leadId = $this->request->getPost('lead_id') ? (int)$this->request->getPost('lead_id') : null;

        // Prevent duplicate customer for the same lead if one already exists
        if ($leadId) {
            $existing = $this->customerModel->where('lead_id', $leadId)->where('deleted_at', null)->first();
            if ($existing) {
                return redirect()->to("/customers/view/{$existing['id']}")->with('info', "Customer {$existing['customer_code']} already exists for this lead.");
            }
        }

        $code = $this->customerModel->generateCustomerCode();

        $customerId = $this->customerModel->insert([
            'customer_code'   => $code,
            'lead_id'         => $leadId,
            'first_name'      => trim($this->request->getPost('first_name')),
            'last_name'       => trim($this->request->getPost('last_name') ?? ''),
            'email'           => trim($this->request->getPost('email') ?? ''),
            'phone'           => trim($this->request->getPost('phone')),
            'alternate_phone' => trim($this->request->getPost('alternate_phone') ?? ''),
            'address'         => trim($this->request->getPost('address') ?? ''),
            'city'            => trim($this->request->getPost('city') ?? ''),
            'state'           => trim($this->request->getPost('state') ?? ''),
            'pincode'         => trim($this->request->getPost('pincode') ?? ''),
            'id_proof_type'   => $this->request->getPost('id_proof_type') ?: null,
            'id_proof_number' => trim($this->request->getPost('id_proof_number') ?? ''),
            'kyc_status'      => 'Pending',
            'status'          => 'Active',
        ]);

        // If converted from lead, advance lead stage to 'Ready for Booking'
        if ($leadId) {
            $this->leadModel->update($leadId, [
                'lead_stage'  => 'Ready for Booking',
                'lead_status' => 'Qualified',
            ]);
        }

        $this->auditModel->record(
            session()->get('user_id'),
            'CUSTOMER_CREATED',
            'Customers',
            $customerId,
            "Created customer profile {$code} ({$this->request->getPost('first_name')})" . ($leadId ? " converted from Lead ID #{$leadId}" : "")
        );

        return redirect()->to("/customers/view/{$customerId}")->with('success', "Customer {$code} registered successfully.");
    }

    /**
     * Direct conversion of Lead into Customer
     */
    public function convertLead($leadId)
    {
        $lead = $this->leadModel->find($leadId);
        if (!$lead) {
            return redirect()->to('/customers')->with('error', 'Lead record not found.');
        }

        // Prevent duplicate conversion
        $existing = $this->customerModel->where('lead_id', $leadId)->where('deleted_at', null)->first();
        if ($existing) {
            return redirect()->to("/customers/view/{$existing['id']}")->with('info', "Customer {$existing['customer_code']} already exists for this lead.");
        }

        $code = $this->customerModel->generateCustomerCode();
        $userId = session()->get('user_id');

        $customerId = $this->customerModel->insert([
            'customer_code'   => $code,
            'lead_id'         => (int)$leadId,
            'first_name'      => $lead['first_name'] ?? 'Customer',
            'last_name'       => $lead['last_name'] ?? '',
            'email'           => $lead['email'] ?? '',
            'phone'           => $lead['phone'] ?? '',
            'alternate_phone' => null,
            'address'         => $lead['address'] ?? '',
            'city'            => $lead['city'] ?? '',
            'state'           => '',
            'pincode'         => '',
            'id_proof_type'   => null,
            'id_proof_number' => null,
            'kyc_status'      => 'Pending',
            'status'          => 'active',
        ]);

        // Advance lead stage to 'Ready for Booking'
        $this->leadModel->update($leadId, [
            'lead_stage'  => 'Ready for Booking',
            'lead_status' => 'Qualified',
        ]);

        $this->auditModel->record(
            $userId,
            'Customer Created',
            'customers',
            $customerId,
            "Customer {$code} converted from Lead #{$lead['lead_code']}"
        );

        return redirect()->to("/customers/view/{$customerId}")->with('success', "Lead #{$lead['lead_code']} converted to Customer {$code} successfully.");
    }

    /**
     * View comprehensive customer detail page with KYC, bookings, payments, and invoices
     */
    public function view($id)
    {
        $customer = $this->customerModel->getCustomerWithFullProfile((int)$id);
        if (!$customer) {
            return redirect()->to('/customers')->with('error', 'Customer not found.');
        }

        $data = [
            'title'    => "Customer File: {$customer['customer_code']} - Real Estate ERP",
            'customer' => $customer,
        ];

        return view('customers/view', $data);
    }

    /**
     * Edit customer form
     */
    public function edit($id)
    {
        $customer = $this->customerModel->where('deleted_at', null)->find($id);
        if (!$customer) {
            return redirect()->to('/customers')->with('error', 'Customer not found.');
        }

        $data = [
            'title'    => "Edit Customer {$customer['customer_code']}",
            'customer' => $customer,
        ];

        return view('customers/edit', $data);
    }

    /**
     * Update customer
     */
    public function update($id)
    {
        $customer = $this->customerModel->where('deleted_at', null)->find($id);
        if (!$customer) {
            return redirect()->to('/customers')->with('error', 'Customer not found.');
        }

        $rules = [
            'first_name' => 'required|min_length[2]|max_length[100]',
            'phone'      => 'required|min_length[8]|max_length[30]',
            'email'      => 'permit_empty|valid_email|max_length[150]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->customerModel->update($id, [
            'first_name'      => trim($this->request->getPost('first_name')),
            'last_name'       => trim($this->request->getPost('last_name') ?? ''),
            'email'           => trim($this->request->getPost('email') ?? ''),
            'phone'           => trim($this->request->getPost('phone')),
            'alternate_phone' => trim($this->request->getPost('alternate_phone') ?? ''),
            'address'         => trim($this->request->getPost('address') ?? ''),
            'city'            => trim($this->request->getPost('city') ?? ''),
            'state'           => trim($this->request->getPost('state') ?? ''),
            'pincode'         => trim($this->request->getPost('pincode') ?? ''),
            'id_proof_type'   => $this->request->getPost('id_proof_type') ?: null,
            'id_proof_number' => trim($this->request->getPost('id_proof_number') ?? ''),
            'status'          => $this->request->getPost('status') ?: 'Active',
        ]);

        $this->auditModel->record(
            session()->get('user_id'),
            'CUSTOMER_UPDATED',
            'Customers',
            $id,
            "Updated customer {$customer['customer_code']}"
        );

        return redirect()->to("/customers/view/{$id}")->with('success', "Customer {$customer['customer_code']} updated successfully.");
    }

    /**
     * Upload KYC document
     */
    public function uploadKyc($id)
    {
        $customer = $this->customerModel->where('deleted_at', null)->find($id);
        if (!$customer) {
            return redirect()->to('/customers')->with('error', 'Customer not found.');
        }

        $file = $this->request->getFile('kyc_file');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Please select a valid document file to upload.');
        }

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
        if (!in_array($file->getMimeType(), $allowedMimes, true)) {
            return redirect()->back()->with('error', 'Only PDF, JPEG, and PNG document formats are permitted.');
        }

        $uploadDir = WRITEPATH . 'uploads/kyc';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $newName = $file->getRandomName();
        $file->move($uploadDir, $newName);

        $docType = $this->request->getPost('document_type') ?: 'Identity Proof';
        $docNum  = trim($this->request->getPost('document_number') ?? '');

        $docId = $this->docModel->insert([
            'customer_id'         => $id,
            'document_type'       => $docType,
            'document_number'     => $docNum,
            'file_name'           => $file->getClientName(),
            'file_path'           => 'uploads/kyc/' . $newName,
            'verification_status' => 'Pending',
        ]);

        $this->auditModel->record(
            session()->get('user_id'),
            'KYC_UPLOADED',
            'Customers',
            $id,
            "Uploaded {$docType} document ({$file->getClientName()}) for Customer {$customer['customer_code']}"
        );

        return redirect()->to("/customers/view/{$id}")->with('success', "KYC document uploaded successfully.");
    }

    /**
     * Verify or reject KYC document
     */
    public function verifyKyc($docId)
    {
        $doc = $this->docModel->find($docId);
        if (!$doc) {
            return redirect()->back()->with('error', 'Document record not found.');
        }

        $status = $this->request->getPost('verification_status');
        if (!in_array($status, ['Verified', 'Rejected'], true)) {
            return redirect()->back()->with('error', 'Invalid verification action.');
        }

        $userId = session()->get('user_id');
        $now    = date('Y-m-d H:i:s');

        $this->docModel->update($docId, [
            'verification_status' => $status,
            'verified_by'         => $userId,
            'verified_at'         => $now,
        ]);

        // Evaluate overall customer KYC status
        $allDocs = $this->docModel->where('customer_id', $doc['customer_id'])->findAll();
        $hasVerified = false;
        $hasPending = false;
        foreach ($allDocs as $d) {
            if ($d['verification_status'] === 'Verified') $hasVerified = true;
            if ($d['verification_status'] === 'Pending')  $hasPending = true;
        }

        $overallStatus = 'Pending';
        if ($hasVerified && !$hasPending) {
            $overallStatus = 'Verified';
        } elseif ($status === 'Rejected' && !$hasVerified) {
            $overallStatus = 'Rejected';
        }

        $this->customerModel->update($doc['customer_id'], ['kyc_status' => $overallStatus]);

        $this->auditModel->record(
            $userId,
            'KYC_VERIFIED',
            'Customers',
            $doc['customer_id'],
            "Marked {$doc['document_type']} as '{$status}'. Overall KYC: {$overallStatus}"
        );

        return redirect()->back()->with('success', "Document status updated to {$status}.");
    }

    /**
     * Soft delete customer
     */
    public function delete($id)
    {
        $customer = $this->customerModel->where('deleted_at', null)->find($id);
        if (!$customer) {
            return redirect()->to('/customers')->with('error', 'Customer not found.');
        }

        // Prevent delete if customer has active bookings
        $db = \Config\Database::connect();
        $activeBookings = $db->table('bookings')
            ->where('customer_id', $id)
            ->whereIn('booking_status', ['Draft', 'Pending Confirmation', 'Confirmed'])
            ->where('deleted_at', null)
            ->countAllResults();

        if ($activeBookings > 0) {
            return redirect()->back()->with('error', "Cannot delete customer {$customer['customer_code']}. There are active booking transactions associated with this customer.");
        }

        $this->customerModel->delete($id);

        $this->auditModel->record(
            session()->get('user_id'),
            'CUSTOMER_DELETED',
            'Customers',
            $id,
            "Soft deleted customer {$customer['customer_code']}"
        );

        return redirect()->to('/customers')->with('success', "Customer {$customer['customer_code']} archived.");
    }
}
