<?php

namespace App\Models;

use CodeIgniter\Model;

class ConstructionWorkOrderModel extends Model
{
    protected $table            = 'construction_work_orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'work_order_code',
        'contractor_id',
        'project_id',
        'tower_id',
        'milestone_id',
        'title',
        'scope_of_work',
        'contract_amount',
        'retention_percentage',
        'start_date',
        'completion_date',
        'payment_terms',
        'status',
        'created_by',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function generateWorkOrderCode(): string
    {
        $year = date('Y');
        $prefix = "CWO-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('work_order_code');
        $builder->like('work_order_code', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['work_order_code'])) {
            $parts = explode('-', $last['work_order_code']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return $prefix . str_pad((string)$nextSeq, 6, '0', STR_PAD_LEFT);
    }

    public function getWorkOrdersWithDetails(?int $contractorId = null, ?int $projectId = null)
    {
        $builder = $this->builder();
        $builder->select('construction_work_orders.*, contractors.company_name, contractors.specialization, contractors.contact_person, projects.name as project_name, project_towers.tower_name, construction_milestones.milestone_name');
        $builder->join('contractors', 'contractors.id = construction_work_orders.contractor_id', 'left');
        $builder->join('projects', 'projects.id = construction_work_orders.project_id', 'left');
        $builder->join('project_towers', 'project_towers.id = construction_work_orders.tower_id', 'left');
        $builder->join('construction_milestones', 'construction_milestones.id = construction_work_orders.milestone_id', 'left');

        if ($contractorId) {
            $builder->where('construction_work_orders.contractor_id', $contractorId);
        }
        if ($projectId) {
            $builder->where('construction_work_orders.project_id', $projectId);
        }

        $builder->orderBy('construction_work_orders.id', 'DESC');

        return $builder->get()->getResultArray();
    }
}
