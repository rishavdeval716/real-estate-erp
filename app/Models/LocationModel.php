<?php

namespace App\Models;

use CodeIgniter\Model;

class LocationModel extends Model
{
    protected $table            = 'locations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'state',
        'city',
        'area',
        'locality',
        'landmark',
        'pincode',
        'nearby_locations',
        'map_location',
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
        'state'   => 'required|min_length[2]|max_length[100]',
        'city'    => 'required|min_length[2]|max_length[100]',
        'area'    => 'required|min_length[2]|max_length[150]',
        'pincode' => 'required|min_length[3]|max_length[20]',
        'status'  => 'required|in_list[active,inactive]',
    ];

    protected $validationMessages = [
        'state'   => ['required' => 'State is required.'],
        'city'    => ['required' => 'City is required.'],
        'area'    => ['required' => 'Area / Neighborhood is required.'],
        'pincode' => ['required' => 'Pincode / Postal Code is required.'],
    ];

    /**
     * Get filtered and paginated locations
     */
    public function getFilteredLocations(?string $search = null, ?string $city = null, ?string $status = null, int $perPage = 10)
    {
        $builder = $this;

        if (!empty($search)) {
            $builder = $builder->groupStart()
                               ->like('area', $search)
                               ->orLike('locality', $search)
                               ->orLike('landmark', $search)
                               ->orLike('city', $search)
                               ->orLike('state', $search)
                               ->orLike('pincode', $search)
                               ->groupEnd();
        }

        if (!empty($city)) {
            $builder = $builder->where('city', $city);
        }

        if (!empty($status) && in_array($status, ['active', 'inactive'])) {
            $builder = $builder->where('status', $status);
        }

        return $builder->orderBy('id', 'DESC')->paginate($perPage);
    }

    /**
     * Get distinct cities for dropdown filtering
     */
    public function getDistinctCities(): array
    {
        return $this->select('city')
                    ->distinct()
                    ->where('status', 'active')
                    ->orderBy('city', 'ASC')
                    ->findColumn('city') ?? [];
    }
}
