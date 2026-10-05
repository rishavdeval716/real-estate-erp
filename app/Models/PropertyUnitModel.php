<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\PropertyStatus;

class PropertyUnitModel extends Model
{
    protected $table            = 'property_units';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'property_id',
        'project_id',
        'tower_id',
        'unit_number',
        'floor',
        'flat_type',
        'carpet_area',
        'built_up_area',
        'balcony',
        'parking',
        'facing',
        'unit_price',
        'availability_status',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
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
        'unit_price'          => 'required|numeric|greater_than_equal_to[0]',
        'availability_status' => 'required|in_list[Available,Reserved,Under Negotiation,Booked,Sold,Rented,Under Maintenance,Unavailable]',
    ];

    /**
     * Check if unit number already exists in this project and tower
     */
    public function isUnitDuplicate(string $unitNumber, int $projectId, ?int $towerId, ?int $excludeId = null): bool
    {
        $builder = $this->where('project_id', $projectId)
                        ->where('unit_number', $unitNumber);

        if ($towerId !== null) {
            $builder->where('tower_id', $towerId);
        } else {
            $builder->where('tower_id', null);
        }

        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Get filtered units list with joined project, tower, and property details
     */
    public function getFilteredUnits(
        ?string $search = null,
        ?int $projectId = null,
        ?int $towerId = null,
        ?string $status = null,
        ?string $flatType = null,
        ?int $floor = null,
        int $perPage = 15
    ) {
        $builder = $this->select('
            property_units.*,
            projects.name as project_name,
            projects.project_code,
            project_towers.tower_name,
            project_towers.tower_code,
            properties.title as property_title
        ')
        ->join('projects', 'projects.id = property_units.project_id', 'left')
        ->join('project_towers', 'project_towers.id = property_units.tower_id', 'left')
        ->join('properties', 'properties.id = property_units.property_id', 'left');

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('property_units.unit_number', $search)
                    ->orLike('property_units.flat_type', $search)
                    ->orLike('projects.name', $search)
                    ->orLike('project_towers.tower_name', $search)
                    ->groupEnd();
        }

        if (!empty($projectId)) {
            $builder->where('property_units.project_id', $projectId);
        }

        if (!empty($towerId)) {
            $builder->where('property_units.tower_id', $towerId);
        }

        if (!empty($status) && PropertyStatus::isValid($status)) {
            $builder->where('property_units.availability_status', $status);
        }

        if (!empty($flatType)) {
            $builder->where('property_units.flat_type', $flatType);
        }

        if ($floor !== null && $floor !== '') {
            $builder->where('property_units.floor', (int)$floor);
        }

        return $builder->orderBy('property_units.id', 'DESC')->paginate($perPage);
    }

    /**
     * Get single unit with full project and tower context
     */
    public function getUnitFull(int $id)
    {
        return $this->select('
            property_units.*,
            projects.name as project_name,
            projects.project_code,
            projects.builder_developer,
            project_towers.tower_name,
            project_towers.tower_code,
            properties.title as property_title,
            properties.property_code as property_code_ref
        ')
        ->join('projects', 'projects.id = property_units.project_id', 'left')
        ->join('project_towers', 'project_towers.id = property_units.tower_id', 'left')
        ->join('properties', 'properties.id = property_units.property_id', 'left')
        ->where('property_units.id', $id)
        ->first();
    }

    /**
     * Get units grouped by floor for a project / tower
     */
    public function getFloorWiseInventory(int $projectId, ?int $towerId = null): array
    {
        $builder = $this->where('project_id', $projectId);
        if ($towerId) {
            $builder->where('tower_id', $towerId);
        }

        $units = $builder->orderBy('floor', 'DESC')
                         ->orderBy('unit_number', 'ASC')
                         ->findAll();

        $grouped = [];
        foreach ($units as $unit) {
            $floor = $unit['floor'];
            if (!isset($grouped[$floor])) {
                $grouped[$floor] = [];
            }
            $grouped[$floor][] = $unit;
        }

        return $grouped;
    }
}
