<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PropertyUnitModel;
use App\Models\ProjectModel;
use App\Models\ProjectTowerModel;
use App\Models\PropertyModel;
use App\Models\PropertyStatusHistoryModel;
use App\Models\AuditLogModel;
use App\Libraries\PropertyStatus;

class PropertyUnitController extends BaseController
{
    protected $unitModel;
    protected $projectModel;
    protected $towerModel;
    protected $propertyModel;
    protected $statusHistoryModel;

    public function __construct()
    {
        $this->unitModel          = new PropertyUnitModel();
        $this->projectModel       = new ProjectModel();
        $this->towerModel         = new ProjectTowerModel();
        $this->propertyModel      = new PropertyModel();
        $this->statusHistoryModel = new PropertyStatusHistoryModel();
    }

    /**
     * List all units with search, project, tower, and status filtering
     */
    public function index()
    {
        $search    = trim($this->request->getGet('search') ?? '');
        $projectId = (int)($this->request->getGet('project_id') ?? 0) ?: null;
        $towerId   = (int)($this->request->getGet('tower_id') ?? 0) ?: null;
        $status    = trim($this->request->getGet('status') ?? '');
        $flatType  = trim($this->request->getGet('flat_type') ?? '');
        $floor     = $this->request->getGet('floor') !== null && $this->request->getGet('floor') !== '' ? (int)$this->request->getGet('floor') : null;

        $perPage = 15;
        $units   = $this->unitModel->getFilteredUnits($search, $projectId, $towerId, $status, $flatType, $floor, $perPage);
        $pager   = $this->unitModel->pager;

        $projects = $this->projectModel->where('status !=', 'archived')->orderBy('name', 'ASC')->findAll();
        $towers   = $projectId ? $this->towerModel->where('project_id', $projectId)->findAll() : [];
        $statuses = PropertyStatus::all();

        $data = [
            'title'     => 'Property Units - Real Estate ERP',
            'units'     => $units,
            'pager'     => $pager,
            'search'    => $search,
            'projectId' => $projectId,
            'towerId'   => $towerId,
            'status'    => $status,
            'flatType'  => $flatType,
            'floor'     => $floor,
            'projects'  => $projects,
            'towers'    => $towers,
            'statuses'  => $statuses,
        ];

        return view('property_units/index', $data);
    }

    /**
     * Show create unit form
     */
    public function create()
    {
        $projects   = $this->projectModel->where('status !=', 'archived')->orderBy('name', 'ASC')->findAll();
        $properties = $this->propertyModel->orderBy('title', 'ASC')->findAll();
        $statuses   = PropertyStatus::all();

        $projectId = (int)($this->request->getGet('project_id') ?? 0) ?: null;
        $towers    = $projectId ? $this->towerModel->where('project_id', $projectId)->findAll() : [];

        $data = [
            'title'      => 'Create New Unit - Real Estate ERP',
            'projects'   => $projects,
            'properties' => $properties,
            'statuses'   => $statuses,
            'projectId'  => $projectId,
            'towers'     => $towers,
        ];

        return view('property_units/create', $data);
    }

    /**
     * Store new unit
     */
    public function store()
    {
        $rules = [
            'project_id'          => 'required|is_not_unique[projects.id]',
            'tower_id'            => 'permit_empty|is_not_unique[project_towers.id]',
            'property_id'         => 'permit_empty|is_not_unique[properties.id]',
            'unit_number'         => 'required|min_length[1]|max_length[50]',
            'floor'               => 'required|integer',
            'flat_type'           => 'required|min_length[2]|max_length[50]',
            'carpet_area'         => 'required|numeric|greater_than[0]',
            'built_up_area'       => 'required|numeric|greater_than[0]',
            'balcony'             => 'permit_empty|is_natural',
            'parking'             => 'permit_empty|is_natural',
            'facing'              => 'permit_empty|max_length[50]',
            'unit_price'          => 'required|numeric|greater_than_equal_to[0]',
            'availability_status' => 'required|in_list[Available,Reserved,Under Negotiation,Booked,Sold,Rented,Under Maintenance,Unavailable]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $projectId  = (int)$this->request->getPost('project_id');
        $towerId    = $this->request->getPost('tower_id') ? (int)$this->request->getPost('tower_id') : null;
        $unitNumber = trim($this->request->getPost('unit_number'));

        // Check uniqueness within project & tower
        if ($this->unitModel->isUnitDuplicate($unitNumber, $projectId, $towerId)) {
            return redirect()->back()->withInput()->with('error', "Unit '{$unitNumber}' already exists in this project and tower.");
        }

        $unitData = [
            'project_id'          => $projectId,
            'tower_id'            => $towerId,
            'property_id'         => $this->request->getPost('property_id') ? (int)$this->request->getPost('property_id') : null,
            'unit_number'         => $unitNumber,
            'floor'               => (int)$this->request->getPost('floor'),
            'flat_type'           => trim($this->request->getPost('flat_type')),
            'carpet_area'         => (float)$this->request->getPost('carpet_area'),
            'built_up_area'       => (float)$this->request->getPost('built_up_area'),
            'balcony'             => (int)($this->request->getPost('balcony') ?? 0),
            'parking'             => (int)($this->request->getPost('parking') ?? 0),
            'facing'              => trim($this->request->getPost('facing') ?? ''),
            'unit_price'          => (float)$this->request->getPost('unit_price'),
            'availability_status' => $this->request->getPost('availability_status'),
        ];

        $unitId = $this->unitModel->insert($unitData);

        if (!$unitId) {
            return redirect()->back()->withInput()->with('error', 'Failed to save property unit.');
        }

        // Sync project counts
        $this->projectModel->syncUnitCounts($projectId);

        // Record status history if property is linked
        $propertyId = $unitData['property_id'];
        $userId     = session()->get('user_id');
        if ($propertyId) {
            $this->statusHistoryModel->recordChange(
                $propertyId,
                (int)$unitId,
                'None',
                $unitData['availability_status'],
                $userId,
                "Unit {$unitNumber} registered"
            );
        }

        AuditLogModel::record(
            'UNIT_CREATED',
            'Units',
            (int)$unitId,
            "Created unit {$unitNumber} ({$unitData['flat_type']}) in project ID {$projectId}"
        );

        return redirect()->to('/units')->with('success', "Unit '{$unitNumber}' created successfully.");
    }

    /**
     * Show single unit view
     */
    public function show($id)
    {
        $id = (int)$id;
        $unit = $this->unitModel->getUnitFull($id);

        if (!$unit) {
            return redirect()->to('/units')->with('error', 'Unit not found.');
        }

        // Status history for this unit
        $statusHistory = [];
        if ($unit['property_id']) {
            $statusHistory = $this->statusHistoryModel->getHistoryForEntity($unit['property_id'], $id);
        }

        $statuses = PropertyStatus::all();

        $data = [
            'title'         => "Unit: {$unit['unit_number']} - Real Estate ERP",
            'unit'          => $unit,
            'statusHistory' => $statusHistory,
            'statuses'      => $statuses,
        ];

        return view('property_units/view', $data);
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $id = (int)$id;
        $unit = $this->unitModel->find($id);

        if (!$unit) {
            return redirect()->to('/units')->with('error', 'Unit not found.');
        }

        $projects   = $this->projectModel->where('status !=', 'archived')->orderBy('name', 'ASC')->findAll();
        $towers     = $this->towerModel->where('project_id', $unit['project_id'])->findAll();
        $properties = $this->propertyModel->orderBy('title', 'ASC')->findAll();
        $statuses   = PropertyStatus::all();

        $data = [
            'title'      => "Edit Unit: {$unit['unit_number']} - Real Estate ERP",
            'unit'       => $unit,
            'projects'   => $projects,
            'towers'     => $towers,
            'properties' => $properties,
            'statuses'   => $statuses,
        ];

        return view('property_units/edit', $data);
    }

    /**
     * Update unit
     */
    public function update($id)
    {
        $id = (int)$id;
        $unit = $this->unitModel->find($id);

        if (!$unit) {
            return redirect()->to('/units')->with('error', 'Unit not found.');
        }

        $rules = [
            'project_id'          => 'required|is_not_unique[projects.id]',
            'tower_id'            => 'permit_empty|is_not_unique[project_towers.id]',
            'property_id'         => 'permit_empty|is_not_unique[properties.id]',
            'unit_number'         => 'required|min_length[1]|max_length[50]',
            'floor'               => 'required|integer',
            'flat_type'           => 'required|min_length[2]|max_length[50]',
            'carpet_area'         => 'required|numeric|greater_than[0]',
            'built_up_area'       => 'required|numeric|greater_than[0]',
            'balcony'             => 'permit_empty|is_natural',
            'parking'             => 'permit_empty|is_natural',
            'facing'              => 'permit_empty|max_length[50]',
            'unit_price'          => 'required|numeric|greater_than_equal_to[0]',
            'availability_status' => 'required|in_list[Available,Reserved,Under Negotiation,Booked,Sold,Rented,Under Maintenance,Unavailable]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $projectId  = (int)$this->request->getPost('project_id');
        $towerId    = $this->request->getPost('tower_id') ? (int)$this->request->getPost('tower_id') : null;
        $unitNumber = trim($this->request->getPost('unit_number'));

        // Check duplicate excluding current
        if ($this->unitModel->isUnitDuplicate($unitNumber, $projectId, $towerId, $id)) {
            return redirect()->back()->withInput()->with('error', "Unit '{$unitNumber}' already exists in this project and tower.");
        }

        $oldStatus = $unit['availability_status'];
        $newStatus = $this->request->getPost('availability_status');

        $unitData = [
            'project_id'          => $projectId,
            'tower_id'            => $towerId,
            'property_id'         => $this->request->getPost('property_id') ? (int)$this->request->getPost('property_id') : null,
            'unit_number'         => $unitNumber,
            'floor'               => (int)$this->request->getPost('floor'),
            'flat_type'           => trim($this->request->getPost('flat_type')),
            'carpet_area'         => (float)$this->request->getPost('carpet_area'),
            'built_up_area'       => (float)$this->request->getPost('built_up_area'),
            'balcony'             => (int)($this->request->getPost('balcony') ?? 0),
            'parking'             => (int)($this->request->getPost('parking') ?? 0),
            'facing'              => trim($this->request->getPost('facing') ?? ''),
            'unit_price'          => (float)$this->request->getPost('unit_price'),
            'availability_status' => $newStatus,
        ];

        $this->unitModel->update($id, $unitData);

        // Sync project counts
        $this->projectModel->syncUnitCounts($projectId);

        // If status changed, record in history
        if ($oldStatus !== $newStatus && !empty($unitData['property_id'])) {
            $userId = session()->get('user_id');
            $this->statusHistoryModel->recordChange(
                $unitData['property_id'],
                $id,
                $oldStatus,
                $newStatus,
                $userId,
                'Unit status modified in unit edit form'
            );
        }

        AuditLogModel::record(
            'UNIT_UPDATED',
            'Units',
            $id,
            "Updated unit {$unitNumber} ({$unitData['flat_type']})"
        );

        return redirect()->to("/units/view/{$id}")->with('success', "Unit '{$unitNumber}' updated successfully.");
    }

    /**
     * Soft delete unit
     */
    public function delete($id)
    {
        $id = (int)$id;
        $unit = $this->unitModel->find($id);

        if (!$unit) {
            return redirect()->to('/units')->with('error', 'Unit not found.');
        }

        $projectId = $unit['project_id'];
        $this->unitModel->delete($id);

        // Sync project counts
        $this->projectModel->syncUnitCounts($projectId);

        AuditLogModel::record(
            'UNIT_DELETED',
            'Units',
            $id,
            "Deleted unit '{$unit['unit_number']}'"
        );

        return redirect()->to('/units')->with('success', "Unit '{$unit['unit_number']}' removed.");
    }
}
