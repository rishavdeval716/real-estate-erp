<?php

namespace App\Models;

use CodeIgniter\Model;

class PropertyOwnerModel extends Model
{
    protected $table            = 'property_owners';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'owner_code',
        'user_id',
        'first_name',
        'last_name',
        'company_name',
        'email',
        'phone',
        'alternate_phone',
        'address',
        'city',
        'state',
        'pincode',
        'pan_number',
        'aadhaar_number',
        'kyc_status',
        'bank_name',
        'bank_account_number',
        'bank_ifsc',
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

    public function generateOwnerCode(): string
    {
        $year = date('Y');
        $prefix = "OWN-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('owner_code');
        $builder->like('owner_code', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['owner_code'])) {
            $parts = explode('-', $last['owner_code']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return $prefix . str_pad((string)$nextSeq, 6, '0', STR_PAD_LEFT);
    }

    public function getOwnerWithDetails($id)
    {
        $owner = $this->find($id);
        if (!$owner) return null;

        $db = \Config\Database::connect();
        // Associated properties
        $owner['properties'] = $db->table('properties')
            ->select('properties.*, property_types.name as property_type_name, locations.city as location_city, projects.name as project_name')
            ->join('property_types', 'property_types.id = properties.property_type_id', 'left')
            ->join('locations', 'locations.id = properties.location_id', 'left')
            ->join('projects', 'projects.id = properties.project_id', 'left')
            ->where('properties.owner_id', $id)
            ->where('properties.deleted_at IS NULL')
            ->get()->getResultArray();

        // Associated documents
        $owner['documents'] = $db->table('property_documents')
            ->where('owner_id', $id)
            ->where('deleted_at IS NULL')
            ->get()->getResultArray();

        // Property IDs for financial aggregation
        $propIds = array_column($owner['properties'], 'id');
        $owner['total_properties'] = count($owner['properties']);

        // Rental income calculation
        if (!empty($propIds)) {
            $owner['rental_income'] = (float)$db->table('rent_collections')
                ->selectSum('rent_collections.amount', 'total_rent')
                ->join('rent_demands', 'rent_demands.id = rent_collections.rent_demand_id')
                ->join('lease_agreements', 'lease_agreements.id = rent_demands.lease_id')
                ->join('property_units', 'property_units.id = lease_agreements.property_unit_id')
                ->whereIn('property_units.property_id', $propIds)
                ->get()->getRow()->total_rent ?? 0.00;

            $owner['expenses'] = (float)$db->table('property_expenses')
                ->selectSum('total_amount', 'total_exp')
                ->whereIn('property_id', $propIds)
                ->where('deleted_at IS NULL')
                ->get()->getRow()->total_exp ?? 0.00;
        } else {
            $owner['rental_income'] = 0.00;
            $owner['expenses'] = 0.00;
        }

        return $owner;
    }
}
