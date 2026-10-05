<?php

namespace App\Models;

use CodeIgniter\Model;

class ComplaintModel extends Model
{
    protected $table            = 'complaints';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'complaint_code',
        'complaint_type',
        'property_id',
        'property_unit_id',
        'tenant_id',
        'description',
        'priority',
        'assigned_user_id',
        'status',
        'resolution',
        'feedback_rating',
        'feedback_comments',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function generateComplaintCode(): string
    {
        $year = date('Y');
        $prefix = "CMP-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('complaint_code');
        $builder->like('complaint_code', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['complaint_code'])) {
            $parts = explode('-', $last['complaint_code']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return sprintf('%s%06d', $prefix, $nextSeq);
    }

    public function getComplaintWithDetails(int $id): ?array
    {
        return $this->select('complaints.*, t.full_name as tenant_name, t.mobile as tenant_mobile, p.title as property_title, u.unit_number, u_as.name as assignee_name')
            ->join('tenants t', 't.id = complaints.tenant_id', 'left')
            ->join('properties p', 'p.id = complaints.property_id')
            ->join('property_units u', 'u.id = complaints.property_unit_id', 'left')
            ->join('users u_as', 'u_as.id = complaints.assigned_user_id', 'left')
            ->where('complaints.id', $id)
            ->first();
    }
}
