<?php

namespace App\Models;

use CodeIgniter\Model;

class FacilityAssetModel extends Model
{
    protected $table            = 'facility_assets';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'asset_code',
        'name',
        'category',
        'property_id',
        'location_details',
        'installation_date',
        'warranty_expiry',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function generateAssetCode(): string
    {
        $year = date('Y');
        $prefix = "AST-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('asset_code');
        $builder->like('asset_code', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['asset_code'])) {
            $parts = explode('-', $last['asset_code']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return sprintf('%s%06d', $prefix, $nextSeq);
    }

    public function getAssetsWithDetails(array $filters = []): array
    {
        $builder = $this->select('facility_assets.*, p.title as property_title, p.property_code')
            ->join('properties p', 'p.id = facility_assets.property_id');

        if (!empty($filters['category'])) {
            $builder->where('facility_assets.category', $filters['category']);
        }
        if (!empty($filters['status'])) {
            $builder->where('facility_assets.status', $filters['status']);
        }
        if (!empty($filters['property_id'])) {
            $builder->where('facility_assets.property_id', $filters['property_id']);
        }

        return $builder->orderBy('facility_assets.id', 'DESC')->findAll();
    }
}
