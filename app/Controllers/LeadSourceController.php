<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LeadSourceModel;
use App\Models\AuditLogModel;

class LeadSourceController extends BaseController
{
    protected $leadSourceModel;
    protected $auditModel;

    public function __construct()
    {
        $this->leadSourceModel = new LeadSourceModel();
        $this->auditModel      = new AuditLogModel();
    }

    /**
     * List all lead sources with search and pagination
     */
    public function index()
    {
        $search = trim($this->request->getGet('search') ?? '');
        $status = trim($this->request->getGet('status') ?? '');

        $builder = $this->leadSourceModel->where('deleted_at', null);

        if (!empty($search)) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('slug', $search)
                ->orLike('description', $search)
                ->groupEnd();
        }

        if (!empty($status)) {
            $builder->where('status', $status);
        }

        $sources = $builder->orderBy('name', 'ASC')->paginate(15);

        $data = [
            'title'   => 'Lead Sources - Real Estate CRM',
            'sources' => $sources,
            'pager'   => $this->leadSourceModel->pager,
            'search'  => $search,
            'status'  => $status,
        ];

        return view('lead_sources/index', $data);
    }

    /**
     * Show create source form
     */
    public function create()
    {
        $data = [
            'title' => 'Add Lead Source - Real Estate CRM',
        ];
        return view('lead_sources/create', $data);
    }

    /**
     * Store new lead source
     */
    public function store()
    {
        $name        = trim($this->request->getPost('name') ?? '');
        $slug        = trim($this->request->getPost('slug') ?? '');
        $description = trim($this->request->getPost('description') ?? '');
        $status      = $this->request->getPost('status') ?? 'active';

        if (empty($slug) && !empty($name)) {
            $slug = url_title($name, '-', true);
        }

        $inputData = [
            'name'        => $name,
            'slug'        => $slug,
            'description' => $description,
            'status'      => $status,
        ];

        $rules = [
            'name'   => 'required|min_length[2]|max_length[100]|is_unique[lead_sources.name]',
            'slug'   => 'required|min_length[2]|max_length[100]|regex_match[/^[a-z0-9-]+$/]|is_unique[lead_sources.slug]',
            'status' => 'required|in_list[active,inactive]',
        ];

        if (!$this->validateData($inputData, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $sourceId = $this->leadSourceModel->insert([
            'name'        => $name,
            'slug'        => $slug,
            'description' => $description,
            'status'      => $status,
        ]);

        $this->auditModel->record(
            session()->get('user_id'),
            'LEAD_SOURCE_CREATED',
            'LeadSources',
            $sourceId,
            "Created lead source: {$name}"
        );

        return redirect()->to('/lead-sources')->with('success', "Lead source '{$name}' created successfully.");
    }

    /**
     * Edit form
     */
    public function edit($id)
    {
        $source = $this->leadSourceModel->where('deleted_at', null)->find($id);
        if (!$source) {
            return redirect()->to('/lead-sources')->with('error', 'Lead source not found.');
        }

        $data = [
            'title'  => 'Edit Lead Source - Real Estate CRM',
            'source' => $source,
        ];
        return view('lead_sources/edit', $data);
    }

    /**
     * Update source
     */
    public function update($id)
    {
        $source = $this->leadSourceModel->where('deleted_at', null)->find($id);
        if (!$source) {
            return redirect()->to('/lead-sources')->with('error', 'Lead source not found.');
        }

        $name        = trim($this->request->getPost('name') ?? '');
        $slug        = trim($this->request->getPost('slug') ?? '');
        $description = trim($this->request->getPost('description') ?? '');
        $status      = $this->request->getPost('status') ?? 'active';

        if (empty($slug) && !empty($name)) {
            $slug = url_title($name, '-', true);
        }

        $inputData = [
            'name'        => $name,
            'slug'        => $slug,
            'description' => $description,
            'status'      => $status,
        ];

        $rules = [
            'name'   => "required|min_length[2]|max_length[100]|is_unique[lead_sources.name,id,{$id}]",
            'slug'   => "required|min_length[2]|max_length[100]|regex_match[/^[a-z0-9-]+$/]|is_unique[lead_sources.slug,id,{$id}]",
            'status' => 'required|in_list[active,inactive]',
        ];

        if (!$this->validateData($inputData, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->leadSourceModel->update($id, [
            'name'        => $name,
            'slug'        => $slug,
            'description' => $description,
            'status'      => $status,
        ]);

        $this->auditModel->record(
            session()->get('user_id'),
            'LEAD_SOURCE_UPDATED',
            'LeadSources',
            $id,
            "Updated lead source: {$name}"
        );

        return redirect()->to('/lead-sources')->with('success', "Lead source '{$name}' updated successfully.");
    }

    /**
     * Soft delete lead source
     */
    public function delete($id)
    {
        $source = $this->leadSourceModel->where('deleted_at', null)->find($id);
        if (!$source) {
            return redirect()->to('/lead-sources')->with('error', 'Lead source not found.');
        }

        $db = \Config\Database::connect();
        $leadCount = $db->table('leads')->where('lead_source_id', $id)->where('deleted_at', null)->countAllResults();
        if ($leadCount > 0) {
            return redirect()->to('/lead-sources')->with('error', "Cannot delete lead source '{$source['name']}' as it has {$leadCount} active leads associated.");
        }

        $this->leadSourceModel->delete($id);

        $this->auditModel->record(
            session()->get('user_id'),
            'LEAD_SOURCE_DELETED',
            'LeadSources',
            $id,
            "Soft deleted lead source: {$source['name']}"
        );

        return redirect()->to('/lead-sources')->with('success', "Lead source '{$source['name']}' deleted successfully.");
    }
}
