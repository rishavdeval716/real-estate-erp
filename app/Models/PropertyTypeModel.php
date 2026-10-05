<?php

namespace App\Models;

use CodeIgniter\Model;

class PropertyTypeModel extends Model
{
    protected $table            = 'property_types';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'slug',
        'description',
        'status',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Callbacks
    protected $beforeInsert = ['generateSlug'];
    protected $beforeUpdate = ['generateSlug'];

    // Validation
    protected $validationRules = [
        'name'   => 'required|min_length[2]|max_length[100]|is_unique[property_types.name,id,{id}]',
        'slug'   => 'permit_empty|min_length[2]|max_length[100]|is_unique[property_types.slug,id,{id}]',
        'status' => 'required|in_list[active,inactive]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'  => 'The property type name is required.',
            'is_unique' => 'A property type with this name already exists.',
        ],
        'slug' => [
            'is_unique' => 'This slug is already in use.',
        ],
    ];

    protected function generateSlug(array $data)
    {
        if (isset($data['data']['name']) && empty($data['data']['slug'])) {
            $data['data']['slug'] = url_title($data['data']['name'], '-', true);
        } elseif (isset($data['data']['slug'])) {
            $data['data']['slug'] = url_title($data['data']['slug'], '-', true);
        }
        return $data;
    }

    /**
     * Filtered search and pagination
     */
    public function getFilteredTypes(?string $search = null, ?string $status = null, int $perPage = 10)
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

        return $builder->orderBy('id', 'ASC')->paginate($perPage);
    }
}
