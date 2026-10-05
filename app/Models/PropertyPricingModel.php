<?php

namespace App\Models;

use CodeIgniter\Model;

class PropertyPricingModel extends Model
{
    protected $table            = 'property_pricing';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'property_id',
        'unit_id',
        'base_price',
        'price_per_sqft',
        'market_price',
        'negotiated_price',
        'discount',
        'effective_from',
        'effective_to',
        'remarks',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'property_id'      => 'required|is_not_unique[properties.id]',
        'unit_id'          => 'permit_empty|is_not_unique[property_units.id]',
        'base_price'       => 'required|numeric|greater_than_equal_to[0]',
        'price_per_sqft'   => 'permit_empty|numeric|greater_than_equal_to[0]',
        'market_price'     => 'permit_empty|numeric|greater_than_equal_to[0]',
        'negotiated_price' => 'permit_empty|numeric|greater_than_equal_to[0]',
        'discount'         => 'permit_empty|numeric|greater_than_equal_to[0]',
        'effective_from'   => 'permit_empty|valid_date',
        'effective_to'     => 'permit_empty|valid_date',
    ];

    /**
     * Get pricing history for a property or unit
     */
    public function getPricingHistory(int $propertyId, ?int $unitId = null): array
    {
        $builder = $this->where('property_id', $propertyId);

        if ($unitId !== null) {
            $builder->where('unit_id', $unitId);
        } else {
            $builder->where('unit_id', null);
        }

        return $builder->orderBy('id', 'DESC')->findAll();
    }

    /**
     * Get the most recent pricing record
     */
    public function getLatestPricing(int $propertyId, ?int $unitId = null)
    {
        $builder = $this->where('property_id', $propertyId);

        if ($unitId !== null) {
            $builder->where('unit_id', $unitId);
        } else {
            $builder->where('unit_id', null);
        }

        return $builder->orderBy('id', 'DESC')->first();
    }
}
