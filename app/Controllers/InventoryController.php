<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProjectModel;
use App\Models\ProjectTowerModel;
use App\Models\PropertyUnitModel;
use App\Libraries\PropertyStatus;

class InventoryController extends BaseController
{
    protected $projectModel;
    protected $towerModel;
    protected $unitModel;

    public function __construct()
    {
        $this->projectModel = new ProjectModel();
        $this->towerModel   = new ProjectTowerModel();
        $this->unitModel    = new PropertyUnitModel();
    }

    /**
     * Project Unit Inventory Overview & Dynamic Floor/Tower Matrix
     */
    public function index()
    {
        $projects  = $this->projectModel->where('status !=', 'archived')->orderBy('name', 'ASC')->findAll();
        $projectId = (int)($this->request->getGet('project_id') ?? 0);

        // If no project selected, pick first project
        if (!$projectId && !empty($projects)) {
            $projectId = (int)$projects[0]['id'];
        }

        $towerId = (int)($this->request->getGet('tower_id') ?? 0) ?: null;

        $selectedProject = null;
        $towers          = [];
        $inventoryCounts = [
            'total'             => 0,
            'available'         => 0,
            'reserved'          => 0,
            'under_negotiation' => 0,
            'booked'            => 0,
            'sold'              => 0,
            'rented'            => 0,
            'under_maintenance' => 0,
            'unavailable'       => 0,
        ];
        $floorWiseUnits  = [];

        if ($projectId) {
            $selectedProject = $this->projectModel->getProjectWithLocation($projectId);
            $towers          = $this->towerModel->getTowersForProject($projectId);
            $inventoryCounts = $this->projectModel->getInventoryCounts($projectId);
            $floorWiseUnits  = $this->unitModel->getFloorWiseInventory($projectId, $towerId);
        }

        $data = [
            'title'           => 'Project Unit Inventory Management - Real Estate ERP',
            'projects'        => $projects,
            'projectId'       => $projectId,
            'selectedProject' => $selectedProject,
            'towers'          => $towers,
            'towerId'         => $towerId,
            'inventory'       => $inventoryCounts,
            'floorWiseUnits'  => $floorWiseUnits,
        ];

        return view('inventory/index', $data);
    }
}
