<?php

namespace App\Models;

use CodeIgniter\Model;

class PreventiveMaintenanceModel extends Model
{
    protected $table            = 'preventive_maintenance';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'schedule_code',
        'asset_id',
        'maintenance_type',
        'frequency',
        'last_service_date',
        'next_service_date',
        'assigned_technician_id',
        'status',
        'remarks',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function generateScheduleCode(): string
    {
        $year = date('Y');
        $prefix = "PM-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('schedule_code');
        $builder->like('schedule_code', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['schedule_code'])) {
            $parts = explode('-', $last['schedule_code']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return sprintf('%s%06d', $prefix, $nextSeq);
    }

    public function getSchedulesWithDetails(array $filters = []): array
    {
        $builder = $this->select('preventive_maintenance.*, fa.name as asset_name, fa.asset_code, fa.category as asset_category, p.title as property_title, tech.name as technician_name, tech.skill as technician_skill')
            ->join('facility_assets fa', 'fa.id = preventive_maintenance.asset_id')
            ->join('properties p', 'p.id = fa.property_id')
            ->join('technicians tech', 'tech.id = preventive_maintenance.assigned_technician_id', 'left');

        if (!empty($filters['status'])) {
            $builder->where('preventive_maintenance.status', $filters['status']);
        }
        if (!empty($filters['asset_id'])) {
            $builder->where('preventive_maintenance.asset_id', $filters['asset_id']);
        }

        return $builder->orderBy('preventive_maintenance.next_service_date', 'ASC')->findAll();
    }
}
