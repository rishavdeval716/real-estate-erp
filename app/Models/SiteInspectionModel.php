<?php

namespace App\Models;

use CodeIgniter\Model;

class SiteInspectionModel extends Model
{
    protected $table            = 'site_inspections';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'inspection_code',
        'project_id',
        'tower_id',
        'unit_id',
        'inspection_type',
        'inspection_date',
        'inspector_id',
        'result',
        'snags_found',
        'snag_details',
        'rectification_deadline',
        'remarks',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function generateInspectionCode(): string
    {
        $year = date('Y');
        $prefix = "INSP-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('inspection_code');
        $builder->like('inspection_code', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['inspection_code'])) {
            $parts = explode('-', $last['inspection_code']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return $prefix . str_pad((string)$nextSeq, 6, '0', STR_PAD_LEFT);
    }

    public function getInspectionsWithDetails(?int $projectId = null, ?string $result = null)
    {
        $builder = $this->builder();
        $builder->select('site_inspections.*, projects.name as project_name, project_towers.tower_name, property_units.unit_number, users.name as inspector_name');
        $builder->join('projects', 'projects.id = site_inspections.project_id', 'left');
        $builder->join('project_towers', 'project_towers.id = site_inspections.tower_id', 'left');
        $builder->join('property_units', 'property_units.id = site_inspections.unit_id', 'left');
        $builder->join('users', 'users.id = site_inspections.inspector_id', 'left');

        if ($projectId) {
            $builder->where('site_inspections.project_id', $projectId);
        }
        if ($result) {
            $builder->where('site_inspections.result', $result);
        }

        $builder->orderBy('site_inspections.id', 'DESC');

        return $builder->get()->getResultArray();
    }
}
