<?php

namespace App\Models;

use CodeIgniter\Model;

class MaintenanceRequestModel extends Model
{
    protected $table            = 'maintenance_requests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'ticket_number',
        'tenant_id',
        'property_id',
        'property_unit_id',
        'asset_id',
        'category',
        'subcategory',
        'priority',
        'description',
        'attachment',
        'created_date',
        'assigned_technician_id',
        'sla_due_date',
        'status',
        'resolution',
        'closed_date',
        'created_by',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function generateTicketNumber(): string
    {
        $year = date('Y');
        $prefix = "MR-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('ticket_number');
        $builder->like('ticket_number', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['ticket_number'])) {
            $parts = explode('-', $last['ticket_number']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return sprintf('%s%06d', $prefix, $nextSeq);
    }

    public function getRequestWithDetails(int $id): ?array
    {
        return $this->select('maintenance_requests.*, t.full_name as tenant_name, t.mobile as tenant_mobile, p.title as property_title, p.property_code, u.unit_number, fa.name as asset_name, fa.asset_code, tech.name as technician_name, tech.mobile as technician_mobile, tech.skill as technician_skill, u_cr.name as creator_name')
            ->join('tenants t', 't.id = maintenance_requests.tenant_id', 'left')
            ->join('properties p', 'p.id = maintenance_requests.property_id')
            ->join('property_units u', 'u.id = maintenance_requests.property_unit_id', 'left')
            ->join('facility_assets fa', 'fa.id = maintenance_requests.asset_id', 'left')
            ->join('technicians tech', 'tech.id = maintenance_requests.assigned_technician_id', 'left')
            ->join('users u_cr', 'u_cr.id = maintenance_requests.created_by', 'left')
            ->where('maintenance_requests.id', $id)
            ->first();
    }

    public function getRequestsWithDetails(array $filters = []): array
    {
        $builder = $this->select('maintenance_requests.*, t.full_name as tenant_name, p.title as property_title, u.unit_number, fa.name as asset_name, tech.name as technician_name')
            ->join('tenants t', 't.id = maintenance_requests.tenant_id', 'left')
            ->join('properties p', 'p.id = maintenance_requests.property_id')
            ->join('property_units u', 'u.id = maintenance_requests.property_unit_id', 'left')
            ->join('facility_assets fa', 'fa.id = maintenance_requests.asset_id', 'left')
            ->join('technicians tech', 'tech.id = maintenance_requests.assigned_technician_id', 'left');

        if (!empty($filters['status'])) {
            $builder->where('maintenance_requests.status', $filters['status']);
        }
        if (!empty($filters['priority'])) {
            $builder->where('maintenance_requests.priority', $filters['priority']);
        }
        if (!empty($filters['property_id'])) {
            $builder->where('maintenance_requests.property_id', $filters['property_id']);
        }
        if (!empty($filters['tenant_id'])) {
            $builder->where('maintenance_requests.tenant_id', $filters['tenant_id']);
        }
        if (!empty($filters['assigned_technician_id'])) {
            $builder->where('maintenance_requests.assigned_technician_id', $filters['assigned_technician_id']);
        }

        return $builder->orderBy('maintenance_requests.id', 'DESC')->findAll();
    }
}
