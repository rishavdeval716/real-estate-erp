<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectTowerModel extends Model
{
    protected $table            = 'project_towers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'project_id',
        'tower_name',
        'tower_code',
        'number_of_floors',
        'total_units',
        'status',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'project_id'       => 'required|is_not_unique[projects.id]',
        'tower_name'       => 'required|min_length[1]|max_length[100]',
        'tower_code'       => 'required|min_length[1]|max_length[50]',
        'number_of_floors' => 'required|is_natural_no_zero',
        'status'           => 'required|in_list[active,inactive]',
    ];

    protected $validationMessages = [
        'project_id' => [
            'is_not_unique' => 'The selected project does not exist.',
        ],
    ];

    /**
     * Get towers for a project with unit counts
     */
    public function getTowersForProject(int $projectId): array
    {
        $db = \Config\Database::connect();
        return $this->select('project_towers.*, COUNT(property_units.id) as actual_units')
                    ->join('property_units', 'property_units.tower_id = project_towers.id AND property_units.deleted_at IS NULL', 'left')
                    ->where('project_towers.project_id', $projectId)
                    ->groupBy('project_towers.id')
                    ->orderBy('project_towers.tower_code', 'ASC')
                    ->findAll();
    }
}
