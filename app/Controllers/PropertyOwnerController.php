<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PropertyOwnerModel;
use App\Models\AuditLogModel;

class PropertyOwnerController extends BaseController
{
    protected $ownerModel;
    protected $auditModel;

    public function __construct()
    {
        $this->ownerModel = new PropertyOwnerModel();
        $this->auditModel = new AuditLogModel();
    }

    public function index()
    {
        $search    = trim($this->request->getGet('search') ?? '');
        $kycStatus = trim($this->request->getGet('kyc_status') ?? '');
        $status    = trim($this->request->getGet('status') ?? '');

        $builder = $this->ownerModel->builder();
        if ($search) {
            $builder->groupStart()
                ->like('first_name', $search)
                ->orLike('last_name', $search)
                ->orLike('owner_code', $search)
                ->orLike('company_name', $search)
                ->orLike('email', $search)
                ->orLike('phone', $search)
                ->groupEnd();
        }
        if ($kycStatus) {
            $builder->where('kyc_status', $kycStatus);
        }
        if ($status) {
            $builder->where('status', $status);
        }

        $owners = $builder->where('deleted_at IS NULL')
            ->orderBy('id', 'DESC')
            ->get()->getResultArray();

        $totalOwners   = $this->ownerModel->countAllResults();
        $verifiedOwners= $this->ownerModel->where('kyc_status', 'verified')->countAllResults();
        $pendingKyc    = $this->ownerModel->where('kyc_status', 'pending')->countAllResults();

        $data = [
            'title'          => 'Property Owners Management',
            'owners'         => $owners,
            'search'         => $search,
            'kycStatus'      => $kycStatus,
            'status'         => $status,
            'totalOwners'    => $totalOwners,
            'verifiedOwners' => $verifiedOwners,
            'pendingKyc'     => $pendingKyc,
        ];

        return view('owners/index', $data);
    }

    public function create()
    {
        return view('owners/create', [
            'title' => 'Register Property Owner',
        ]);
    }

    public function store()
    {
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name'  => 'required|min_length[2]|max_length[100]',
            'email'      => 'required|valid_email|max_length[150]',
            'phone'      => 'required|min_length[8]|max_length[30]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $ownerCode = $this->ownerModel->generateOwnerCode();
        $data = [
            'owner_code'          => $ownerCode,
            'first_name'          => trim($this->request->getPost('first_name')),
            'last_name'           => trim($this->request->getPost('last_name')),
            'company_name'        => trim($this->request->getPost('company_name') ?? ''),
            'email'               => trim($this->request->getPost('email')),
            'phone'               => trim($this->request->getPost('phone')),
            'alternate_phone'     => trim($this->request->getPost('alternate_phone') ?? ''),
            'address'             => trim($this->request->getPost('address') ?? ''),
            'city'                => trim($this->request->getPost('city') ?? ''),
            'state'               => trim($this->request->getPost('state') ?? ''),
            'pincode'             => trim($this->request->getPost('pincode') ?? ''),
            'pan_number'          => trim($this->request->getPost('pan_number') ?? ''),
            'aadhaar_number'      => trim($this->request->getPost('aadhaar_number') ?? ''),
            'kyc_status'          => $this->request->getPost('kyc_status') ?? 'pending',
            'bank_name'           => trim($this->request->getPost('bank_name') ?? ''),
            'bank_account_number' => trim($this->request->getPost('bank_account_number') ?? ''),
            'bank_ifsc'           => trim($this->request->getPost('bank_ifsc') ?? ''),
            'status'              => $this->request->getPost('status') ?? 'active',
            'notes'               => trim($this->request->getPost('notes') ?? ''),
        ];

        $ownerId = $this->ownerModel->insert($data);

        $this->auditModel->log(
            'OWNER_CREATED',
            "Property Owner {$ownerCode} ({$data['first_name']} {$data['last_name']}) registered",
            'property_owners',
            $ownerId
        );

        return redirect()->to('/owners')->with('success', "Property owner {$ownerCode} registered successfully.");
    }

    public function show($id)
    {
        $owner = $this->ownerModel->getOwnerWithDetails($id);
        if (!$owner) {
            return redirect()->to('/owners')->with('error', 'Property owner not found.');
        }

        return view('owners/view', [
            'title' => "Owner Profile: {$owner['first_name']} {$owner['last_name']} ({$owner['owner_code']})",
            'owner' => $owner,
        ]);
    }

    public function edit($id)
    {
        $owner = $this->ownerModel->find($id);
        if (!$owner) {
            return redirect()->to('/owners')->with('error', 'Property owner not found.');
        }

        return view('owners/edit', [
            'title' => "Edit Owner: {$owner['owner_code']}",
            'owner' => $owner,
        ]);
    }

    public function update($id)
    {
        $owner = $this->ownerModel->find($id);
        if (!$owner) {
            return redirect()->to('/owners')->with('error', 'Property owner not found.');
        }

        $rules = [
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name'  => 'required|min_length[2]|max_length[100]',
            'email'      => 'required|valid_email|max_length[150]',
            'phone'      => 'required|min_length[8]|max_length[30]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'first_name'          => trim($this->request->getPost('first_name')),
            'last_name'           => trim($this->request->getPost('last_name')),
            'company_name'        => trim($this->request->getPost('company_name') ?? ''),
            'email'               => trim($this->request->getPost('email')),
            'phone'               => trim($this->request->getPost('phone')),
            'alternate_phone'     => trim($this->request->getPost('alternate_phone') ?? ''),
            'address'             => trim($this->request->getPost('address') ?? ''),
            'city'                => trim($this->request->getPost('city') ?? ''),
            'state'               => trim($this->request->getPost('state') ?? ''),
            'pincode'             => trim($this->request->getPost('pincode') ?? ''),
            'pan_number'          => trim($this->request->getPost('pan_number') ?? ''),
            'aadhaar_number'      => trim($this->request->getPost('aadhaar_number') ?? ''),
            'kyc_status'          => $this->request->getPost('kyc_status') ?? 'pending',
            'bank_name'           => trim($this->request->getPost('bank_name') ?? ''),
            'bank_account_number' => trim($this->request->getPost('bank_account_number') ?? ''),
            'bank_ifsc'           => trim($this->request->getPost('bank_ifsc') ?? ''),
            'status'              => $this->request->getPost('status') ?? 'active',
            'notes'               => trim($this->request->getPost('notes') ?? ''),
        ];

        $this->ownerModel->update($id, $data);

        $this->auditModel->log(
            'OWNER_UPDATED',
            "Property Owner {$owner['owner_code']} profile updated",
            'property_owners',
            $id
        );

        return redirect()->to('/owners/view/' . $id)->with('success', 'Owner profile updated successfully.');
    }

    public function delete($id)
    {
        $owner = $this->ownerModel->find($id);
        if (!$owner) {
            return redirect()->to('/owners')->with('error', 'Property owner not found.');
        }

        $this->ownerModel->delete($id);

        $this->auditModel->log(
            'OWNER_DELETED',
            "Property Owner {$owner['owner_code']} deactivated",
            'property_owners',
            $id
        );

        return redirect()->to('/owners')->with('success', "Owner {$owner['owner_code']} deactivated successfully.");
    }

    public function statement($id)
    {
        $owner = $this->ownerModel->getOwnerWithDetails($id);
        if (!$owner) {
            return redirect()->to('/owners')->with('error', 'Property owner not found.');
        }

        return view('owners/statement', [
            'title' => "Owner Portfolio Statement - {$owner['owner_code']}",
            'owner' => $owner,
        ]);
    }
}
