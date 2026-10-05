<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerCommunicationModel;
use App\Models\CustomerModel;
use App\Models\LeadModel;
use App\Models\AuditLogModel;

class CustomerCommunicationController extends BaseController
{
    protected $commModel;
    protected $customerModel;
    protected $leadModel;
    protected $auditModel;

    public function __construct()
    {
        $this->commModel     = new CustomerCommunicationModel();
        $this->customerModel = new CustomerModel();
        $this->leadModel     = new LeadModel();
        $this->auditModel    = new AuditLogModel();
    }

    public function index()
    {
        $channel = $this->request->getGet('channel');
        $purpose = $this->request->getGet('purpose');
        $search  = trim($this->request->getGet('search') ?? '');

        $builder = $this->commModel->select('customer_communications.*, customers.first_name as cust_fname, customers.last_name as cust_lname, leads.first_name as lead_fname, leads.last_name as lead_lname, users.name as staff_name')
            ->join('customers', 'customers.id = customer_communications.customer_id', 'left')
            ->join('leads', 'leads.id = customer_communications.lead_id', 'left')
            ->join('users', 'users.id = customer_communications.user_id', 'left');

        if ($channel) {
            $builder->where('customer_communications.channel', $channel);
        }
        if ($purpose) {
            $builder->where('customer_communications.purpose', $purpose);
        }
        if ($search) {
            $builder->groupStart()
                ->like('customer_communications.subject', $search)
                ->orLike('customer_communications.comm_code', $search)
                ->orLike('customer_communications.content', $search)
                ->groupEnd();
        }

        $communications = $builder->orderBy('customer_communications.communication_date', 'DESC')->get()->getResultArray();

        $totalComms = $this->commModel->countAllResults();
        $callCount  = $this->commModel->where('channel', 'Phone Call')->countAllResults();
        $waCount    = $this->commModel->where('channel', 'WhatsApp')->countAllResults();
        $meetCount  = $this->commModel->where('channel', 'In-Person Meeting')->countAllResults();

        $customers = $this->customerModel->where('deleted_at IS NULL')->findAll();
        $leads     = $this->leadModel->where('deleted_at IS NULL')->findAll();

        return view('communications/index', [
            'title'          => 'Customer Communication History & Logs',
            'communications' => $communications,
            'channel'        => $channel,
            'purpose'        => $purpose,
            'search'         => $search,
            'totalComms'     => $totalComms,
            'callCount'      => $callCount,
            'waCount'        => $waCount,
            'meetCount'      => $meetCount,
            'customers'      => $customers,
            'leads'          => $leads,
        ]);
    }

    public function store()
    {
        $rules = [
            'channel'            => 'required',
            'purpose'            => 'required',
            'subject'            => 'required|min_length[3]|max_length[200]',
            'content'            => 'required',
            'communication_date' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $code = $this->commModel->generateCommCode();
        $userId = session()->get('user_id') ?? 1;

        $data = [
            'comm_code'          => $code,
            'customer_id'        => $this->request->getPost('customer_id') ?: null,
            'lead_id'            => $this->request->getPost('lead_id') ?: null,
            'channel'            => $this->request->getPost('channel'),
            'purpose'            => $this->request->getPost('purpose'),
            'subject'            => trim($this->request->getPost('subject')),
            'content'            => trim($this->request->getPost('content')),
            'status'             => $this->request->getPost('status') ?? 'Completed',
            'communication_date' => $this->request->getPost('communication_date'),
            'user_id'            => $userId,
            'response_notes'     => trim($this->request->getPost('response_notes') ?? ''),
        ];

        $commId = $this->commModel->insert($data);

        $this->auditModel->log(
            'COMMUNICATION_LOGGED',
            "Communication {$code} logged via {$data['channel']} ({$data['purpose']})",
            'customer_communications',
            $commId
        );

        return redirect()->to('/communications')->with('success', "Communication {$code} logged successfully.");
    }
}
