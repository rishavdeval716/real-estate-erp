<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SiteInspectionModel;
use App\Models\ProjectModel;
use App\Models\ProjectTowerModel;
use App\Models\PropertyUnitModel;
use App\Models\AuditLogModel;

class SiteInspectionController extends BaseController
{
    protected $inspectionModel;
    protected $projectModel;
    protected $towerModel;
    protected $unitModel;

    public function __construct()
    {
        $this->inspectionModel = new SiteInspectionModel();
        $this->projectModel    = new ProjectModel();
        $this->towerModel      = new ProjectTowerModel();
        $this->unitModel       = new PropertyUnitModel();
    }

    public function index()
    {
        $projectId = (int)$this->request->getGet('project_id');
        $result    = trim($this->request->getGet('result') ?? '');

        $inspections = $this->inspectionModel->getInspectionsWithDetails($projectId ?: null, $result ?: null);
        $projects    = $this->projectModel->findAll();

        $data = [
            'title'           => 'Site Quality & Safety Inspections',
            'inspections'     => $inspections,
            'projects'        => $projects,
            'selectedProject' => $projectId,
            'selectedResult'  => $result,
        ];

        return view('inspections/index', $data);
    }

    public function create()
    {
        $projects = $this->projectModel->findAll();
        $towers   = $this->towerModel->findAll();
        $units    = $this->unitModel->findAll();

        $data = [
            'title'    => 'Schedule / Conduct Quality Inspection Check',
            'projects' => $projects,
            'towers'   => $towers,
            'units'    => $units,
            'today'    => date('Y-m-d'),
        ];

        return view('inspections/create', $data);
    }

    public function store()
    {
        $rules = [
            'project_id'      => 'required|numeric',
            'inspection_type' => 'required',
            'inspection_date' => 'required|valid_date',
            'result'          => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $code   = $this->inspectionModel->generateInspectionCode();
        $userId = session()->get('user_id') ?: 1;
        $result = trim($this->request->getPost('result'));
        $snags  = (int)$this->request->getPost('snags_found');

        $status = 'Completed';
        if ($result === 'Failed' || $snags > 0) {
            $status = 'Action Required';
        }

        $inspData = [
            'inspection_code'        => $code,
            'project_id'             => (int)$this->request->getPost('project_id'),
            'tower_id'               => (int)$this->request->getPost('tower_id') ?: null,
            'unit_id'                => (int)$this->request->getPost('unit_id') ?: null,
            'inspection_type'        => trim($this->request->getPost('inspection_type')),
            'inspection_date'        => $this->request->getPost('inspection_date'),
            'inspector_id'           => $userId,
            'result'                 => $result,
            'snags_found'            => $snags,
            'snag_details'           => trim($this->request->getPost('snag_details') ?? ''),
            'rectification_deadline' => $this->request->getPost('rectification_deadline') ?: null,
            'remarks'                => trim($this->request->getPost('remarks') ?? ''),
            'status'                 => $status,
        ];

        $insertedId = $this->inspectionModel->insert($inspData);

        AuditLogModel::record(
            'SITE_INSPECTION_CONDUCTED',
            'Inspections',
            $insertedId,
            "Conducted inspection {$code} ({$inspData['inspection_type']}) - Result: {$result}"
        );

        return redirect()->to('/inspections')->with('success', "Inspection {$code} recorded successfully.");
    }

    public function rectify(int $id)
    {
        $insp = $this->inspectionModel->find($id);
        if (!$insp) {
            return redirect()->to('/inspections')->with('error', 'Inspection not found.');
        }

        $this->inspectionModel->update($id, [
            'status'  => 'Rectified & Closed',
            'remarks' => trim(($insp['remarks'] ?? '') . "\n[Rectified on " . date('Y-m-d H:i') . "]"),
        ]);

        AuditLogModel::record(
            'INSPECTION_RECTIFIED',
            'Inspections',
            $id,
            "Rectified and closed inspection snags for {$insp['inspection_code']}"
        );

        return redirect()->to('/inspections')->with('success', "Inspection {$insp['inspection_code']} snags marked as Rectified & Closed.");
    }
}
