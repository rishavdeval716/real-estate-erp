<?php

namespace App\Models;

use CodeIgniter\Model;

class RentDemandModel extends Model
{
    protected $table            = 'rent_demands';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'demand_number',
        'tenant_id',
        'lease_id',
        'property_id',
        'property_unit_id',
        'billing_period',
        'base_rent',
        'maintenance',
        'other_charges',
        'late_fee',
        'tax',
        'total_amount',
        'paid_amount',
        'balance_amount',
        'due_date',
        'status',
        'generated_by',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function generateDemandNumber(): string
    {
        $year = date('Y');
        $prefix = "RNT-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('demand_number');
        $builder->like('demand_number', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['demand_number'])) {
            $parts = explode('-', $last['demand_number']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return sprintf('%s%06d', $prefix, $nextSeq);
    }

    public function getDemandWithDetails(int $id): ?array
    {
        return $this->select('rent_demands.*, t.full_name as tenant_name, t.tenant_code, t.mobile as tenant_mobile, t.email as tenant_email, l.agreement_number, p.title as property_title, p.property_code, p.title as property_address, u.unit_number, u.floor')
            ->join('tenants t', 't.id = rent_demands.tenant_id')
            ->join('lease_agreements l', 'l.id = rent_demands.lease_id')
            ->join('properties p', 'p.id = rent_demands.property_id')
            ->join('property_units u', 'u.id = rent_demands.property_unit_id', 'left')
            ->where('rent_demands.id', $id)
            ->first();
    }

    public function applyLateFees(): void
    {
        $today = date('Y-m-d');
        // Find overdue demands where due_date < today and balance_amount > 0
        $overdue = $this->where('status !=', 'paid')
            ->where('due_date <', $today)
            ->findAll();

        $leaseModel = new LeaseAgreementModel();

        foreach ($overdue as $demand) {
            $lease = $leaseModel->find($demand['lease_id']);
            $lateFee = $lease['late_fee_amount'] ?? 0;
            if ($lateFee > 0 && (float)$demand['late_fee'] == 0) {
                $newTotal = (float)$demand['total_amount'] + (float)$lateFee;
                $newBalance = (float)$demand['balance_amount'] + (float)$lateFee;
                $this->update($demand['id'], [
                    'late_fee'       => $lateFee,
                    'total_amount'   => $newTotal,
                    'balance_amount' => $newBalance,
                    'status'         => 'overdue',
                ]);
            } else {
                $this->update($demand['id'], ['status' => 'overdue']);
            }
        }
    }
}
