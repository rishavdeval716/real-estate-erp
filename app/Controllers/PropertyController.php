<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PropertyModel;
use App\Models\PropertyTypeModel;
use App\Models\ProjectModel;
use App\Models\LocationModel;
use App\Models\AmenityModel;
use App\Models\PropertyUnitModel;
use App\Models\PropertyMediaModel;
use App\Models\PropertyPricingModel;
use App\Models\PropertyStatusHistoryModel;
use App\Models\AuditLogModel;
use App\Libraries\PropertyStatus;

class PropertyController extends BaseController
{
    protected $propertyModel;
    protected $propertyTypeModel;
    protected $projectModel;
    protected $locationModel;
    protected $amenityModel;
    protected $unitModel;
    protected $mediaModel;
    protected $pricingModel;
    protected $statusHistoryModel;

    public function __construct()
    {
        $this->propertyModel      = new PropertyModel();
        $this->propertyTypeModel  = new PropertyTypeModel();
        $this->projectModel       = new ProjectModel();
        $this->locationModel      = new LocationModel();
        $this->amenityModel       = new AmenityModel();
        $this->unitModel          = new PropertyUnitModel();
        $this->mediaModel         = new PropertyMediaModel();
        $this->pricingModel       = new PropertyPricingModel();
        $this->statusHistoryModel = new PropertyStatusHistoryModel();
    }

    /**
     * Advanced Property Search & Listing
     */
    public function index()
    {
        $search     = trim($this->request->getGet('search') ?? '');
        $typeId     = (int)($this->request->getGet('type_id') ?? 0) ?: null;
        $projectId  = (int)($this->request->getGet('project_id') ?? 0) ?: null;
        $locationId = (int)($this->request->getGet('location_id') ?? 0) ?: null;
        $status     = trim($this->request->getGet('status') ?? '');
        $minPrice   = $this->request->getGet('min_price') !== null && $this->request->getGet('min_price') !== '' ? (float)$this->request->getGet('min_price') : null;
        $maxPrice   = $this->request->getGet('max_price') !== null && $this->request->getGet('max_price') !== '' ? (float)$this->request->getGet('max_price') : null;
        $minArea    = $this->request->getGet('min_area') !== null && $this->request->getGet('min_area') !== '' ? (float)$this->request->getGet('min_area') : null;
        $maxArea    = $this->request->getGet('max_area') !== null && $this->request->getGet('max_area') !== '' ? (float)$this->request->getGet('max_area') : null;
        $sortBy     = trim($this->request->getGet('sort_by') ?? 'id');
        $sortDir    = trim($this->request->getGet('sort_dir') ?? 'DESC');

        $perPage    = 10;
        $properties = $this->propertyModel->getFilteredProperties(
            $search,
            $typeId,
            $projectId,
            $locationId,
            $status,
            $minPrice,
            $maxPrice,
            $minArea,
            $maxArea,
            $sortBy,
            $sortDir,
            $perPage
        );
        $pager = $this->propertyModel->pager;

        // Dropdown filter options
        $types     = $this->propertyTypeModel->where('status', 'active')->orderBy('name', 'ASC')->findAll();
        $projects  = $this->projectModel->where('status !=', 'archived')->orderBy('name', 'ASC')->findAll();
        $locations = $this->locationModel->where('status', 'active')->orderBy('city', 'ASC')->findAll();
        $statuses  = PropertyStatus::all();

        $data = [
            'title'      => 'Property Management - Real Estate ERP',
            'properties' => $properties,
            'pager'      => $pager,
            'search'     => $search,
            'typeId'     => $typeId,
            'projectId'  => $projectId,
            'locationId' => $locationId,
            'status'     => $status,
            'minPrice'   => $minPrice,
            'maxPrice'   => $maxPrice,
            'minArea'    => $minArea,
            'maxArea'    => $maxArea,
            'sortBy'     => $sortBy,
            'sortDir'    => $sortDir,
            'types'      => $types,
            'projects'   => $projects,
            'locations'  => $locations,
            'statuses'   => $statuses,
        ];

        return view('properties/index', $data);
    }

    /**
     * Show create property form
     */
    public function create()
    {
        $types     = $this->propertyTypeModel->where('status', 'active')->orderBy('name', 'ASC')->findAll();
        $projects  = $this->projectModel->where('status !=', 'archived')->orderBy('name', 'ASC')->findAll();
        $locations = $this->locationModel->where('status', 'active')->orderBy('city', 'ASC')->findAll();
        $amenities = $this->amenityModel->where('status', 'active')->orderBy('name', 'ASC')->findAll();
        $statuses  = PropertyStatus::all();

        $data = [
            'title'     => 'Create New Property - Real Estate ERP',
            'types'     => $types,
            'projects'  => $projects,
            'locations' => $locations,
            'amenities' => $amenities,
            'statuses'  => $statuses,
        ];

        return view('properties/create', $data);
    }

    /**
     * Store new property
     */
    public function store()
    {
        $rules = [
            'property_code'    => 'required|min_length[2]|max_length[50]|is_unique[properties.property_code]',
            'title'            => 'required|min_length[3]|max_length[200]',
            'property_type_id' => 'required|is_not_unique[property_types.id]',
            'location_id'      => 'required|is_not_unique[locations.id]',
            'project_id'       => 'permit_empty|is_not_unique[projects.id]',
            'area'             => 'required|numeric|greater_than[0]',
            'price'            => 'required|numeric|greater_than_equal_to[0]',
            'status'           => 'required|in_list[Available,Reserved,Under Negotiation,Booked,Sold,Rented,Under Maintenance,Unavailable]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $propertyData = [
            'property_code'           => strtoupper(trim($this->request->getPost('property_code'))),
            'title'                   => trim($this->request->getPost('title')),
            'description'             => trim($this->request->getPost('description') ?? ''),
            'property_type_id'        => (int)$this->request->getPost('property_type_id'),
            'project_id'              => $this->request->getPost('project_id') ? (int)$this->request->getPost('project_id') : null,
            'location_id'             => (int)$this->request->getPost('location_id'),
            'owner_name_or_reference' => trim($this->request->getPost('owner_name_or_reference') ?? ''),
            'ownership_details'       => trim($this->request->getPost('ownership_details') ?? ''),
            'area'                    => (float)$this->request->getPost('area'),
            'price'                   => (float)$this->request->getPost('price'),
            'status'                  => $this->request->getPost('status'),
        ];

        $propertyId = $this->propertyModel->insert($propertyData);

        if (!$propertyId) {
            return redirect()->back()->withInput()->with('error', 'Failed to create property.');
        }

        // Sync amenities if selected
        $amenityIds = $this->request->getPost('amenities') ?? [];
        if (is_array($amenityIds)) {
            $this->propertyModel->syncAmenities((int)$propertyId, $amenityIds);
        }

        // Initialize status history
        $userId = session()->get('user_id');
        $this->statusHistoryModel->recordChange(
            (int)$propertyId,
            null,
            'None',
            $propertyData['status'],
            $userId,
            'Initial property registration'
        );

        // Record initial baseline pricing
        $pricePerSqft = $propertyData['area'] > 0 ? round($propertyData['price'] / $propertyData['area'], 2) : 0;
        $this->pricingModel->insert([
            'property_id'    => (int)$propertyId,
            'unit_id'        => null,
            'base_price'     => $propertyData['price'],
            'price_per_sqft' => $pricePerSqft,
            'market_price'   => $propertyData['price'],
            'discount'       => 0.00,
            'effective_from' => date('Y-m-d'),
            'remarks'        => 'Baseline property listing valuation',
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ]);

        AuditLogModel::record(
            'PROPERTY_CREATED',
            'Properties',
            (int)$propertyId,
            "Created property '{$propertyData['title']}' ({$propertyData['property_code']}) priced at " . number_format($propertyData['price'], 2)
        );

        return redirect()->to("/properties/view/{$propertyId}")->with('success', "Property '{$propertyData['title']}' created successfully.");
    }

    /**
     * Professional Tabbed Property Details Page
     */
    public function show($id)
    {
        $id = (int)$id;
        $property = $this->propertyModel->getPropertyFull($id);

        if (!$property) {
            return redirect()->to('/properties')->with('error', 'Property not found.');
        }

        // Associated amenities
        $amenities = $this->propertyModel->getAmenities($id);

        // Associated units
        $units = $this->unitModel->select('property_units.*, project_towers.tower_name')
                                 ->join('project_towers', 'project_towers.id = property_units.tower_id', 'left')
                                 ->where('property_units.property_id', $id)
                                 ->findAll();

        // Media files
        $media = $this->mediaModel->getMediaForProperty($id);

        // Pricing history
        $pricingHistory = $this->pricingModel->getPricingHistory($id);

        // Status history with user names
        $statusHistory = $this->statusHistoryModel->getHistoryForEntity($id);

        // Status options for status update modal
        $statuses = PropertyStatus::all();

        $data = [
            'title'          => "Property: {$property['title']} - Real Estate ERP",
            'property'       => $property,
            'amenities'      => $amenities,
            'units'          => $units,
            'media'          => $media,
            'pricingHistory' => $pricingHistory,
            'statusHistory'  => $statusHistory,
            'statuses'       => $statuses,
        ];

        return view('properties/view', $data);
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $id = (int)$id;
        $property = $this->propertyModel->find($id);

        if (!$property) {
            return redirect()->to('/properties')->with('error', 'Property not found.');
        }

        $types         = $this->propertyTypeModel->where('status', 'active')->orderBy('name', 'ASC')->findAll();
        $projects      = $this->projectModel->where('status !=', 'archived')->orderBy('name', 'ASC')->findAll();
        $locations     = $this->locationModel->where('status', 'active')->orderBy('city', 'ASC')->findAll();
        $amenities     = $this->amenityModel->where('status', 'active')->orderBy('name', 'ASC')->findAll();
        $currAmenities = $this->propertyModel->getAmenities($id);
        $currAmenityIds = array_column($currAmenities, 'id');
        $statuses      = PropertyStatus::all();

        $data = [
            'title'          => "Edit Property: {$property['title']} - Real Estate ERP",
            'property'       => $property,
            'types'          => $types,
            'projects'       => $projects,
            'locations'      => $locations,
            'amenities'      => $amenities,
            'currAmenityIds' => $currAmenityIds,
            'statuses'       => $statuses,
        ];

        return view('properties/edit', $data);
    }

    /**
     * Update property
     */
    public function update($id)
    {
        $id = (int)$id;
        $property = $this->propertyModel->find($id);

        if (!$property) {
            return redirect()->to('/properties')->with('error', 'Property not found.');
        }

        $rules = [
            'property_code'    => "required|min_length[2]|max_length[50]|is_unique[properties.property_code,id,{$id}]",
            'title'            => 'required|min_length[3]|max_length[200]',
            'property_type_id' => 'required|is_not_unique[property_types.id]',
            'location_id'      => 'required|is_not_unique[locations.id]',
            'project_id'       => 'permit_empty|is_not_unique[projects.id]',
            'area'             => 'required|numeric|greater_than[0]',
            'price'            => 'required|numeric|greater_than_equal_to[0]',
            'status'           => 'required|in_list[Available,Reserved,Under Negotiation,Booked,Sold,Rented,Under Maintenance,Unavailable]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $oldStatus = $property['status'];
        $newStatus = $this->request->getPost('status');

        $propertyData = [
            'property_code'           => strtoupper(trim($this->request->getPost('property_code'))),
            'title'                   => trim($this->request->getPost('title')),
            'description'             => trim($this->request->getPost('description') ?? ''),
            'property_type_id'        => (int)$this->request->getPost('property_type_id'),
            'project_id'              => $this->request->getPost('project_id') ? (int)$this->request->getPost('project_id') : null,
            'location_id'             => (int)$this->request->getPost('location_id'),
            'owner_name_or_reference' => trim($this->request->getPost('owner_name_or_reference') ?? ''),
            'ownership_details'       => trim($this->request->getPost('ownership_details') ?? ''),
            'area'                    => (float)$this->request->getPost('area'),
            'price'                   => (float)$this->request->getPost('price'),
            'status'                  => $newStatus,
        ];

        $this->propertyModel->update($id, $propertyData);

        // Sync amenities
        $amenityIds = $this->request->getPost('amenities') ?? [];
        if (is_array($amenityIds)) {
            $this->propertyModel->syncAmenities($id, $amenityIds);
        }

        // If status changed in edit form, record in history
        if ($oldStatus !== $newStatus) {
            $userId = session()->get('user_id');
            $this->statusHistoryModel->recordChange(
                $id,
                null,
                $oldStatus,
                $newStatus,
                $userId,
                'Status updated via property edit form'
            );
        }

        AuditLogModel::record(
            'PROPERTY_UPDATED',
            'Properties',
            $id,
            "Updated property '{$propertyData['title']}' ({$propertyData['property_code']})"
        );

        return redirect()->to("/properties/view/{$id}")->with('success', "Property '{$propertyData['title']}' updated successfully.");
    }

    /**
     * Soft delete property
     */
    public function delete($id)
    {
        $id = (int)$id;
        $property = $this->propertyModel->find($id);

        if (!$property) {
            return redirect()->to('/properties')->with('error', 'Property not found.');
        }

        $this->propertyModel->delete($id);

        AuditLogModel::record(
            'PROPERTY_DELETED',
            'Properties',
            $id,
            "Deleted property '{$property['title']}' ({$property['property_code']})"
        );

        return redirect()->to('/properties')->with('success', "Property '{$property['title']}' has been removed.");
    }
}
