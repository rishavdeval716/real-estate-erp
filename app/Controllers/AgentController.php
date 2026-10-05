<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AgentModel;
use App\Models\UserModel;
use App\Models\AuditLogModel;

class AgentController extends BaseController
{
    protected $agentModel;
    protected $userModel;
    protected $auditModel;

    public function __construct()
    {
        $this->agentModel = new AgentModel();
        $this->userModel  = new UserModel();
        $this->auditModel = new AuditLogModel();
    }

    public function index()
    {
        $search    = trim($this->request->getGet('search') ?? '');
        $agentType = trim($this->request->getGet('agent_type') ?? '');
        $status    = trim($this->request->getGet('status') ?? '');

        $builder = $this->agentModel->builder();
        if ($search) {
            $builder->groupStart()
                ->like('first_name', $search)
                ->orLike('last_name', $search)
                ->orLike('agent_code', $search)
                ->orLike('agency_name', $search)
                ->orLike('license_number', $search)
                ->orLike('email', $search)
                ->orLike('phone', $search)
                ->groupEnd();
        }
        if ($agentType) {
            $builder->where('agent_type', $agentType);
        }
        if ($status) {
            $builder->where('status', $status);
        }

        $agents = $builder->where('deleted_at IS NULL')
            ->orderBy('id', 'DESC')
            ->get()->getResultArray();

        $totalAgents    = $this->agentModel->countAllResults();
        $activeAgents   = $this->agentModel->where('status', 'active')->countAllResults();
        $brokersCount   = $this->agentModel->where('agent_type', 'External Broker')->countAllResults();
        $partnersCount  = $this->agentModel->where('agent_type', 'Channel Partner')->countAllResults();

        $data = [
            'title'          => 'Agent & Broker Management',
            'agents'         => $agents,
            'search'         => $search,
            'agentType'      => $agentType,
            'status'         => $status,
            'totalAgents'    => $totalAgents,
            'activeAgents'   => $activeAgents,
            'brokersCount'   => $brokersCount,
            'partnersCount'  => $partnersCount,
        ];

        return view('agents/index', $data);
    }

    public function create()
    {
        $users = $this->userModel->where('deleted_at IS NULL')->findAll();

        return view('agents/create', [
            'title' => 'Onboard Agent / Broker',
            'users' => $users,
        ]);
    }

    public function store()
    {
        $rules = [
            'first_name'      => 'required|min_length[2]|max_length[100]',
            'last_name'       => 'required|min_length[2]|max_length[100]',
            'email'           => 'required|valid_email|max_length[150]',
            'phone'           => 'required|min_length[8]|max_length[30]',
            'agent_type'      => 'required',
            'commission_rate' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $agentCode = $this->agentModel->generateAgentCode();
        $data = [
            'agent_code'      => $agentCode,
            'user_id'         => $this->request->getPost('user_id') ?: null,
            'agent_type'      => $this->request->getPost('agent_type'),
            'agency_name'     => trim($this->request->getPost('agency_name') ?? ''),
            'first_name'      => trim($this->request->getPost('first_name')),
            'last_name'       => trim($this->request->getPost('last_name')),
            'email'           => trim($this->request->getPost('email')),
            'phone'           => trim($this->request->getPost('phone')),
            'license_number'  => trim($this->request->getPost('license_number') ?? ''),
            'pan_number'      => trim($this->request->getPost('pan_number') ?? ''),
            'commission_rate' => (float)$this->request->getPost('commission_rate'),
            'status'          => $this->request->getPost('status') ?? 'active',
            'address'         => trim($this->request->getPost('address') ?? ''),
            'city'            => trim($this->request->getPost('city') ?? ''),
            'notes'           => trim($this->request->getPost('notes') ?? ''),
        ];

        $agentId = $this->agentModel->insert($data);

        $this->auditModel->log(
            'AGENT_CREATED',
            "Agent/Broker {$agentCode} ({$data['first_name']} {$data['last_name']}) onboarded",
            'agents',
            $agentId
        );

        return redirect()->to('/agents')->with('success', "Agent {$agentCode} onboarded successfully.");
    }

    public function show($id)
    {
        $agent = $this->agentModel->getAgentPerformance($id);
        if (!$agent) {
            return redirect()->to('/agents')->with('error', 'Agent not found.');
        }

        return view('agents/view', [
            'title' => "Agent Profile: {$agent['first_name']} {$agent['last_name']} ({$agent['agent_code']})",
            'agent' => $agent,
        ]);
    }

    public function edit($id)
    {
        $agent = $this->agentModel->find($id);
        if (!$agent) {
            return redirect()->to('/agents')->with('error', 'Agent not found.');
        }

        $users = $this->userModel->where('deleted_at IS NULL')->findAll();

        return view('agents/edit', [
            'title' => "Edit Agent: {$agent['agent_code']}",
            'agent' => $agent,
            'users' => $users,
        ]);
    }

    public function update($id)
    {
        $agent = $this->agentModel->find($id);
        if (!$agent) {
            return redirect()->to('/agents')->with('error', 'Agent not found.');
        }

        $rules = [
            'first_name'      => 'required|min_length[2]|max_length[100]',
            'last_name'       => 'required|min_length[2]|max_length[100]',
            'email'           => 'required|valid_email|max_length[150]',
            'phone'           => 'required|min_length[8]|max_length[30]',
            'agent_type'      => 'required',
            'commission_rate' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'user_id'         => $this->request->getPost('user_id') ?: null,
            'agent_type'      => $this->request->getPost('agent_type'),
            'agency_name'     => trim($this->request->getPost('agency_name') ?? ''),
            'first_name'      => trim($this->request->getPost('first_name')),
            'last_name'       => trim($this->request->getPost('last_name')),
            'email'           => trim($this->request->getPost('email')),
            'phone'           => trim($this->request->getPost('phone')),
            'license_number'  => trim($this->request->getPost('license_number') ?? ''),
            'pan_number'      => trim($this->request->getPost('pan_number') ?? ''),
            'commission_rate' => (float)$this->request->getPost('commission_rate'),
            'status'          => $this->request->getPost('status') ?? 'active',
            'address'         => trim($this->request->getPost('address') ?? ''),
            'city'            => trim($this->request->getPost('city') ?? ''),
            'notes'           => trim($this->request->getPost('notes') ?? ''),
        ];

        $this->agentModel->update($id, $data);

        $this->auditModel->log(
            'AGENT_UPDATED',
            "Agent/Broker {$agent['agent_code']} updated",
            'agents',
            $id
        );

        return redirect()->to('/agents/view/' . $id)->with('success', 'Agent profile updated successfully.');
    }

    public function delete($id)
    {
        $agent = $this->agentModel->find($id);
        if (!$agent) {
            return redirect()->to('/agents')->with('error', 'Agent not found.');
        }

        $this->agentModel->delete($id);

        $this->auditModel->log(
            'AGENT_DELETED',
            "Agent/Broker {$agent['agent_code']} deleted",
            'agents',
            $id
        );

        return redirect()->to('/agents')->with('success', "Agent {$agent['agent_code']} deleted successfully.");
    }
}
