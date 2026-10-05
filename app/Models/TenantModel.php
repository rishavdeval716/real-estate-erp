<?php

namespace App\Models;

use CodeIgniter\Model;

class TenantModel extends Model
{
    protected $table            = 'tenants';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tenant_code',
        'tenant_type',
        'full_name',
        'company_name',
        'contact_person',
        'mobile',
        'email',
        'address',
        'city',
        'state',
        'pincode',
        'id_proof_type',
        'id_proof_number',
        'kyc_status',
        'occupied_property_id',
        'occupied_unit_id',
        'user_id',
        'lease_start_date',
        'lease_end_date',
        'status',
        'notes',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Generate sequential unique tenant code: TEN-2026-000001
     */
    public function generateTenantCode(): string
    {
        $year = date('Y');
        $prefix = "TEN-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('tenant_code');
        $builder->like('tenant_code', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['tenant_code'])) {
            $parts = explode('-', $last['tenant_code']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return sprintf('%s%06d', $prefix, $nextSeq);
    }

    /**
     * Get single tenant with property and unit joins
     */
    public function getTenantWithDetails(int $id): ?array
    {
        return $this->select('tenants.*, p.title as property_title, p.property_code, u.unit_number, u.floor, pt.name as property_type_name, usr.email as user_email')
            ->join('properties p', 'p.id = tenants.occupied_property_id', 'left')
            ->join('property_units u', 'u.id = tenants.occupied_unit_id', 'left')
            ->join('property_types pt', 'pt.id = p.property_type_id', 'left')
            ->join('users usr', 'usr.id = tenants.user_id', 'left')
            ->where('tenants.id', $id)
            ->first();
    }
}
