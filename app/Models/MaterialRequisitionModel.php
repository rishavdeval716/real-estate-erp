<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialRequisitionModel extends Model
{
    protected $table            = 'material_requisitions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'requisition_code',
        'project_id',
        'tower_id',
        'item_name',
        'category',
        'quantity',
        'unit_of_measure',
        'estimated_unit_cost',
        'estimated_total_cost',
        'required_by_date',
        'priority',
        'requested_by',
        'status',
        'remarks',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function generateRequisitionCode(): string
    {
        $year = date('Y');
        $prefix = "REQ-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('requisition_code');
        $builder->like('requisition_code', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['requisition_code'])) {
            $parts = explode('-', $last['requisition_code']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return $prefix . str_pad((string)$nextSeq, 6, '0', STR_PAD_LEFT);
    }

    public function getRequisitionsWithDetails(?int $projectId = null, ?string $status = null)
    {
        $builder = $this->builder();
        $builder->select('material_requisitions.*, projects.name as project_name, project_towers.tower_name, users.name as requester_name');
        $builder->join('projects', 'projects.id = material_requisitions.project_id', 'left');
        $builder->join('project_towers', 'project_towers.id = material_requisitions.tower_id', 'left');
        $builder->join('users', 'users.id = material_requisitions.requested_by', 'left');

        if ($projectId) {
            $builder->where('material_requisitions.project_id', $projectId);
        }
        if ($status) {
            $builder->where('material_requisitions.status', $status);
        }

        $builder->orderBy('material_requisitions.id', 'DESC');

        return $builder->get()->getResultArray();
    }
}
