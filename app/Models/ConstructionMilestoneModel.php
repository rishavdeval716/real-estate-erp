<?php

namespace App\Models;

use CodeIgniter\Model;

class ConstructionMilestoneModel extends Model
{
    protected $table            = 'construction_milestones';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'milestone_code',
        'project_id',
        'tower_id',
        'milestone_name',
        'stage_order',
        'weightage_percentage',
        'target_start_date',
        'target_completion_date',
        'actual_completion_date',
        'progress_percentage',
        'status',
        'verified_by',
        'verified_at',
        'remarks',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function generateMilestoneCode(): string
    {
        $year = date('Y');
        $prefix = "MIL-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('milestone_code');
        $builder->like('milestone_code', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['milestone_code'])) {
            $parts = explode('-', $last['milestone_code']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return $prefix . str_pad((string)$nextSeq, 6, '0', STR_PAD_LEFT);
    }

    public function getMilestonesWithDetails(?int $projectId = null, ?int $towerId = null)
    {
        $builder = $this->builder();
        $builder->select('construction_milestones.*, projects.name as project_name, projects.project_code, project_towers.tower_name, project_towers.tower_code, users.name as verifier_name');
        $builder->join('projects', 'projects.id = construction_milestones.project_id', 'left');
        $builder->join('project_towers', 'project_towers.id = construction_milestones.tower_id', 'left');
        $builder->join('users', 'users.id = construction_milestones.verified_by', 'left');

        if ($projectId) {
            $builder->where('construction_milestones.project_id', $projectId);
        }
        if ($towerId) {
            $builder->where('construction_milestones.tower_id', $towerId);
        }

        $builder->orderBy('construction_milestones.project_id', 'ASC');
        $builder->orderBy('construction_milestones.stage_order', 'ASC');

        return $builder->get()->getResultArray();
    }
}
