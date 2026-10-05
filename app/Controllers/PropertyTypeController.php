<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PropertyTypeModel;
use App\Models\PropertyModel;
use App\Models\AuditLogModel;

class PropertyTypeController extends BaseController
{
    protected $propertyTypeModel;

    public function __construct()
    {
        $this->propertyTypeModel = new PropertyTypeModel();
    }

    /**
     * List all property types with search and status filtering
     */
    public function index()
    {
        $search = trim($this->request->getGet('search') ?? '');
        $status = trim($this->request->getGet('status') ?? '');

        $perPage = 10;
        $types   = $this->propertyTypeModel->getFilteredTypes($search, $status, $perPage);
        $pager   = $this->propertyTypeModel->pager;

        // Count properties using each type
        $propertyModel = new PropertyModel();
        foreach ($types as &$t) {
            $t['property_count'] = $propertyModel->where('property_type_id', $t['id'])->countAllResults();
        }

        $data = [
            'title'  => 'Property Types - Real Estate ERP',
            'types'  => $types,
            'pager'  => $pager,
            'search' => $search,
            'status' => $status,
        ];

        return view('property_types/index', $data);
    }

    /**
     * Show form to create new property type
     */
    public function create()
    {
        $data = [
            'title' => 'Create Property Type - Real Estate ERP',
        ];

        return view('property_types/create', $data);
    }

    /**
     * Store newly created property type
     */
    public function store()
    {
        $rules = [
            'name'        => 'required|min_length[2]|max_length[100]|is_unique[property_types.name]',
            'slug'        => 'permit_empty|min_length[2]|max_length[100]|is_unique[property_types.slug]',
            'description' => 'permit_empty|max_length[500]',
            'status'      => 'required|in_list[active,inactive]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name = trim($this->request->getPost('name'));
        $slug = trim($this->request->getPost('slug'));
        if (empty($slug)) {
            $slug = url_title($name, '-', true);
        }

        $typeData = [
            'name'        => $name,
            'slug'        => $slug,
            'description' => trim($this->request->getPost('description') ?? ''),
            'status'      => $this->request->getPost('status'),
        ];

        $typeId = $this->propertyTypeModel->insert($typeData);

        if (!$typeId) {
            return redirect()->back()->withInput()->with('error', 'Failed to create property type.');
        }

        AuditLogModel::record(
            'PROPERTY_TYPE_CREATED',
            'Property Types',
            (int)$typeId,
            "Created property type '{$name}' ({$slug})"
        );

        return redirect()->to('/property-types')->with('success', "Property type '{$name}' created successfully.");
    }

    /**
     * View property type details and assigned properties
     */
    public function show($id)
    {
        $id = (int)$id;
        $type = $this->propertyTypeModel->find($id);

        if (!$type) {
            return redirect()->to('/property-types')->with('error', 'Property type not found.');
        }

        $propertyModel = new PropertyModel();
        $properties = $propertyModel->where('property_type_id', $id)->findAll();

        $data = [
            'title'      => "Property Type: {$type['name']} - Real Estate ERP",
            'type'       => $type,
            'properties' => $properties,
        ];

        return view('property_types/view', $data);
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $id = (int)$id;
        $type = $this->propertyTypeModel->find($id);

        if (!$type) {
            return redirect()->to('/property-types')->with('error', 'Property type not found.');
        }

        $data = [
            'title' => "Edit Property Type: {$type['name']} - Real Estate ERP",
            'type'  => $type,
        ];

        return view('property_types/edit', $data);
    }

    /**
     * Update existing property type
     */
    public function update($id)
    {
        $id = (int)$id;
        $type = $this->propertyTypeModel->find($id);

        if (!$type) {
            return redirect()->to('/property-types')->with('error', 'Property type not found.');
        }

        $rules = [
            'name'        => "required|min_length[2]|max_length[100]|is_unique[property_types.name,id,{$id}]",
            'slug'        => "permit_empty|min_length[2]|max_length[100]|is_unique[property_types.slug,id,{$id}]",
            'description' => 'permit_empty|max_length[500]',
            'status'      => 'required|in_list[active,inactive]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name = trim($this->request->getPost('name'));
        $slug = trim($this->request->getPost('slug'));
        if (empty($slug)) {
            $slug = url_title($name, '-', true);
        }

        $typeData = [
            'name'        => $name,
            'slug'        => $slug,
            'description' => trim($this->request->getPost('description') ?? ''),
            'status'      => $this->request->getPost('status'),
        ];

        $this->propertyTypeModel->update($id, $typeData);

        AuditLogModel::record(
            'PROPERTY_TYPE_UPDATED',
            'Property Types',
            $id,
            "Updated property type '{$name}'"
        );

        return redirect()->to('/property-types')->with('success', "Property type '{$name}' updated successfully.");
    }

    /**
     * Soft delete property type
     */
    public function delete($id)
    {
        $id = (int)$id;
        $type = $this->propertyTypeModel->find($id);

        if (!$type) {
            return redirect()->to('/property-types')->with('error', 'Property type not found.');
        }

        // Business rule: Check if active properties are assigned to this type
        $propertyModel = new PropertyModel();
        $assignedCount = $propertyModel->where('property_type_id', $id)->countAllResults();

        if ($assignedCount > 0) {
            return redirect()->to('/property-types')->with('error', "Cannot delete '{$type['name']}' because {$assignedCount} active properties are assigned to it.");
        }

        $this->propertyTypeModel->delete($id);

        AuditLogModel::record(
            'PROPERTY_TYPE_DELETED',
            'Property Types',
            $id,
            "Deleted property type '{$type['name']}'"
        );

        return redirect()->to('/property-types')->with('success', "Property type '{$type['name']}' removed.");
    }
}
