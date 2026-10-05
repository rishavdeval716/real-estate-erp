<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectModel extends Model
{
    protected $table            = 'projects';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'project_code',
        'name',
        'description',
        'builder_developer',
        'location_id',
        'construction_status',
        'possession_date',
        'total_units',
        'available_units',
        'status',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'project_code'        => 'required|min_length[2]|max_length[50]|is_unique[projects.project_code,id,{id}]',
        'name'                => 'required|min_length[3]|max_length[150]',
        'builder_developer'   => 'required|min_length[2]|max_length[150]',
        'location_id'         => 'required|is_not_unique[locations.id]',
        'construction_status' => 'required|in_list[Pre-Launch,Under Construction,Ready to Move,Completed,On Hold]',
        'possession_date'     => 'permit_empty|valid_date',
        'status'              => 'required|in_list[active,inactive,completed,archived]',
    ];

    protected $validationMessages = [
        'project_code' => [
            'is_unique' => 'This project code is already taken. Please enter a unique code.',
        ],
        'location_id' => [
            'is_not_unique' => 'Please select a valid location from the list.',
        ],
    ];

    /**
     * Get filtered projects with location info and dynamic unit counts
     */
    public function getFilteredProjects(
        ?string $search = null,
        ?int $locationId = null,
        ?string $constructionStatus = null,
        ?string $status = null,
        int $perPage = 10
    ) {
        $builder = $this->select('projects.*, locations.area, locations.city, locations.state')
                        ->join('locations', 'locations.id = projects.location_id', 'left');

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('projects.name', $search)
                    ->orLike('projects.project_code', $search)
                    ->orLike('projects.builder_developer', $search)
                    ->orLike('locations.city', $search)
                    ->orLike('locations.area', $search)
                    ->groupEnd();
        }

        if (!empty($locationId)) {
            $builder->where('projects.location_id', $locationId);
        }

        if (!empty($constructionStatus)) {
            $builder->where('projects.construction_status', $constructionStatus);
        }

        if (!empty($status)) {
            $builder->where('projects.status', $status);
        }

        return $builder->orderBy('projects.id', 'DESC')->paginate($perPage);
    }

    /**
     * Get single project with complete location details
     */
    public function getProjectWithLocation(int $id)
    {
        return $this->select('projects.*, locations.area, locations.locality, locations.landmark, locations.city, locations.state, locations.pincode, locations.nearby_locations, locations.map_location')
                    ->join('locations', 'locations.id = projects.location_id', 'left')
                    ->where('projects.id', $id)
                    ->first();
    }

    /**
     * Dynamically compute project unit inventory counts from property_units
     */
    public function getInventoryCounts(int $projectId): array
    {
        $db = \Config\Database::connect();
        
        $row = $db->table('property_units')
                  ->select("
                      COUNT(*) as total,
                      SUM(CASE WHEN availability_status = 'Available' THEN 1 ELSE 0 END) as available,
                      SUM(CASE WHEN availability_status = 'Reserved' THEN 1 ELSE 0 END) as reserved,
                      SUM(CASE WHEN availability_status = 'Under Negotiation' THEN 1 ELSE 0 END) as under_negotiation,
                      SUM(CASE WHEN availability_status = 'Booked' THEN 1 ELSE 0 END) as booked,
                      SUM(CASE WHEN availability_status = 'Sold' THEN 1 ELSE 0 END) as sold,
                      SUM(CASE WHEN availability_status = 'Rented' THEN 1 ELSE 0 END) as rented,
                      SUM(CASE WHEN availability_status = 'Under Maintenance' THEN 1 ELSE 0 END) as under_maintenance,
                      SUM(CASE WHEN availability_status = 'Unavailable' THEN 1 ELSE 0 END) as unavailable
                  ")
                  ->where('project_id', $projectId)
                  ->where('deleted_at', null)
                  ->get()
                  ->getRowArray();

        return [
            'total'             => (int)($row['total'] ?? 0),
            'available'         => (int)($row['available'] ?? 0),
            'reserved'          => (int)($row['reserved'] ?? 0),
            'under_negotiation' => (int)($row['under_negotiation'] ?? 0),
            'booked'            => (int)($row['booked'] ?? 0),
            'sold'              => (int)($row['sold'] ?? 0),
            'rented'            => (int)($row['rented'] ?? 0),
            'under_maintenance' => (int)($row['under_maintenance'] ?? 0),
            'unavailable'       => (int)($row['unavailable'] ?? 0),
        ];
    }

    /**
     * Synchronize total_units and available_units columns with actual property_units table
     */
    public function syncUnitCounts(int $projectId): void
    {
        $counts = $this->getInventoryCounts($projectId);
        $this->update($projectId, [
            'total_units'     => $counts['total'],
            'available_units' => $counts['available'],
        ]);
    }
}
