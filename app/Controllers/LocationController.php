<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LocationModel;
use App\Models\PropertyModel;
use App\Models\ProjectModel;
use App\Models\AuditLogModel;

class LocationController extends BaseController
{
    protected $locationModel;

    public function __construct()
    {
        $this->locationModel = new LocationModel();
    }

    /**
     * List locations with search, city filter, and pagination
     */
    public function index()
    {
        $search = trim($this->request->getGet('search') ?? '');
        $city   = trim($this->request->getGet('city') ?? '');
        $status = trim($this->request->getGet('status') ?? '');

        $perPage   = 10;
        $locations = $this->locationModel->getFilteredLocations($search, $city, $status, $perPage);
        $pager     = $this->locationModel->pager;
        $cities    = $this->locationModel->getDistinctCities();

        // Count properties and projects for each location
        $propertyModel = new PropertyModel();
        $projectModel  = new ProjectModel();
        foreach ($locations as &$loc) {
            $loc['property_count'] = $propertyModel->where('location_id', $loc['id'])->countAllResults();
            $loc['project_count']  = $projectModel->where('location_id', $loc['id'])->countAllResults();
        }

        $data = [
            'title'     => 'Location Management - Real Estate ERP',
            'locations' => $locations,
            'pager'     => $pager,
            'search'    => $search,
            'city'      => $city,
            'status'    => $status,
            'cities'    => $cities,
        ];

        return view('locations/index', $data);
    }

    /**
     * Show create location form
     */
    public function create()
    {
        $data = [
            'title' => 'Create New Location - Real Estate ERP',
        ];

        return view('locations/create', $data);
    }

    /**
     * Store newly created location
     */
    public function store()
    {
        $rules = [
            'state'            => 'required|min_length[2]|max_length[100]',
            'city'             => 'required|min_length[2]|max_length[100]',
            'area'             => 'required|min_length[2]|max_length[150]',
            'locality'         => 'permit_empty|max_length[150]',
            'landmark'         => 'permit_empty|max_length[150]',
            'pincode'          => 'required|min_length[3]|max_length[20]',
            'nearby_locations' => 'permit_empty|max_length[500]',
            'map_location'     => 'permit_empty|max_length[255]',
            'status'           => 'required|in_list[active,inactive]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $locationData = [
            'state'            => trim($this->request->getPost('state')),
            'city'             => trim($this->request->getPost('city')),
            'area'             => trim($this->request->getPost('area')),
            'locality'         => trim($this->request->getPost('locality') ?? ''),
            'landmark'         => trim($this->request->getPost('landmark') ?? ''),
            'pincode'          => trim($this->request->getPost('pincode')),
            'nearby_locations' => trim($this->request->getPost('nearby_locations') ?? ''),
            'map_location'     => trim($this->request->getPost('map_location') ?? ''),
            'status'           => $this->request->getPost('status'),
        ];

        $locationId = $this->locationModel->insert($locationData);

        if (!$locationId) {
            return redirect()->back()->withInput()->with('error', 'Failed to create location.');
        }

        AuditLogModel::record(
            'LOCATION_CREATED',
            'Locations',
            (int)$locationId,
            "Created location {$locationData['area']}, {$locationData['city']} ({$locationData['state']})"
        );

        return redirect()->to('/locations')->with('success', "Location '{$locationData['area']}, {$locationData['city']}' created successfully.");
    }

    /**
     * View location details with associated projects and properties
     */
    public function show($id)
    {
        $id = (int)$id;
        $location = $this->locationModel->find($id);

        if (!$location) {
            return redirect()->to('/locations')->with('error', 'Location not found.');
        }

        $projectModel  = new ProjectModel();
        $propertyModel = new PropertyModel();

        $projects   = $projectModel->where('location_id', $id)->findAll();
        $properties = $propertyModel->where('location_id', $id)->findAll();

        $data = [
            'title'      => "Location: {$location['area']}, {$location['city']} - Real Estate ERP",
            'location'   => $location,
            'projects'   => $projects,
            'properties' => $properties,
        ];

        return view('locations/view', $data);
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $id = (int)$id;
        $location = $this->locationModel->find($id);

        if (!$location) {
            return redirect()->to('/locations')->with('error', 'Location not found.');
        }

        $data = [
            'title'    => "Edit Location: {$location['area']}, {$location['city']} - Real Estate ERP",
            'location' => $location,
        ];

        return view('locations/edit', $data);
    }

    /**
     * Update location details
     */
    public function update($id)
    {
        $id = (int)$id;
        $location = $this->locationModel->find($id);

        if (!$location) {
            return redirect()->to('/locations')->with('error', 'Location not found.');
        }

        $rules = [
            'state'            => 'required|min_length[2]|max_length[100]',
            'city'             => 'required|min_length[2]|max_length[100]',
            'area'             => 'required|min_length[2]|max_length[150]',
            'locality'         => 'permit_empty|max_length[150]',
            'landmark'         => 'permit_empty|max_length[150]',
            'pincode'          => 'required|min_length[3]|max_length[20]',
            'nearby_locations' => 'permit_empty|max_length[500]',
            'map_location'     => 'permit_empty|max_length[255]',
            'status'           => 'required|in_list[active,inactive]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $locationData = [
            'state'            => trim($this->request->getPost('state')),
            'city'             => trim($this->request->getPost('city')),
            'area'             => trim($this->request->getPost('area')),
            'locality'         => trim($this->request->getPost('locality') ?? ''),
            'landmark'         => trim($this->request->getPost('landmark') ?? ''),
            'pincode'          => trim($this->request->getPost('pincode')),
            'nearby_locations' => trim($this->request->getPost('nearby_locations') ?? ''),
            'map_location'     => trim($this->request->getPost('map_location') ?? ''),
            'status'           => $this->request->getPost('status'),
        ];

        $this->locationModel->update($id, $locationData);

        AuditLogModel::record(
            'LOCATION_UPDATED',
            'Locations',
            $id,
            "Updated location {$locationData['area']}, {$locationData['city']}"
        );

        return redirect()->to('/locations')->with('success', "Location updated successfully.");
    }

    /**
     * Soft delete location
     */
    public function delete($id)
    {
        $id = (int)$id;
        $location = $this->locationModel->find($id);

        if (!$location) {
            return redirect()->to('/locations')->with('error', 'Location not found.');
        }

        $projectModel  = new ProjectModel();
        $propertyModel = new PropertyModel();

        $projectCount  = $projectModel->where('location_id', $id)->countAllResults();
        $propertyCount = $propertyModel->where('location_id', $id)->countAllResults();

        if ($projectCount > 0 || $propertyCount > 0) {
            return redirect()->to('/locations')->with('error', "Cannot delete location. It is currently referenced by {$projectCount} project(s) and {$propertyCount} property/properties.");
        }

        $this->locationModel->delete($id);

        AuditLogModel::record(
            'LOCATION_DELETED',
            'Locations',
            $id,
            "Deleted location {$location['area']}, {$location['city']}"
        );

        return redirect()->to('/locations')->with('success', "Location removed successfully.");
    }
}
