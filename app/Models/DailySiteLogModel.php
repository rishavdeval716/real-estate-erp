<?php

namespace App\Models;

use CodeIgniter\Model;

class DailySiteLogModel extends Model
{
    protected $table            = 'daily_site_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'log_code',
        'log_date',
        'project_id',
        'tower_id',
        'milestone_id',
        'skilled_workers',
        'unskilled_workers',
        'weather_condition',
        'work_completed',
        'materials_used',
        'equipment_deployed',
        'delays_or_impediments',
        'logged_by',
        'approved_by',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function generateLogCode(): string
    {
        $year = date('Y');
        $prefix = "LOG-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('log_code');
        $builder->like('log_code', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['log_code'])) {
            $parts = explode('-', $last['log_code']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return $prefix . str_pad((string)$nextSeq, 6, '0', STR_PAD_LEFT);
    }

    public function getLogsWithDetails(?int $projectId = null, ?string $date = null)
    {
        $builder = $this->builder();
        $builder->select('daily_site_logs.*, projects.name as project_name, project_towers.tower_name, construction_milestones.milestone_name, u_log.name as logger_name, u_app.name as approver_name');
        $builder->join('projects', 'projects.id = daily_site_logs.project_id', 'left');
        $builder->join('project_towers', 'project_towers.id = daily_site_logs.tower_id', 'left');
        $builder->join('construction_milestones', 'construction_milestones.id = daily_site_logs.milestone_id', 'left');
        $builder->join('users as u_log', 'u_log.id = daily_site_logs.logged_by', 'left');
        $builder->join('users as u_app', 'u_app.id = daily_site_logs.approved_by', 'left');

        if ($projectId) {
            $builder->where('daily_site_logs.project_id', $projectId);
        }
        if ($date) {
            $builder->where('daily_site_logs.log_date', $date);
        }

        $builder->orderBy('daily_site_logs.log_date', 'DESC');
        $builder->orderBy('daily_site_logs.id', 'DESC');

        return $builder->get()->getResultArray();
    }
}
