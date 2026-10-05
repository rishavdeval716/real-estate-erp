<?php

namespace App\Models;

use CodeIgniter\Model;

class AmenityModel extends Model
{
    protected $table            = 'amenities';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'icon',
        'description',
        'status',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'name'   => 'required|min_length[2]|max_length[100]|is_unique[amenities.name,id,{id}]',
        'icon'   => 'permit_empty|max_length[100]',
        'status' => 'required|in_list[active,inactive]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'  => 'Amenity name is required.',
            'is_unique' => 'An amenity with this name already exists.',
        ],
    ];

    /**
     * Get filtered amenities
     */
    public function getFilteredAmenities(?string $search = null, ?string $status = null, int $perPage = 10)
    {
        $builder = $this;

        if (!empty($search)) {
            $builder = $builder->groupStart()
                               ->like('name', $search)
                               ->orLike('description', $search)
                               ->groupEnd();
        }

        if (!empty($status) && in_array($status, ['active', 'inactive'])) {
            $builder = $builder->where('status', $status);
        }

        return $builder->orderBy('name', 'ASC')->paginate($perPage);
    }
}
