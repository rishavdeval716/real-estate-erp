<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PropertyVerificationModel;
use App\Models\PropertyModel;
use App\Models\AuditLogModel;

class PropertyVerificationController extends BaseController
{
    protected $verificationModel;
    protected $propertyModel;
    protected $auditModel;

    public function __construct()
    {
        $this->verificationModel = new PropertyVerificationModel();
        $this->propertyModel     = new PropertyModel();
        $this->auditModel        = new AuditLogModel();
    }

    public function index()
    {
        $type   = $this->request->getGet('type');
        $status = $this->request->getGet('status');
        $search = trim($this->request->getGet('search') ?? '');

        $builder = $this->verificationModel->select('property_verifications.*, properties.title as property_title, properties.property_code')
            ->join('properties', 'properties.id = property_verifications.property_id', 'left');

        if ($type) {
            $builder->where('property_verifications.verification_type', $type);
        }
        if ($status) {
            $builder->where('property_verifications.status', $status);
        }
        if ($search) {
            $builder->groupStart()
                ->like('property_verifications.verification_code', $search)
                ->orLike('properties.title', $search)
                ->orLike('properties.property_code', $search)
                ->groupEnd();
        }

        $verifications = $builder->orderBy('property_verifications.id', 'DESC')->get()->getResultArray();

        $totalAudits    = $this->verificationModel->countAllResults();
        $verifiedAudits = $this->verificationModel->where('status', 'Verified')->countAllResults();
        $pendingAudits  = $this->verificationModel->where('status', 'Pending')->countAllResults();
        $underReview    = $this->verificationModel->where('status', 'Under Review')->countAllResults();

        return view('verifications/index', [
            'title'          => 'Property Verification & Compliance',
            'verifications'  => $verifications,
            'type'           => $type,
            'status'         => $status,
            'search'         => $search,
            'totalAudits'    => $totalAudits,
            'verifiedAudits' => $verifiedAudits,
            'pendingAudits'  => $pendingAudits,
            'underReview'    => $underReview,
        ]);
    }

    public function create()
    {
        $properties = $this->propertyModel->where('deleted_at IS NULL')->findAll();

        return view('verifications/create', [
            'title'      => 'Schedule Compliance Audit',
            'properties' => $properties,
        ]);
    }

    public function store()
    {
        $rules = [
            'property_id'       => 'required|numeric',
            'verification_type' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $code = $this->verificationModel->generateVerificationCode();
        $userId = session()->get('user_id') ?? 1;

        $checklists = [
            'Legal Title Clearance' => [
                'title_deed_verified'      => (bool)$this->request->getPost('check_title'),
                'non_encumbrance_verified' => (bool)$this->request->getPost('check_encumbrance'),
                'tax_receipts_cleared'     => (bool)$this->request->getPost('check_tax'),
                'litigation_search_clear'  => (bool)$this->request->getPost('check_litigation'),
            ],
            'RERA Compliance Check' => [
                'rera_number_active'         => (bool)$this->request->getPost('check_rera'),
                'escrow_account_reconciled'  => (bool)$this->request->getPost('check_escrow'),
                'quarterly_filings_current'  => (bool)$this->request->getPost('check_filings'),
                'advertisement_rera_labeled' => (bool)$this->request->getPost('check_ads'),
            ],
        ];

        $type = $this->request->getPost('verification_type');
        $checklistData = $checklists[$type] ?? [
            'site_inspected'     => true,
            'statutory_cleared'  => true,
        ];

        $data = [
            'verification_code' => $code,
            'property_id'       => $this->request->getPost('property_id'),
            'verification_type' => $type,
            'status'            => $this->request->getPost('status') ?? 'Pending',
            'assigned_to'       => $userId,
            'checklist_data'    => json_encode($checklistData),
            'findings'          => trim($this->request->getPost('findings') ?? ''),
            'expiry_date'       => $this->request->getPost('expiry_date') ?: null,
        ];

        $verId = $this->verificationModel->insert($data);

        $this->auditModel->log(
            'VERIFICATION_SCHEDULED',
            "Property Compliance Audit {$code} ({$data['verification_type']}) scheduled",
            'property_verifications',
            $verId
        );

        return redirect()->to('/verifications')->with('success', "Verification audit {$code} scheduled successfully.");
    }

    public function show($id)
    {
        $verification = $this->verificationModel->select('property_verifications.*, properties.title as property_title, properties.property_code, properties.price, properties.area, properties.ownership_details, properties.owner_name_or_reference')
            ->join('properties', 'properties.id = property_verifications.property_id', 'left')
            ->where('property_verifications.id', $id)
            ->first();

        if (!$verification) {
            return redirect()->to('/verifications')->with('error', 'Verification audit not found.');
        }

        return view('verifications/view', [
            'title'        => "Audit: {$verification['verification_code']}",
            'verification' => $verification,
        ]);
    }

    public function conduct($id)
    {
        $verification = $this->verificationModel->find($id);
        if (!$verification) {
            return redirect()->to('/verifications')->with('error', 'Verification record not found.');
        }

        $status   = $this->request->getPost('status');
        $findings = trim($this->request->getPost('findings') ?? '');
        $rejection= trim($this->request->getPost('rejection_reason') ?? '');
        $userId   = session()->get('user_id') ?? 1;

        $updateData = [
            'status'           => $status,
            'findings'         => $findings,
            'rejection_reason' => $status === 'Rejected' ? $rejection : null,
            'verified_by'      => $userId,
            'verified_at'      => date('Y-m-d H:i:s'),
        ];

        $this->verificationModel->update($id, $updateData);

        $this->auditModel->log(
            'VERIFICATION_CONDUCTED',
            "Property Verification {$verification['verification_code']} marked as {$status}",
            'property_verifications',
            $id
        );

        return redirect()->to('/verifications/view/' . $id)->with('success', "Verification audit updated to {$status}.");
    }
}
