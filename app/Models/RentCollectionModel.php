<?php

namespace App\Models;

use CodeIgniter\Model;

class RentCollectionModel extends Model
{
    protected $table            = 'rent_collections';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'collection_number',
        'rent_demand_id',
        'tenant_id',
        'lease_id',
        'amount',
        'payment_date',
        'payment_method',
        'transaction_reference',
        'remarks',
        'received_by',
        'created_at',
    ];

    protected $useTimestamps = false;

    public function generateCollectionNumber(): string
    {
        $year = date('Y');
        $prefix = "RCL-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('collection_number');
        $builder->like('collection_number', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['collection_number'])) {
            $parts = explode('-', $last['collection_number']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return sprintf('%s%06d', $prefix, $nextSeq);
    }

    public function getCollectionWithDetails(int $id): ?array
    {
        return $this->select('rent_collections.*, rd.demand_number, rd.billing_period, t.full_name as tenant_name, t.tenant_code, l.agreement_number, p.title as property_title, u.unit_number, u_rec.name as receiver_name')
            ->join('rent_demands rd', 'rd.id = rent_collections.rent_demand_id')
            ->join('tenants t', 't.id = rent_collections.tenant_id')
            ->join('lease_agreements l', 'l.id = rent_collections.lease_id')
            ->join('properties p', 'p.id = l.property_id')
            ->join('property_units u', 'u.id = l.property_unit_id', 'left')
            ->join('users u_rec', 'u_rec.id = rent_collections.received_by', 'left')
            ->where('rent_collections.id', $id)
            ->first();
    }
}
