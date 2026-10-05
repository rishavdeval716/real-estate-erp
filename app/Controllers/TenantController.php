<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TenantModel;
use App\Models\TenantDocumentModel;
use App\Models\RentalHistoryModel;
use App\Models\LeaseAgreementModel;
use App\Models\MaintenanceRequestModel;
use App\Models\AuditLogModel;
use App\Models\PropertyModel;
use App\Models\PropertyUnitModel;

class TenantController extends BaseController
{
    protected $tenantModel;
    protected $docModel;
    protected $historyModel;
    protected $leaseModel;
    protected $maintenanceModel;
    protected $auditModel;
    protected $propertyModel;
    protected $unitModel;

    public function __construct()
    {
        $this->tenantModel      = new TenantModel();
        $this->docModel         = new TenantDocumentModel();
        $this->historyModel     = new RentalHistoryModel();
        $this->leaseModel       = new LeaseAgreementModel();
        $this->maintenanceModel = new MaintenanceRequestModel();
        $this->auditModel       = new AuditLogModel();
        $this->propertyModel    = new PropertyModel();
        $this->unitModel        = new PropertyUnitModel();
    }

    public function index()
    {
        $search    = trim($this->request->getGet('search') ?? '');
        $status    = trim($this->request->getGet('status') ?? '');
        $kycStatus = trim($this->request->getGet('kyc_status') ?? '');
        $type      = trim($this->request->getGet('tenant_type') ?? '');

        $builder = $this->tenantModel->select('tenants.*, p.title as property_title, u.unit_number')
            ->join('properties p', 'p.id = tenants.occupied_property_id', 'left')
            ->join('property_units u', 'u.id = tenants.occupied_unit_id', 'left')
            ->where('tenants.deleted_at', null);

        if ($search !== '') {
            $builder->groupStart()
                ->like('tenants.full_name', $search)
                ->orLike('tenants.company_name', $search)
                ->orLike('tenants.tenant_code', $search)
                ->orLike('tenants.mobile', $search)
                ->orLike('tenants.email', $search)
                ->groupEnd();
        }

        if ($status !== '') {
            $builder->where('tenants.status', $status);
        }

        if ($kycStatus !== '') {
            $builder->where('tenants.kyc_status', $kycStatus);
        }

        if ($type !== '') {
            $builder->where('tenants.tenant_type', $type);
        }

        $tenants = $builder->orderBy('tenants.id', 'DESC')->paginate(15);
        $pager   = $this->tenantModel->pager;

        $db = \Config\Database::connect();
        $kpi = [
            'total'        => $db->table('tenants')->where('deleted_at', null)->countAllResults(),
            'active'       => $db->table('tenants')->where('status', 'active')->where('deleted_at', null)->countAllResults(),
            'kyc_verified' => $db->table('tenants')->where('kyc_status', 'verified')->where('deleted_at', null)->countAllResults(),
            'kyc_pending'  => $db->table('tenants')->where('kyc_status', 'pending')->where('deleted_at', null)->countAllResults(),
        ];

        return view('tenants/index', [
            'title'   => 'Tenant Directory - Real Estate ERP',
            'tenants' => $tenants,
            'pager'   => $pager,
            'filters' => [
                'search'      => $search,
                'status'      => $status,
                'kyc_status'  => $kycStatus,
                'tenant_type' => $type,
            ],
            'kpi'     => $kpi,
        ]);
    }

    public function create()
    {
        $properties = $this->propertyModel->where('status', 'Available')->orWhere('status', 'Rented')->findAll();
        $units      = $this->unitModel->where('availability_status', 'Available')->findAll();

        return view('tenants/create', [
            'title'      => 'Register New Tenant - Real Estate ERP',
            'properties' => $properties,
            'units'      => $units,
        ]);
    }

    public function store()
    {
        $rules = [
            'tenant_type' => 'required|in_list[individual,company]',
            'full_name'   => 'required|min_length[3]|max_length[255]',
            'mobile'      => 'required|min_length[8]|max_length[20]',
            'email'       => 'required|valid_email|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $code = $this->tenantModel->generateTenantCode();

        $data = [
            'tenant_code'          => $code,
            'tenant_type'          => $this->request->getPost('tenant_type'),
            'full_name'            => trim($this->request->getPost('full_name')),
            'company_name'         => trim($this->request->getPost('company_name') ?? '') ?: null,
            'contact_person'       => trim($this->request->getPost('contact_person') ?? '') ?: null,
            'mobile'               => trim($this->request->getPost('mobile')),
            'email'                => trim($this->request->getPost('email')),
            'address'              => trim($this->request->getPost('address') ?? '') ?: null,
            'city'                 => trim($this->request->getPost('city') ?? '') ?: null,
            'state'                => trim($this->request->getPost('state') ?? '') ?: null,
            'pincode'              => trim($this->request->getPost('pincode') ?? '') ?: null,
            'id_proof_type'        => trim($this->request->getPost('id_proof_type') ?? '') ?: null,
            'id_proof_number'      => trim($this->request->getPost('id_proof_number') ?? '') ?: null,
            'kyc_status'           => 'pending',
            'occupied_property_id' => $this->request->getPost('occupied_property_id') ?: null,
            'occupied_unit_id'     => $this->request->getPost('occupied_unit_id') ?: null,
            'status'               => $this->request->getPost('status') ?? 'active',
            'notes'                => trim($this->request->getPost('notes') ?? '') ?: null,
        ];

        $tenantId = $this->tenantModel->insert($data);

        $this->auditModel->record(
            session()->get('user_id'),
            'TENANT_CREATED',
            'Tenants',
            $tenantId,
            "Created tenant {$code} - {$data['full_name']}"
        );

        return redirect()->to(base_url("tenants/view/{$tenantId}"))->with('success', "Tenant {$code} registered successfully.");
    }

    public function view(int $id)
    {
        $tenant = $this->tenantModel->getTenantWithDetails($id);
        if (!$tenant) {
            return redirect()->to(base_url('tenants'))->with('error', 'Tenant not found.');
        }

        $documents = $this->docModel->getByTenant($id);
        $leases    = $this->leaseModel->where('tenant_id', $id)->orderBy('id', 'DESC')->findAll();
        $history   = $this->historyModel->getByTenant($id);
        $tickets   = $this->maintenanceModel->where('tenant_id', $id)->orderBy('id', 'DESC')->findAll();

        return view('tenants/view', [
            'title'     => "Tenant {$tenant['tenant_code']} - {$tenant['full_name']}",
            'tenant'    => $tenant,
            'documents' => $documents,
            'leases'    => $leases,
            'history'   => $history,
            'tickets'   => $tickets,
        ]);
    }

    public function edit(int $id)
    {
        $tenant = $this->tenantModel->find($id);
        if (!$tenant) {
            return redirect()->to(base_url('tenants'))->with('error', 'Tenant not found.');
        }

        $properties = $this->propertyModel->findAll();
        $units      = $this->unitModel->findAll();

        return view('tenants/edit', [
            'title'      => "Edit Tenant {$tenant['tenant_code']}",
            'tenant'     => $tenant,
            'properties' => $properties,
            'units'      => $units,
        ]);
    }

    public function update(int $id)
    {
        $tenant = $this->tenantModel->find($id);
        if (!$tenant) {
            return redirect()->to(base_url('tenants'))->with('error', 'Tenant not found.');
        }

        $rules = [
            'tenant_type' => 'required|in_list[individual,company]',
            'full_name'   => 'required|min_length[3]|max_length[255]',
            'mobile'      => 'required|min_length[8]|max_length[20]',
            'email'       => 'required|valid_email|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'tenant_type'          => $this->request->getPost('tenant_type'),
            'full_name'            => trim($this->request->getPost('full_name')),
            'company_name'         => trim($this->request->getPost('company_name') ?? '') ?: null,
            'contact_person'       => trim($this->request->getPost('contact_person') ?? '') ?: null,
            'mobile'               => trim($this->request->getPost('mobile')),
            'email'                => trim($this->request->getPost('email')),
            'address'              => trim($this->request->getPost('address') ?? '') ?: null,
            'city'                 => trim($this->request->getPost('city') ?? '') ?: null,
            'state'                => trim($this->request->getPost('state') ?? '') ?: null,
            'pincode'              => trim($this->request->getPost('pincode') ?? '') ?: null,
            'id_proof_type'        => trim($this->request->getPost('id_proof_type') ?? '') ?: null,
            'id_proof_number'      => trim($this->request->getPost('id_proof_number') ?? '') ?: null,
            'occupied_property_id' => $this->request->getPost('occupied_property_id') ?: null,
            'occupied_unit_id'     => $this->request->getPost('occupied_unit_id') ?: null,
            'status'               => $this->request->getPost('status') ?? 'active',
            'notes'                => trim($this->request->getPost('notes') ?? '') ?: null,
        ];

        $this->tenantModel->update($id, $data);

        $this->auditModel->record(
            session()->get('user_id'),
            'TENANT_UPDATED',
            'Tenants',
            $id,
            "Updated tenant profile {$tenant['tenant_code']}"
        );

        return redirect()->to(base_url("tenants/view/{$id}"))->with('success', 'Tenant details updated successfully.');
    }

    public function delete(int $id)
    {
        $tenant = $this->tenantModel->find($id);
        if (!$tenant) {
            return redirect()->to(base_url('tenants'))->with('error', 'Tenant not found.');
        }

        $activeLeases = $this->leaseModel->where('tenant_id', $id)
            ->whereIn('status', ['draft', 'active', 'expiring_soon'])
            ->countAllResults();

        if ($activeLeases > 0) {
            return redirect()->back()->with('error', "Cannot delete tenant {$tenant['tenant_code']} with active lease agreements.");
        }

        $this->tenantModel->delete($id);

        $this->auditModel->record(
            session()->get('user_id'),
            'TENANT_DELETED',
            'Tenants',
            $id,
            "Deleted tenant {$tenant['tenant_code']}"
        );

        return redirect()->to(base_url('tenants'))->with('success', "Tenant {$tenant['tenant_code']} deleted.");
    }

    public function uploadKyc(int $id)
    {
        $tenant = $this->tenantModel->find($id);
        if (!$tenant) {
            return redirect()->to(base_url('tenants'))->with('error', 'Tenant not found.');
        }

        $file = $this->request->getFile('kyc_file');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Please choose a valid document file (PDF, JPG, PNG).');
        }

        $uploadPath = WRITEPATH . 'uploads/kyc/tenants/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0775, true);
        }

        $newName = $file->getRandomName();
        $file->move($uploadPath, $newName);

        $docData = [
            'tenant_id'           => $id,
            'document_type'       => $this->request->getPost('document_type') ?? 'Identity Proof',
            'document_number'     => trim($this->request->getPost('document_number') ?? '') ?: null,
            'file_name'           => $file->getClientName(),
            'file_path'           => 'kyc/tenants/' . $newName,
            'verification_status' => 'pending',
            'expiry_date'         => $this->request->getPost('expiry_date') ?: null,
            'remarks'             => trim($this->request->getPost('remarks') ?? '') ?: null,
        ];

        $this->docModel->insert($docData);

        $this->auditModel->record(
            session()->get('user_id'),
            'TENANT_KYC_UPLOADED',
            'Tenants',
            $id,
            "Uploaded KYC document {$docData['document_type']} for {$tenant['tenant_code']}"
        );

        return redirect()->back()->with('success', 'KYC document uploaded successfully.');
    }

    public function verifyKyc(int $docId)
    {
        $doc = $this->docModel->find($docId);
        if (!$doc) {
            return redirect()->back()->with('error', 'Document not found.');
        }

        $status  = $this->request->getPost('status') ?? 'verified';
        $remarks = trim($this->request->getPost('remarks') ?? '');
        $userId  = session()->get('user_id');

        $this->docModel->update($docId, [
            'verification_status' => $status,
            'verified_by'         => $userId,
            'verification_date'   => date('Y-m-d H:i:s'),
            'remarks'             => $remarks ?: $doc['remarks'],
        ]);

        // If verified, check if tenant has verified documents and update tenant kyc_status
        $tenantId = (int)$doc['tenant_id'];
        if ($status === 'verified') {
            $this->tenantModel->update($tenantId, ['kyc_status' => 'verified']);
        } elseif ($status === 'rejected') {
            $pendingOrVerified = $this->docModel->where('tenant_id', $tenantId)
                ->where('verification_status', 'verified')
                ->countAllResults();
            if ($pendingOrVerified == 0) {
                $this->tenantModel->update($tenantId, ['kyc_status' => 'rejected']);
            }
        }

        $this->auditModel->record(
            $userId,
            'TENANT_KYC_VERIFIED',
            'Tenants',
            $tenantId,
            "Updated KYC document #{$docId} status to {$status}"
        );

        return redirect()->back()->with('success', "Document status updated to {$status}.");
    }

    public function viewKycDocument(int $docId)
    {
        $doc = $this->docModel->find($docId);
        if (!$doc) {
            return $this->response->setStatusCode(404, 'Document not found');
        }

        $fullPath = WRITEPATH . 'uploads/' . $doc['file_path'];
        if (!file_exists($fullPath)) {
            return $this->response->setStatusCode(404, 'File not found on server');
        }

        $mime = mime_content_type($fullPath) ?: 'application/octet-stream';
        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'inline; filename="' . basename($doc['file_name']) . '"')
            ->setBody(file_get_contents($fullPath));
    }
}
