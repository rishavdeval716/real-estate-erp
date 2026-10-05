<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BranchModel;
use App\Models\CompanyModel;
use App\Models\UserModel;
use App\Models\AuditLogModel;

class BranchController extends BaseController
{
    protected $branchModel;
    protected $companyModel;

    public function __construct()
    {
        $this->branchModel  = new BranchModel();
        $this->companyModel = new CompanyModel();
    }

    /**
     * Branch listing with search, status filtering, and pagination
     */
    public function index()
    {
        $search = trim($this->request->getGet('search') ?? '');
        $status = trim($this->request->getGet('status') ?? '');

        $perPage  = 10;
        $branches = $this->branchModel->getFilteredBranches($search, $status, $perPage);
        $pager    = $this->branchModel->pager;

        // Add user count to each branch
        $userModel = new UserModel();
        foreach ($branches as &$b) {
            $b['user_count'] = $userModel->where('branch_id', $b['id'])->countAllResults();
        }

        $data = [
            'title'    => 'Branch Management - Real Estate ERP',
            'branches' => $branches,
            'pager'    => $pager,
            'search'   => $search,
            'status'   => $status,
        ];

        return view('branches/index', $data);
    }

    /**
     * Show create branch form
     */
    public function create()
    {
        $companies = $this->companyModel->where('status', 'active')->findAll();

        $data = [
            'title'     => 'Create New Branch - Real Estate ERP',
            'companies' => $companies,
        ];

        return view('branches/create', $data);
    }

    /**
     * Store new branch
     */
    public function store()
    {
        $rules = [
            'company_id'   => 'required|is_not_unique[companies.id]',
            'name'         => 'required|min_length[3]|max_length[150]',
            'code'         => 'required|min_length[2]|max_length[50]|is_unique[branches.code]',
            'manager_name' => 'permit_empty|max_length[150]',
            'email'        => 'permit_empty|valid_email|max_length[150]',
            'phone'        => 'permit_empty|max_length[30]',
            'city'         => 'permit_empty|max_length[100]',
            'state'        => 'permit_empty|max_length[100]',
            'country'      => 'permit_empty|max_length[100]',
            'pincode'      => 'permit_empty|max_length[20]',
            'status'       => 'required|in_list[active,inactive]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $branchData = [
            'company_id'   => (int) $this->request->getPost('company_id'),
            'name'         => trim($this->request->getPost('name')),
            'code'         => strtoupper(trim($this->request->getPost('code'))),
            'manager_name' => trim($this->request->getPost('manager_name')),
            'email'        => trim($this->request->getPost('email')),
            'phone'        => trim($this->request->getPost('phone')),
            'address'      => trim($this->request->getPost('address')),
            'city'         => trim($this->request->getPost('city')),
            'state'        => trim($this->request->getPost('state')),
            'country'      => trim($this->request->getPost('country') ?: 'India'),
            'pincode'      => trim($this->request->getPost('pincode')),
            'status'       => $this->request->getPost('status'),
        ];

        $branchId = $this->branchModel->insert($branchData);

        if (!$branchId) {
            return redirect()->back()->withInput()->with('error', 'Failed to create branch.');
        }

        AuditLogModel::record(
            'BRANCH_CREATED',
            'Branches',
            (int) $branchId,
            "Created branch '{$branchData['name']}' with code {$branchData['code']}"
        );

        return redirect()->to('/branches')->with('success', "Branch '{$branchData['name']}' successfully established.");
    }

    /**
     * View branch details and stationed users
     */
    public function show($id)
    {
        $id = (int) $id;
        $branch = $this->branchModel->select('branches.*, companies.name as company_name')
                                    ->join('companies', 'companies.id = branches.company_id', 'left')
                                    ->find($id);

        if (!$branch) {
            return redirect()->to('/branches')->with('error', 'Branch not found.');
        }

        $userModel = new UserModel();
        $users = $userModel->where('branch_id', $id)->findAll();

        $data = [
            'title'  => "Branch: {$branch['name']} - Real Estate ERP",
            'branch' => $branch,
            'users'  => $users,
        ];

        return view('branches/view', $data);
    }

    /**
     * Show edit branch form
     */
    public function edit($id)
    {
        $id = (int) $id;
        $branch = $this->branchModel->find($id);

        if (!$branch) {
            return redirect()->to('/branches')->with('error', 'Branch not found.');
        }

        $companies = $this->companyModel->where('status', 'active')->findAll();

        $data = [
            'title'     => "Edit Branch: {$branch['name']} - Real Estate ERP",
            'branch'    => $branch,
            'companies' => $companies,
        ];

        return view('branches/edit', $data);
    }

    /**
     * Update branch details
     */
    public function update($id)
    {
        $id = (int) $id;
        $branch = $this->branchModel->find($id);

        if (!$branch) {
            return redirect()->to('/branches')->with('error', 'Branch not found.');
        }

        $rules = [
            'company_id'   => 'required|is_not_unique[companies.id]',
            'name'         => 'required|min_length[3]|max_length[150]',
            'code'         => "required|min_length[2]|max_length[50]|is_unique[branches.code,id,{$id}]",
            'manager_name' => 'permit_empty|max_length[150]',
            'email'        => 'permit_empty|valid_email|max_length[150]',
            'phone'        => 'permit_empty|max_length[30]',
            'city'         => 'permit_empty|max_length[100]',
            'state'        => 'permit_empty|max_length[100]',
            'country'      => 'permit_empty|max_length[100]',
            'pincode'      => 'permit_empty|max_length[20]',
            'status'       => 'required|in_list[active,inactive]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $branchData = [
            'company_id'   => (int) $this->request->getPost('company_id'),
            'name'         => trim($this->request->getPost('name')),
            'code'         => strtoupper(trim($this->request->getPost('code'))),
            'manager_name' => trim($this->request->getPost('manager_name')),
            'email'        => trim($this->request->getPost('email')),
            'phone'        => trim($this->request->getPost('phone')),
            'address'      => trim($this->request->getPost('address')),
            'city'         => trim($this->request->getPost('city')),
            'state'        => trim($this->request->getPost('state')),
            'country'      => trim($this->request->getPost('country') ?: 'India'),
            'pincode'      => trim($this->request->getPost('pincode')),
            'status'       => $this->request->getPost('status'),
        ];

        $this->branchModel->update($id, $branchData);

        AuditLogModel::record(
            'BRANCH_UPDATED',
            'Branches',
            $id,
            "Updated branch {$branchData['name']} ({$branchData['code']})"
        );

        return redirect()->to('/branches')->with('success', "Branch '{$branchData['name']}' updated successfully.");
    }

    /**
     * Soft delete branch
     */
    public function delete($id)
    {
        $id = (int) $id;
        $branch = $this->branchModel->find($id);

        if (!$branch) {
            return redirect()->to('/branches')->with('error', 'Branch not found.');
        }

        $this->branchModel->delete($id);

        AuditLogModel::record(
            'BRANCH_DELETED',
            'Branches',
            $id,
            "Soft deleted branch '{$branch['name']}' ({$branch['code']})"
        );

        return redirect()->to('/branches')->with('success', "Branch '{$branch['name']}' has been removed.");
    }
}
