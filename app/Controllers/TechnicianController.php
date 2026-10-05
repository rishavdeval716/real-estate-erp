<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TechnicianModel;
use App\Models\AuditLogModel;

class TechnicianController extends BaseController
{
    protected $techModel;
    protected $auditModel;

    public function __construct()
    {
        $this->techModel  = new TechnicianModel();
        $this->auditModel = new AuditLogModel();
    }

    public function index()
    {
        $availability = trim($this->request->getGet('availability') ?? '');
        $status       = trim($this->request->getGet('status') ?? '');
        $search       = trim($this->request->getGet('search') ?? '');

        $builder = $this->techModel;

        if ($availability !== '') {
            $builder->where('availability', $availability);
        }
        if ($status !== '') {
            $builder->where('status', $status);
        }
        if ($search !== '') {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('technician_code', $search)
                ->orLike('skill', $search)
                ->orLike('department', $search)
                ->orLike('mobile', $search)
                ->groupEnd();
        }

        $technicians = $builder->orderBy('id', 'DESC')->paginate(15);
        $pager       = $this->techModel->pager;

        $db = \Config\Database::connect();
        $kpi = [
            'total'     => $db->table('technicians')->countAllResults(),
            'available' => $db->table('technicians')->where('availability', 'available')->where('status', 'active')->countAllResults(),
            'busy'      => $db->table('technicians')->where('availability', 'busy')->countAllResults(),
            'on_leave'  => $db->table('technicians')->where('availability', 'on_leave')->countAllResults(),
        ];

        return view('maintenance/technician_index', [
            'title'       => 'Technician Directory - Real Estate ERP',
            'technicians' => $technicians,
            'pager'       => $pager,
            'filters'     => ['availability' => $availability, 'status' => $status, 'search' => $search],
            'kpi'         => $kpi,
        ]);
    }

    public function store()
    {
        $rules = [
            'name'         => 'required|min_length[3]|max_length[255]',
            'mobile'       => 'required|min_length[8]|max_length[20]',
            'email'        => 'required|valid_email|max_length[100]',
            'skill'        => 'required|max_length[100]',
            'department'   => 'required|max_length[100]',
            'availability' => 'required|in_list[available,busy,on_leave]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $code   = $this->techModel->generateTechnicianCode();
        $userId = session()->get('user_id');

        $data = [
            'technician_code' => $code,
            'name'            => trim($this->request->getPost('name')),
            'mobile'          => trim($this->request->getPost('mobile')),
            'email'           => trim($this->request->getPost('email')),
            'skill'           => trim($this->request->getPost('skill')),
            'department'      => trim($this->request->getPost('department')),
            'availability'    => $this->request->getPost('availability') ?? 'available',
            'status'          => $this->request->getPost('status') ?? 'active',
        ];

        $techId = $this->techModel->insert($data);

        $this->auditModel->record(
            $userId,
            'TECHNICIAN_CREATED',
            'Technicians',
            $techId,
            "Created technician {$code} - {$data['name']}"
        );

        return redirect()->to(base_url('technicians'))->with('success', "Technician {$code} registered successfully.");
    }

    public function update(int $id)
    {
        $tech = $this->techModel->find($id);
        if (!$tech) {
            return redirect()->to(base_url('technicians'))->with('error', 'Technician not found.');
        }

        $rules = [
            'name'         => 'required|min_length[3]|max_length[255]',
            'mobile'       => 'required|min_length[8]|max_length[20]',
            'email'        => 'required|valid_email|max_length[100]',
            'skill'        => 'required|max_length[100]',
            'department'   => 'required|max_length[100]',
            'availability' => 'required|in_list[available,busy,on_leave]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name'         => trim($this->request->getPost('name')),
            'mobile'       => trim($this->request->getPost('mobile')),
            'email'        => trim($this->request->getPost('email')),
            'skill'        => trim($this->request->getPost('skill')),
            'department'   => trim($this->request->getPost('department')),
            'availability' => $this->request->getPost('availability'),
            'status'       => $this->request->getPost('status') ?? $tech['status'],
        ];

        $this->techModel->update($id, $data);

        $this->auditModel->record(
            session()->get('user_id'),
            'TECHNICIAN_UPDATED',
            'Technicians',
            $id,
            "Updated technician {$tech['technician_code']}"
        );

        return redirect()->to(base_url('technicians'))->with('success', "Technician {$tech['technician_code']} updated.");
    }

    public function delete(int $id)
    {
        $tech = $this->techModel->find($id);
        if (!$tech) {
            return redirect()->to(base_url('technicians'))->with('error', 'Technician not found.');
        }

        $this->techModel->delete($id);

        $this->auditModel->record(
            session()->get('user_id'),
            'TECHNICIAN_DELETED',
            'Technicians',
            $id,
            "Deleted technician {$tech['technician_code']}"
        );

        return redirect()->to(base_url('technicians'))->with('success', "Technician {$tech['technician_code']} deleted.");
    }
}
