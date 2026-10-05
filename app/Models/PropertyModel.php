<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\PropertyStatus;

class PropertyModel extends Model
{
    protected $table            = 'properties';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'property_code',
        'title',
        'description',
        'property_type_id',
        'project_id',
        'location_id',
        'owner_name_or_reference',
        'ownership_details',
        'area',
        'price',
        'status',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'property_code'    => 'required|min_length[2]|max_length[50]|is_unique[properties.property_code,id,{id}]',
        'title'            => 'required|min_length[3]|max_length[200]',
        'property_type_id' => 'required|is_not_unique[property_types.id]',
        'location_id'      => 'required|is_not_unique[locations.id]',
        'project_id'       => 'permit_empty|is_not_unique[projects.id]',
        'area'             => 'required|numeric|greater_than[0]',
        'price'            => 'required|numeric|greater_than_equal_to[0]',
        'status'           => 'required|in_list[Available,Reserved,Under Negotiation,Booked,Sold,Rented,Under Maintenance,Unavailable]',
    ];

    protected $validationMessages = [
        'property_code' => [
            'is_unique' => 'This property code already exists. Please generate or enter a unique code.',
        ],
        'property_type_id' => [
            'is_not_unique' => 'Please select a valid property type.',
        ],
        'location_id' => [
            'is_not_unique' => 'Please select a valid location.',
        ],
    ];

    /**
     * Advanced property search with multi-criteria filtering, sorting, and pagination
     */
    public function getFilteredProperties(
        ?string $search = null,
        ?int $typeId = null,
        ?int $projectId = null,
        ?int $locationId = null,
        ?string $status = null,
        ?float $minPrice = null,
        ?float $maxPrice = null,
        ?float $minArea = null,
        ?float $maxArea = null,
        string $sortBy = 'id',
        string $sortDir = 'DESC',
        int $perPage = 10
    ) {
        $builder = $this->select('
            properties.*,
            property_types.name as property_type_name,
            projects.name as project_name,
            locations.city as location_city,
            locations.area as location_area,
            locations.state as location_state,
            (SELECT file_path FROM property_media WHERE property_media.property_id = properties.id AND property_media.is_primary = 1 LIMIT 1) as primary_image
        ')
        ->join('property_types', 'property_types.id = properties.property_type_id', 'left')
        ->join('projects', 'projects.id = properties.project_id', 'left')
        ->join('locations', 'locations.id = properties.location_id', 'left');

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('properties.title', $search)
                    ->orLike('properties.property_code', $search)
                    ->orLike('properties.owner_name_or_reference', $search)
                    ->orLike('locations.city', $search)
                    ->orLike('locations.area', $search)
                    ->orLike('projects.name', $search)
                    ->groupEnd();
        }

        if (!empty($typeId)) {
            $builder->where('properties.property_type_id', $typeId);
        }

        if (!empty($projectId)) {
            $builder->where('properties.project_id', $projectId);
        }

        if (!empty($locationId)) {
            $builder->where('properties.location_id', $locationId);
        }

        if (!empty($status) && PropertyStatus::isValid($status)) {
            $builder->where('properties.status', $status);
        }

        if (!empty($minPrice)) {
            $builder->where('properties.price >=', $minPrice);
        }

        if (!empty($maxPrice)) {
            $builder->where('properties.price <=', $maxPrice);
        }

        if (!empty($minArea)) {
            $builder->where('properties.area >=', $minArea);
        }

        if (!empty($maxArea)) {
            $builder->where('properties.area <=', $maxArea);
        }

        $validSortCols = ['id', 'price', 'area', 'title', 'created_at', 'status'];
        $column = in_array($sortBy, $validSortCols) ? 'properties.' . $sortBy : 'properties.id';
        $direction = strtoupper($sortDir) === 'ASC' ? 'ASC' : 'DESC';

        return $builder->orderBy($column, $direction)->paginate($perPage);
    }

    /**
     * Get single property with joined type, location, and project details
     */
    public function getPropertyFull(int $id)
    {
        return $this->select('
            properties.*,
            property_types.name as property_type_name,
            property_types.slug as property_type_slug,
            projects.name as project_name,
            projects.project_code as project_code_ref,
            projects.builder_developer,
            projects.construction_status as project_construction_status,
            locations.state,
            locations.city,
            locations.area,
            locations.locality,
            locations.landmark,
            locations.pincode,
            locations.nearby_locations,
            locations.map_location
        ')
        ->join('property_types', 'property_types.id = properties.property_type_id', 'left')
        ->join('projects', 'projects.id = properties.project_id', 'left')
        ->join('locations', 'locations.id = properties.location_id', 'left')
        ->where('properties.id', $id)
        ->first();
    }

    /**
     * Get amenities associated with a property
     */
    public function getAmenities(int $propertyId): array
    {
        $db = \Config\Database::connect();
        return $db->table('property_amenities')
                  ->select('amenities.*')
                  ->join('amenities', 'amenities.id = property_amenities.amenity_id')
                  ->where('property_amenities.property_id', $propertyId)
                  ->orderBy('amenities.name', 'ASC')
                  ->get()
                  ->getResultArray();
    }

    /**
     * Sync property amenities pivot table
     */
    public function syncAmenities(int $propertyId, array $amenityIds): void
    {
        $db = \Config\Database::connect();
        $db->table('property_amenities')->where('property_id', $propertyId)->delete();

        if (!empty($amenityIds)) {
            $batch = [];
            foreach ($amenityIds as $amenityId) {
                if (is_numeric($amenityId)) {
                    $batch[] = [
                        'property_id' => $propertyId,
                        'amenity_id'  => (int)$amenityId,
                        'created_at'  => date('Y-m-d H:i:s'),
                    ];
                }
            }
            if (!empty($batch)) {
                $db->table('property_amenities')->insertBatch($batch);
            }
        }
    }
}
