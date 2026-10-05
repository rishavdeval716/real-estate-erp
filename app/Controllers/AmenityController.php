<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AmenityModel;
use App\Models\AuditLogModel;

class AmenityController extends BaseController
{
    protected $amenityModel;

    public function __construct()
    {
        $this->amenityModel = new AmenityModel();
    }

    /**
     * List amenities with search and status filtering
     */
    public function index()
    {
        $search = trim($this->request->getGet('search') ?? '');
        $status = trim($this->request->getGet('status') ?? '');

        $perPage   = 10;
        $amenities = $this->amenityModel->getFilteredAmenities($search, $status, $perPage);
        $pager     = $this->amenityModel->pager;

        // Count properties using each amenity
        $db = \Config\Database::connect();
        foreach ($amenities as &$am) {
            $am['usage_count'] = $db->table('property_amenities')
                                    ->where('amenity_id', $am['id'])
                                    ->countAllResults();
        }

        $data = [
            'title'     => 'Property Amenities - Real Estate ERP',
            'amenities' => $amenities,
            'pager'     => $pager,
            'search'    => $search,
            'status'    => $status,
        ];

        return view('amenities/index', $data);
    }

    /**
     * Show create amenity form
     */
    public function create()
    {
        $data = [
            'title' => 'Add Property Amenity - Real Estate ERP',
        ];

        return view('amenities/create', $data);
    }

    /**
     * Store new amenity
     */
    public function store()
    {
        $rules = [
            'name'        => 'required|min_length[2]|max_length[100]|is_unique[amenities.name]',
            'icon'        => 'permit_empty|max_length[100]',
            'description' => 'permit_empty|max_length[255]',
            'status'      => 'required|in_list[active,inactive]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $amenityData = [
            'name'        => trim($this->request->getPost('name')),
            'icon'        => trim($this->request->getPost('icon') ?? 'ri-checkbox-circle-line'),
            'description' => trim($this->request->getPost('description') ?? ''),
            'status'      => $this->request->getPost('status'),
        ];

        $amenityId = $this->amenityModel->insert($amenityData);

        if (!$amenityId) {
            return redirect()->back()->withInput()->with('error', 'Failed to create amenity.');
        }

        AuditLogModel::record(
            'AMENITY_CREATED',
            'Amenities',
            (int)$amenityId,
            "Created amenity '{$amenityData['name']}'"
        );

        return redirect()->to('/amenities')->with('success', "Amenity '{$amenityData['name']}' created successfully.");
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $id = (int)$id;
        $amenity = $this->amenityModel->find($id);

        if (!$amenity) {
            return redirect()->to('/amenities')->with('error', 'Amenity not found.');
        }

        $data = [
            'title'   => "Edit Amenity: {$amenity['name']} - Real Estate ERP",
            'amenity' => $amenity,
        ];

        return view('amenities/edit', $data);
    }

    /**
     * Update amenity
     */
    public function update($id)
    {
        $id = (int)$id;
        $amenity = $this->amenityModel->find($id);

        if (!$amenity) {
            return redirect()->to('/amenities')->with('error', 'Amenity not found.');
        }

        $rules = [
            'name'        => "required|min_length[2]|max_length[100]|is_unique[amenities.name,id,{$id}]",
            'icon'        => 'permit_empty|max_length[100]',
            'description' => 'permit_empty|max_length[255]',
            'status'      => 'required|in_list[active,inactive]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $amenityData = [
            'name'        => trim($this->request->getPost('name')),
            'icon'        => trim($this->request->getPost('icon') ?? 'ri-checkbox-circle-line'),
            'description' => trim($this->request->getPost('description') ?? ''),
            'status'      => $this->request->getPost('status'),
        ];

        $this->amenityModel->update($id, $amenityData);

        AuditLogModel::record(
            'AMENITY_UPDATED',
            'Amenities',
            $id,
            "Updated amenity '{$amenityData['name']}'"
        );

        return redirect()->to('/amenities')->with('success', "Amenity '{$amenityData['name']}' updated successfully.");
    }

    /**
     * Delete amenity
     */
    public function delete($id)
    {
        $id = (int)$id;
        $amenity = $this->amenityModel->find($id);

        if (!$amenity) {
            return redirect()->to('/amenities')->with('error', 'Amenity not found.');
        }

        $db = \Config\Database::connect();
        $usageCount = $db->table('property_amenities')->where('amenity_id', $id)->countAllResults();

        if ($usageCount > 0) {
            return redirect()->to('/amenities')->with('error', "Cannot delete amenity '{$amenity['name']}'. It is linked to {$usageCount} active property listing(s).");
        }

        $this->amenityModel->delete($id);

        AuditLogModel::record(
            'AMENITY_DELETED',
            'Amenities',
            $id,
            "Deleted amenity '{$amenity['name']}'"
        );

        return redirect()->to('/amenities')->with('success', "Amenity '{$amenity['name']}' deleted successfully.");
    }
}
