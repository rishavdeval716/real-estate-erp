<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MarketingCampaignModel;
use App\Models\ProjectModel;
use App\Models\PropertyModel;
use App\Models\AuditLogModel;

class MarketingCampaignController extends BaseController
{
    protected $campaignModel;
    protected $projectModel;
    protected $propertyModel;
    protected $auditModel;

    public function __construct()
    {
        $this->campaignModel = new MarketingCampaignModel();
        $this->projectModel  = new ProjectModel();
        $this->propertyModel = new PropertyModel();
        $this->auditModel    = new AuditLogModel();
    }

    public function index()
    {
        $type   = $this->request->getGet('type');
        $status = $this->request->getGet('status');
        $search = trim($this->request->getGet('search') ?? '');

        $builder = $this->campaignModel->builder();
        if ($type) {
            $builder->where('campaign_type', $type);
        }
        if ($status) {
            $builder->where('status', $status);
        }
        if ($search) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('campaign_code', $search)
                ->groupEnd();
        }

        $campaigns = $builder->where('deleted_at IS NULL')
            ->orderBy('id', 'DESC')
            ->get()->getResultArray();

        $metrics = $this->campaignModel->getDashboardMetrics();

        return view('campaigns/index', [
            'title'     => 'Marketing & Advertisement Management',
            'campaigns' => $campaigns,
            'metrics'   => $metrics,
            'type'      => $type,
            'status'    => $status,
            'search'    => $search,
        ]);
    }

    public function create()
    {
        $projects   = $this->projectModel->findAll();
        $properties = $this->propertyModel->where('deleted_at IS NULL')->findAll();

        return view('campaigns/create', [
            'title'      => 'Launch Marketing Campaign',
            'projects'   => $projects,
            'properties' => $properties,
        ]);
    }

    public function store()
    {
        $rules = [
            'name'          => 'required|min_length[3]|max_length[200]',
            'campaign_type' => 'required',
            'start_date'    => 'required|valid_date',
            'budget'        => 'required|numeric|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $code = $this->campaignModel->generateCampaignCode();
        $userId = session()->get('user_id') ?? 1;

        $data = [
            'campaign_code'   => $code,
            'name'            => trim($this->request->getPost('name')),
            'campaign_type'   => $this->request->getPost('campaign_type'),
            'project_id'      => $this->request->getPost('project_id') ?: null,
            'property_id'     => $this->request->getPost('property_id') ?: null,
            'start_date'      => $this->request->getPost('start_date'),
            'end_date'        => $this->request->getPost('end_date') ?: null,
            'budget'          => (float)$this->request->getPost('budget'),
            'actual_spend'    => (float)($this->request->getPost('actual_spend') ?? 0),
            'leads_generated' => (int)($this->request->getPost('leads_generated') ?? 0),
            'qualified_leads' => (int)($this->request->getPost('qualified_leads') ?? 0),
            'converted_leads' => (int)($this->request->getPost('converted_leads') ?? 0),
            'status'          => $this->request->getPost('status') ?? 'Active',
            'description'     => trim($this->request->getPost('description') ?? ''),
            'notes'           => trim($this->request->getPost('notes') ?? ''),
            'created_by'      => $userId,
        ];

        $campId = $this->campaignModel->insert($data);

        $this->auditModel->log(
            'CAMPAIGN_CREATED',
            "Marketing Campaign {$code} ({$data['name']}) created with budget ₹" . number_format($data['budget'], 2),
            'marketing_campaigns',
            $campId
        );

        return redirect()->to('/campaigns')->with('success', "Campaign {$code} created successfully.");
    }

    public function show($id)
    {
        $campaign = $this->campaignModel->find($id);
        if (!$campaign) {
            return redirect()->to('/campaigns')->with('error', 'Campaign not found.');
        }

        $db = \Config\Database::connect();
        $project = $campaign['project_id'] ? $db->table('projects')->where('id', $campaign['project_id'])->get()->getRowArray() : null;
        $property= $campaign['property_id'] ? $db->table('properties')->where('id', $campaign['property_id'])->get()->getRowArray() : null;

        $cpl = $campaign['leads_generated'] > 0 ? round($campaign['actual_spend'] / $campaign['leads_generated'], 2) : 0;
        $conversionRate = $campaign['leads_generated'] > 0 ? round(($campaign['converted_leads'] / $campaign['leads_generated']) * 100, 1) : 0;

        return view('campaigns/view', [
            'title'          => "Campaign: {$campaign['name']} ({$campaign['campaign_code']})",
            'campaign'       => $campaign,
            'project'        => $project,
            'property'       => $property,
            'cpl'            => $cpl,
            'conversionRate' => $conversionRate,
        ]);
    }

    public function edit($id)
    {
        $campaign = $this->campaignModel->find($id);
        if (!$campaign) {
            return redirect()->to('/campaigns')->with('error', 'Campaign not found.');
        }

        $projects   = $this->projectModel->findAll();
        $properties = $this->propertyModel->where('deleted_at IS NULL')->findAll();

        return view('campaigns/edit', [
            'title'      => "Edit Campaign: {$campaign['campaign_code']}",
            'campaign'   => $campaign,
            'projects'   => $projects,
            'properties' => $properties,
        ]);
    }

    public function update($id)
    {
        $campaign = $this->campaignModel->find($id);
        if (!$campaign) {
            return redirect()->to('/campaigns')->with('error', 'Campaign not found.');
        }

        $rules = [
            'name'          => 'required|min_length[3]|max_length[200]',
            'campaign_type' => 'required',
            'start_date'    => 'required|valid_date',
            'budget'        => 'required|numeric|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name'            => trim($this->request->getPost('name')),
            'campaign_type'   => $this->request->getPost('campaign_type'),
            'project_id'      => $this->request->getPost('project_id') ?: null,
            'property_id'     => $this->request->getPost('property_id') ?: null,
            'start_date'      => $this->request->getPost('start_date'),
            'end_date'        => $this->request->getPost('end_date') ?: null,
            'budget'          => (float)$this->request->getPost('budget'),
            'actual_spend'    => (float)($this->request->getPost('actual_spend') ?? 0),
            'leads_generated' => (int)($this->request->getPost('leads_generated') ?? 0),
            'qualified_leads' => (int)($this->request->getPost('qualified_leads') ?? 0),
            'converted_leads' => (int)($this->request->getPost('converted_leads') ?? 0),
            'status'          => $this->request->getPost('status') ?? 'Active',
            'description'     => trim($this->request->getPost('description') ?? ''),
            'notes'           => trim($this->request->getPost('notes') ?? ''),
        ];

        $this->campaignModel->update($id, $data);

        $this->auditModel->log(
            'CAMPAIGN_UPDATED',
            "Marketing Campaign {$campaign['campaign_code']} updated",
            'marketing_campaigns',
            $id
        );

        return redirect()->to('/campaigns/view/' . $id)->with('success', 'Campaign updated successfully.');
    }

    public function delete($id)
    {
        $campaign = $this->campaignModel->find($id);
        if (!$campaign) {
            return redirect()->to('/campaigns')->with('error', 'Campaign not found.');
        }

        $this->campaignModel->delete($id);

        $this->auditModel->log(
            'CAMPAIGN_DELETED',
            "Marketing Campaign {$campaign['campaign_code']} deleted",
            'marketing_campaigns',
            $id
        );

        return redirect()->to('/campaigns')->with('success', "Campaign {$campaign['campaign_code']} deleted successfully.");
    }
}
