<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProjectModel;
use App\Models\ProjectTowerModel;
use App\Models\LocationModel;
use App\Models\PropertyModel;
use App\Models\PropertyUnitModel;
use App\Models\AuditLogModel;

class ProjectController extends BaseController
{
    protected $projectModel;
    protected $towerModel;
    protected $locationModel;

    public function __construct()
    {
        $this->projectModel  = new ProjectModel();
        $this->towerModel    = new ProjectTowerModel();
        $this->locationModel = new LocationModel();
    }

    /**
     * List all projects with search, filter, and dynamic unit counts
     */
    public function index()
    {
        $search             = trim($this->request->getGet('search') ?? '');
        $locationId         = (int)($this->request->getGet('location_id') ?? 0) ?: null;
        $constructionStatus = trim($this->request->getGet('construction_status') ?? '');
        $status             = trim($this->request->getGet('status') ?? '');

        $perPage  = 10;
        $projects = $this->projectModel->getFilteredProjects($search, $locationId, $constructionStatus, $status, $perPage);
        $pager    = $this->projectModel->pager;

        // Sync and load dynamic counts for each project
        foreach ($projects as &$p) {
            $counts = $this->projectModel->getInventoryCounts($p['id']);
            $p['dynamic_total_units']     = $counts['total'];
            $p['dynamic_available_units'] = $counts['available'];
            $p['tower_count']             = $this->towerModel->where('project_id', $p['id'])->countAllResults();
        }

        $locations = $this->locationModel->where('status', 'active')->orderBy('city', 'ASC')->findAll();

        $data = [
            'title'              => 'Project Management - Real Estate ERP',
            'projects'           => $projects,
            'pager'              => $pager,
            'search'             => $search,
            'locationId'         => $locationId,
            'constructionStatus' => $constructionStatus,
            'status'             => $status,
            'locations'          => $locations,
        ];

        return view('projects/index', $data);
    }

    /**
     * Show create project form
     */
    public function create()
    {
        $locations = $this->locationModel->where('status', 'active')->orderBy('city', 'ASC')->findAll();

        $data = [
            'title'     => 'Create New Project - Real Estate ERP',
            'locations' => $locations,
        ];

        return view('projects/create', $data);
    }

    /**
     * Store new project
     */
    public function store()
    {
        $rules = [
            'project_code'        => 'required|min_length[2]|max_length[50]|is_unique[projects.project_code]',
            'name'                => 'required|min_length[3]|max_length[150]',
            'builder_developer'   => 'required|min_length[2]|max_length[150]',
            'location_id'         => 'required|is_not_unique[locations.id]',
            'construction_status' => 'required|in_list[Pre-Launch,Under Construction,Ready to Move,Completed,On Hold]',
            'possession_date'     => 'permit_empty|valid_date',
            'status'              => 'required|in_list[active,inactive,completed,archived]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $projectData = [
            'project_code'        => strtoupper(trim($this->request->getPost('project_code'))),
            'name'                => trim($this->request->getPost('name')),
            'description'         => trim($this->request->getPost('description') ?? ''),
            'builder_developer'   => trim($this->request->getPost('builder_developer')),
            'location_id'         => (int)$this->request->getPost('location_id'),
            'construction_status' => $this->request->getPost('construction_status'),
            'possession_date'     => $this->request->getPost('possession_date') ?: null,
            'total_units'         => 0,
            'available_units'     => 0,
            'status'              => $this->request->getPost('status'),
        ];

        $projectId = $this->projectModel->insert($projectData);

        if (!$projectId) {
            return redirect()->back()->withInput()->with('error', 'Failed to create project.');
        }

        AuditLogModel::record(
            'PROJECT_CREATED',
            'Projects',
            (int)$projectId,
            "Created project '{$projectData['name']}' ({$projectData['project_code']})"
        );

        return redirect()->to('/projects')->with('success', "Project '{$projectData['name']}' created successfully.");
    }

    /**
     * View project details with towers and inventory cards
     */
    public function show($id)
    {
        $id = (int)$id;
        $project = $this->projectModel->getProjectWithLocation($id);

        if (!$project) {
            return redirect()->to('/projects')->with('error', 'Project not found.');
        }

        // Dynamic inventory counts from property_units
        $inventory = $this->projectModel->getInventoryCounts($id);

        // Towers
        $towers = $this->towerModel->getTowersForProject($id);

        // Properties under this project
        $propertyModel = new PropertyModel();
        $properties    = $propertyModel->select('properties.*, property_types.name as property_type_name')
                                       ->join('property_types', 'property_types.id = properties.property_type_id', 'left')
                                       ->where('properties.project_id', $id)
                                       ->findAll();

        // Property Units under this project
        $unitModel = new PropertyUnitModel();
        $units     = $unitModel->select('property_units.*, project_towers.tower_name')
                               ->join('project_towers', 'project_towers.id = property_units.tower_id', 'left')
                               ->where('property_units.project_id', $id)
                               ->orderBy('property_units.floor', 'DESC')
                               ->findAll();

        $data = [
            'title'      => "Project: {$project['name']} - Real Estate ERP",
            'project'    => $project,
            'inventory'  => $inventory,
            'towers'     => $towers,
            'properties' => $properties,
            'units'      => $units,
        ];

        return view('projects/view', $data);
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $id = (int)$id;
        $project = $this->projectModel->find($id);

        if (!$project) {
            return redirect()->to('/projects')->with('error', 'Project not found.');
        }

        $locations = $this->locationModel->where('status', 'active')->orderBy('city', 'ASC')->findAll();

        $data = [
            'title'     => "Edit Project: {$project['name']} - Real Estate ERP",
            'project'   => $project,
            'locations' => $locations,
        ];

        return view('projects/edit', $data);
    }

    /**
     * Update project
     */
    public function update($id)
    {
        $id = (int)$id;
        $project = $this->projectModel->find($id);

        if (!$project) {
            return redirect()->to('/projects')->with('error', 'Project not found.');
        }

        $rules = [
            'project_code'        => "required|min_length[2]|max_length[50]|is_unique[projects.project_code,id,{$id}]",
            'name'                => 'required|min_length[3]|max_length[150]',
            'builder_developer'   => 'required|min_length[2]|max_length[150]',
            'location_id'         => 'required|is_not_unique[locations.id]',
            'construction_status' => 'required|in_list[Pre-Launch,Under Construction,Ready to Move,Completed,On Hold]',
            'possession_date'     => 'permit_empty|valid_date',
            'status'              => 'required|in_list[active,inactive,completed,archived]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $projectData = [
            'project_code'        => strtoupper(trim($this->request->getPost('project_code'))),
            'name'                => trim($this->request->getPost('name')),
            'description'         => trim($this->request->getPost('description') ?? ''),
            'builder_developer'   => trim($this->request->getPost('builder_developer')),
            'location_id'         => (int)$this->request->getPost('location_id'),
            'construction_status' => $this->request->getPost('construction_status'),
            'possession_date'     => $this->request->getPost('possession_date') ?: null,
            'status'              => $this->request->getPost('status'),
        ];

        $this->projectModel->update($id, $projectData);

        AuditLogModel::record(
            'PROJECT_UPDATED',
            'Projects',
            $id,
            "Updated project '{$projectData['name']}' ({$projectData['project_code']})"
        );

        return redirect()->to("/projects/view/{$id}")->with('success', "Project '{$projectData['name']}' updated successfully.");
    }

    /**
     * Soft delete project
     */
    public function delete($id)
    {
        $id = (int)$id;
        $project = $this->projectModel->find($id);

        if (!$project) {
            return redirect()->to('/projects')->with('error', 'Project not found.');
        }

        // Business rule: Check if active units exist
        $unitModel = new PropertyUnitModel();
        $unitCount = $unitModel->where('project_id', $id)->countAllResults();

        if ($unitCount > 0) {
            return redirect()->to('/projects')->with('error', "Cannot delete project. It has {$unitCount} registered units. Archive or remove units first.");
        }

        $this->projectModel->delete($id);

        AuditLogModel::record(
            'PROJECT_DELETED',
            'Projects',
            $id,
            "Deleted project '{$project['name']}'"
        );

        return redirect()->to('/projects')->with('success', "Project '{$project['name']}' has been removed.");
    }

    /**
     * Add a Tower to a project
     */
    public function addTower($projectId)
    {
        $projectId = (int)$projectId;
        $project = $this->projectModel->find($projectId);

        if (!$project) {
            return redirect()->to('/projects')->with('error', 'Project not found.');
        }

        $rules = [
            'tower_name'       => 'required|min_length[1]|max_length[100]',
            'tower_code'       => 'required|min_length[1]|max_length[50]',
            'number_of_floors' => 'required|is_natural_no_zero',
            'total_units'      => 'permit_empty|is_natural',
            'status'           => 'required|in_list[active,inactive]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $towerData = [
            'project_id'       => $projectId,
            'tower_name'       => trim($this->request->getPost('tower_name')),
            'tower_code'       => strtoupper(trim($this->request->getPost('tower_code'))),
            'number_of_floors' => (int)$this->request->getPost('number_of_floors'),
            'total_units'      => (int)($this->request->getPost('total_units') ?? 0),
            'status'           => $this->request->getPost('status'),
        ];

        // Check unique tower code within project
        $exists = $this->towerModel->where(['project_id' => $projectId, 'tower_code' => $towerData['tower_code']])->countAllResults();
        if ($exists > 0) {
            return redirect()->back()->withInput()->with('error', "Tower code '{$towerData['tower_code']}' already exists in this project.");
        }

        $towerId = $this->towerModel->insert($towerData);

        AuditLogModel::record(
            'TOWER_CREATED',
            'Projects',
            $projectId,
            "Added tower {$towerData['tower_name']} ({$towerData['tower_code']}) to project {$project['name']}"
        );

        return redirect()->to("/projects/view/{$projectId}")->with('success', "Tower '{$towerData['tower_name']}' added successfully.");
    }

    /**
     * Delete a tower
     */
    public function deleteTower($towerId)
    {
        $towerId = (int)$towerId;
        $tower = $this->towerModel->find($towerId);

        if (!$tower) {
            return redirect()->to('/projects')->with('error', 'Tower not found.');
        }

        $projectId = $tower['project_id'];

        // Check if units assigned
        $unitModel = new PropertyUnitModel();
        $unitCount = $unitModel->where('tower_id', $towerId)->countAllResults();
        if ($unitCount > 0) {
            return redirect()->to("/projects/view/{$projectId}")->with('error', "Cannot delete tower with {$unitCount} assigned units.");
        }

        $this->towerModel->delete($towerId);

        AuditLogModel::record(
            'TOWER_DELETED',
            'Projects',
            $projectId,
            "Removed tower {$tower['tower_name']} ({$tower['tower_code']})"
        );

        return redirect()->to("/projects/view/{$projectId}")->with('success', "Tower '{$tower['tower_name']}' removed.");
    }
}
