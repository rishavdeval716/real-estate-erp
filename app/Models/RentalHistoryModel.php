<?php

namespace App\Models;

use CodeIgniter\Model;

class RentalHistoryModel extends Model
{
    protected $table            = 'rental_histories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tenant_id',
        'property_id',
        'property_unit_id',
        'lease_id',
        'previous_rent',
        'current_rent',
        'start_date',
        'end_date',
        'status',
        'notes',
        'created_at',
    ];

    protected $useTimestamps = false;

    public function getByTenant(int $tenantId): array
    {
        return $this->select('rental_histories.*, p.title as property_title, u.unit_number, l.agreement_number')
            ->join('properties p', 'p.id = rental_histories.property_id')
            ->join('property_units u', 'u.id = rental_histories.property_unit_id', 'left')
            ->join('lease_agreements l', 'l.id = rental_histories.lease_id', 'left')
            ->where('rental_histories.tenant_id', $tenantId)
            ->orderBy('rental_histories.id', 'DESC')
            ->findAll();
    }
}
