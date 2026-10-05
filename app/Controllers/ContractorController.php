<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ContractorModel;
use App\Models\ConstructionWorkOrderModel;
use App\Models\ProjectModel;
use App\Models\ProjectTowerModel;
use App\Models\ConstructionMilestoneModel;
use App\Models\AuditLogModel;

class ContractorController extends BaseController
{
    protected $contractorModel;
    protected $workOrderModel;
    protected $projectModel;
    protected $towerModel;
    protected $milestoneModel;

    public function __construct()
    {
        $this->contractorModel = new ContractorModel();
        $this->workOrderModel  = new ConstructionWorkOrderModel();
        $this->projectModel    = new ProjectModel();
        $this->towerModel      = new ProjectTowerModel();
        $this->milestoneModel  = new ConstructionMilestoneModel();
    }

    // =====================================================
    // 1. CONTRACTOR DIRECTORY
    // =====================================================
    public function index()
    {
        $specialization = trim($this->request->getGet('specialization') ?? '');
        $status         = trim($this->request->getGet('status') ?? '');
        $search         = trim($this->request->getGet('search') ?? '');

        $builder = $this->contractorModel->builder();
        if ($specialization) {
            $builder->where('specialization', $specialization);
        }
        if ($status) {
            $builder->where('status', $status);
        }
        if ($search) {
            $builder->groupStart()
                ->like('company_name', $search)
                ->orLike('contractor_code', $search)
                ->orLike('contact_person', $search)
                ->orLike('email', $search)
                ->groupEnd();
        }

        $contractors = $builder->orderBy('id', 'DESC')->get()->getResultArray();

        $data = [
            'title'                  => 'Contractor & Vendor Management',
            'contractors'            => $contractors,
            'selectedSpecialization' => $specialization,
            'selectedStatus'         => $status,
            'search'                 => $search,
        ];

        return view('contractors/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Onboard New Contractor / Trade Specialist',
        ];

        return view('contractors/create', $data);
    }

    public function store()
    {
        $rules = [
            'company_name'   => 'required|min_length[3]|max_length[255]',
            'specialization' => 'required',
            'contact_person' => 'required|min_length[2]|max_length[150]',
            'phone'          => 'required|min_length[7]|max_length[50]',
            'email'          => 'required|valid_email|max_length[150]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $code = $this->contractorModel->generateContractorCode();

        $contractorData = [
            'contractor_code' => $code,
            'company_name'    => trim($this->request->getPost('company_name')),
            'specialization'  => trim($this->request->getPost('specialization')),
            'contact_person'  => trim($this->request->getPost('contact_person')),
            'phone'           => trim($this->request->getPost('phone')),
            'email'           => trim($this->request->getPost('email')),
            'license_number'  => trim($this->request->getPost('license_number') ?? ''),
            'gstin'           => trim($this->request->getPost('gstin') ?? ''),
            'rating'          => (float)$this->request->getPost('rating') ?: 5.0,
            'status'          => $this->request->getPost('status') ?: 'Active',
        ];

        $insertedId = $this->contractorModel->insert($contractorData);

        AuditLogModel::record(
            'CONTRACTOR_CREATED',
            'Contractors',
            $insertedId,
            "Onboarded contractor {$code}: {$contractorData['company_name']} ({$contractorData['specialization']})"
        );

        return redirect()->to('/contractors')->with('success', "Contractor {$code} onboarded successfully.");
    }

    public function edit(int $id)
    {
        $contractor = $this->contractorModel->find($id);
        if (!$contractor) {
            return redirect()->to('/contractors')->with('error', 'Contractor not found.');
        }

        $data = [
            'title'      => 'Edit Contractor Profile',
            'contractor' => $contractor,
        ];

        return view('contractors/edit', $data);
    }

    public function update(int $id)
    {
        $contractor = $this->contractorModel->find($id);
        if (!$contractor) {
            return redirect()->to('/contractors')->with('error', 'Contractor not found.');
        }

        $rules = [
            'company_name'   => 'required|min_length[3]|max_length[255]',
            'specialization' => 'required',
            'contact_person' => 'required|min_length[2]|max_length[150]',
            'phone'          => 'required|min_length[7]|max_length[50]',
            'email'          => 'required|valid_email|max_length[150]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $updateData = [
            'company_name'   => trim($this->request->getPost('company_name')),
            'specialization' => trim($this->request->getPost('specialization')),
            'contact_person' => trim($this->request->getPost('contact_person')),
            'phone'          => trim($this->request->getPost('phone')),
            'email'          => trim($this->request->getPost('email')),
            'license_number' => trim($this->request->getPost('license_number') ?? ''),
            'gstin'          => trim($this->request->getPost('gstin') ?? ''),
            'rating'         => (float)$this->request->getPost('rating') ?: 5.0,
            'status'         => $this->request->getPost('status') ?: 'Active',
        ];

        $this->contractorModel->update($id, $updateData);

        AuditLogModel::record(
            'CONTRACTOR_UPDATED',
            'Contractors',
            $id,
            "Updated contractor {$contractor['contractor_code']}: {$updateData['company_name']}"
        );

        return redirect()->to('/contractors')->with('success', "Contractor {$contractor['contractor_code']} updated.");
    }

    public function delete(int $id)
    {
        $contractor = $this->contractorModel->find($id);
        if (!$contractor) {
            return redirect()->to('/contractors')->with('error', 'Contractor not found.');
        }

        $this->contractorModel->update($id, ['status' => 'Inactive']);

        AuditLogModel::record(
            'CONTRACTOR_DEACTIVATED',
            'Contractors',
            $id,
            "Deactivated contractor {$contractor['contractor_code']}"
        );

        return redirect()->to('/contractors')->with('success', "Contractor {$contractor['contractor_code']} set to Inactive.");
    }

    // =====================================================
    // 2. CONSTRUCTION WORK ORDERS & CONTRACTS
    // =====================================================
    public function workOrders()
    {
        $contractorId = (int)$this->request->getGet('contractor_id');
        $projectId    = (int)$this->request->getGet('project_id');

        $workOrders  = $this->workOrderModel->getWorkOrdersWithDetails($contractorId ?: null, $projectId ?: null);
        $contractors = $this->contractorModel->where('status', 'Active')->findAll();
        $projects    = $this->projectModel->findAll();
        $towers      = $this->towerModel->findAll();
        $milestones  = $this->milestoneModel->findAll();

        $data = [
            'title'              => 'Construction Work Orders & Contracts',
            'workOrders'         => $workOrders,
            'contractors'        => $contractors,
            'projects'           => $projects,
            'towers'             => $towers,
            'milestones'         => $milestones,
            'selectedContractor' => $contractorId,
            'selectedProject'    => $projectId,
        ];

        return view('contractors/work_orders', $data);
    }

    public function storeWorkOrder()
    {
        $rules = [
            'contractor_id'   => 'required|numeric',
            'project_id'      => 'required|numeric',
            'title'           => 'required|min_length[3]|max_length[255]',
            'scope_of_work'   => 'required|min_length[10]',
            'contract_amount' => 'required|numeric',
            'start_date'      => 'required|valid_date',
            'completion_date' => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $code   = $this->workOrderModel->generateWorkOrderCode();
        $userId = session()->get('user_id') ?: 1;

        $cwoData = [
            'work_order_code'      => $code,
            'contractor_id'        => (int)$this->request->getPost('contractor_id'),
            'project_id'           => (int)$this->request->getPost('project_id'),
            'tower_id'             => (int)$this->request->getPost('tower_id') ?: null,
            'milestone_id'         => (int)$this->request->getPost('milestone_id') ?: null,
            'title'                => trim($this->request->getPost('title')),
            'scope_of_work'        => trim($this->request->getPost('scope_of_work')),
            'contract_amount'      => (float)$this->request->getPost('contract_amount'),
            'retention_percentage' => (float)$this->request->getPost('retention_percentage') ?: 5.00,
            'start_date'           => $this->request->getPost('start_date'),
            'completion_date'      => $this->request->getPost('completion_date'),
            'payment_terms'        => trim($this->request->getPost('payment_terms') ?? ''),
            'status'               => $this->request->getPost('status') ?: 'Awarded',
            'created_by'           => $userId,
        ];

        $insertedId = $this->workOrderModel->insert($cwoData);

        AuditLogModel::record(
            'WORK_ORDER_AWARDED',
            'Contractors',
            $insertedId,
            "Awarded work order {$code}: {$cwoData['title']} for ₹{$cwoData['contract_amount']}"
        );

        return redirect()->to('/construction/work-orders')->with('success', "Work order {$code} awarded successfully.");
    }

    public function updateWorkOrderStatus(int $id)
    {
        $order = $this->workOrderModel->find($id);
        if (!$order) {
            return redirect()->to('/construction/work-orders')->with('error', 'Work order not found.');
        }

        $newStatus = trim($this->request->getPost('status'));
        $this->workOrderModel->update($id, ['status' => $newStatus]);

        AuditLogModel::record(
            'WORK_ORDER_STATUS_CHANGED',
            'Contractors',
            $id,
            "Changed work order {$order['work_order_code']} status to {$newStatus}"
        );

        return redirect()->to('/construction/work-orders')->with('success', "Work order {$order['work_order_code']} status updated to {$newStatus}.");
    }
}
