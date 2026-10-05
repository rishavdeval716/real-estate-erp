<?php

namespace App\Models;

use CodeIgniter\Model;

class PortalRequestModel extends Model
{
    protected $table            = 'portal_requests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'request_code',
        'portal_type',
        'user_id',
        'request_type',
        'subject',
        'details',
        'status',
        'admin_notes',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function generateRequestCode(): string
    {
        $year = date('Y');
        $prefix = "REQ-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('request_code');
        $builder->like('request_code', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['request_code'])) {
            $parts = explode('-', $last['request_code']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return sprintf('%s%06d', $prefix, $nextSeq);
    }

    public function getRequestsWithDetails(array $filters = []): array
    {
        $builder = $this->select('portal_requests.*, u.first_name, u.last_name, u.email')
            ->join('users u', 'u.id = portal_requests.user_id');

        if (!empty($filters['portal_type'])) {
            $builder->where('portal_requests.portal_type', $filters['portal_type']);
        }
        if (!empty($filters['user_id'])) {
            $builder->where('portal_requests.user_id', $filters['user_id']);
        }
        if (!empty($filters['status'])) {
            $builder->where('portal_requests.status', $filters['status']);
        }

        return $builder->orderBy('portal_requests.id', 'DESC')->findAll();
    }
}
