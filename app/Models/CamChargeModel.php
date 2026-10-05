<?php

namespace App\Models;

use CodeIgniter\Model;

class CamChargeModel extends Model
{
    protected $table            = 'cam_charges';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'cam_code',
        'property_id',
        'property_unit_id',
        'tenant_id',
        'area_sqft',
        'billing_model',
        'rate',
        'period',
        'amount',
        'tax',
        'total',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function generateCamCode(): string
    {
        $year = date('Y');
        $prefix = "CAM-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('cam_code');
        $builder->like('cam_code', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['cam_code'])) {
            $parts = explode('-', $last['cam_code']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return sprintf('%s%06d', $prefix, $nextSeq);
    }

    public function getChargesWithDetails(array $filters = []): array
    {
        $builder = $this->select('cam_charges.*, p.title as property_title, u.unit_number, t.full_name as tenant_name, t.tenant_code')
            ->join('properties p', 'p.id = cam_charges.property_id')
            ->join('property_units u', 'u.id = cam_charges.property_unit_id', 'left')
            ->join('tenants t', 't.id = cam_charges.tenant_id', 'left');

        if (!empty($filters['status'])) {
            $builder->where('cam_charges.status', $filters['status']);
        }
        if (!empty($filters['property_id'])) {
            $builder->where('cam_charges.property_id', $filters['property_id']);
        }

        return $builder->orderBy('cam_charges.id', 'DESC')->findAll();
    }
}
