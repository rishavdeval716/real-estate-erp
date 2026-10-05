<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MaterialRequisitionModel;
use App\Models\ProjectModel;
use App\Models\ProjectTowerModel;
use App\Models\AuditLogModel;

class ProcurementController extends BaseController
{
    protected $reqModel;
    protected $projectModel;
    protected $towerModel;

    public function __construct()
    {
        $this->reqModel     = new MaterialRequisitionModel();
        $this->projectModel = new ProjectModel();
        $this->towerModel   = new ProjectTowerModel();
    }

    public function index()
    {
        $projectId = (int)$this->request->getGet('project_id');
        $status    = trim($this->request->getGet('status') ?? '');

        $requisitions = $this->reqModel->getRequisitionsWithDetails($projectId ?: null, $status ?: null);
        $projects     = $this->projectModel->findAll();

        $data = [
            'title'           => 'Material Requisitions & Procurement',
            'requisitions'    => $requisitions,
            'projects'        => $projects,
            'selectedProject' => $projectId,
            'selectedStatus'  => $status,
        ];

        return view('procurement/index', $data);
    }

    public function create()
    {
        $projects = $this->projectModel->findAll();
        $towers   = $this->towerModel->findAll();

        $data = [
            'title'    => 'Create Material Indent / Requisition',
            'projects' => $projects,
            'towers'   => $towers,
        ];

        return view('procurement/create', $data);
    }

    public function store()
    {
        $rules = [
            'project_id'          => 'required|numeric',
            'item_name'           => 'required|min_length[3]|max_length[255]',
            'category'            => 'required',
            'quantity'            => 'required|numeric',
            'unit_of_measure'     => 'required',
            'estimated_unit_cost' => 'required|numeric',
            'required_by_date'    => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $qty      = (float)$this->request->getPost('quantity');
        $unitCost = (float)$this->request->getPost('estimated_unit_cost');
        $total    = $qty * $unitCost;
        $code     = $this->reqModel->generateRequisitionCode();
        $userId   = session()->get('user_id') ?: 1;

        $reqData = [
            'requisition_code'     => $code,
            'project_id'           => (int)$this->request->getPost('project_id'),
            'tower_id'             => (int)$this->request->getPost('tower_id') ?: null,
            'item_name'            => trim($this->request->getPost('item_name')),
            'category'             => trim($this->request->getPost('category')),
            'quantity'             => $qty,
            'unit_of_measure'      => trim($this->request->getPost('unit_of_measure')),
            'estimated_unit_cost'  => $unitCost,
            'estimated_total_cost' => $total,
            'required_by_date'     => $this->request->getPost('required_by_date'),
            'priority'             => $this->request->getPost('priority') ?: 'Medium',
            'requested_by'         => $userId,
            'status'               => 'Requested',
            'remarks'              => trim($this->request->getPost('remarks') ?? ''),
        ];

        $insertedId = $this->reqModel->insert($reqData);

        AuditLogModel::record(
            'MATERIAL_REQUISITION_CREATED',
            'Procurement',
            $insertedId,
            "Created material requisition {$code}: {$reqData['item_name']} (Total: ₹{$total})"
        );

        return redirect()->to('/procurement')->with('success', "Material requisition {$code} submitted successfully.");
    }

    public function approve(int $id)
    {
        $req = $this->reqModel->find($id);
        if (!$req) {
            return redirect()->to('/procurement')->with('error', 'Requisition not found.');
        }

        $this->reqModel->update($id, ['status' => 'Approved']);

        AuditLogModel::record(
            'MATERIAL_REQUISITION_APPROVED',
            'Procurement',
            $id,
            "Approved material requisition {$req['requisition_code']}"
        );

        return redirect()->to('/procurement')->with('success', "Requisition {$req['requisition_code']} approved.");
    }

    public function procure(int $id)
    {
        $req = $this->reqModel->find($id);
        if (!$req) {
            return redirect()->to('/procurement')->with('error', 'Requisition not found.');
        }

        $this->reqModel->update($id, ['status' => 'Procured']);

        AuditLogModel::record(
            'MATERIAL_PROCURED',
            'Procurement',
            $id,
            "Marked material requisition {$req['requisition_code']} as procured"
        );

        return redirect()->to('/procurement')->with('success', "Requisition {$req['requisition_code']} marked as Procured.");
    }
}
