<?php

namespace App\Models;

use CodeIgniter\Model;

class SecurityDepositModel extends Model
{
    protected $table            = 'security_deposits';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'deposit_number',
        'tenant_id',
        'lease_id',
        'amount',
        'deposit_date',
        'refundable_amount',
        'adjusted_amount',
        'refund_date',
        'refund_status',
        'adjustment_reason',
        'remarks',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function generateDepositNumber(): string
    {
        $year = date('Y');
        $prefix = "DEP-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('deposit_number');
        $builder->like('deposit_number', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['deposit_number'])) {
            $parts = explode('-', $last['deposit_number']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return sprintf('%s%06d', $prefix, $nextSeq);
    }

    public function getDepositsWithDetails(array $filters = []): array
    {
        $builder = $this->select('security_deposits.*, t.full_name as tenant_name, t.tenant_code, l.agreement_number, p.title as property_title, u.unit_number')
            ->join('tenants t', 't.id = security_deposits.tenant_id')
            ->join('lease_agreements l', 'l.id = security_deposits.lease_id')
            ->join('properties p', 'p.id = l.property_id')
            ->join('property_units u', 'u.id = l.property_unit_id', 'left');

        if (!empty($filters['tenant_id'])) {
            $builder->where('security_deposits.tenant_id', $filters['tenant_id']);
        }
        if (!empty($filters['lease_id'])) {
            $builder->where('security_deposits.lease_id', $filters['lease_id']);
        }
        if (!empty($filters['refund_status'])) {
            $builder->where('security_deposits.refund_status', $filters['refund_status']);
        }

        return $builder->orderBy('security_deposits.id', 'DESC')->findAll();
    }
}
