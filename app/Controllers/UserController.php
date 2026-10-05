<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\BranchModel;
use App\Models\AuditLogModel;

class UserController extends BaseController
{
    protected $userModel;
    protected $roleModel;
    protected $branchModel;

    public function __construct()
    {
        $this->userModel   = new UserModel();
        $this->roleModel   = new RoleModel();
        $this->branchModel = new BranchModel();
    }

    /**
     * User listing with search, filters, pagination
     */
    public function index()
    {
        $search = trim($this->request->getGet('search') ?? '');
        $status = trim($this->request->getGet('status') ?? '');
        $roleId = (int) ($this->request->getGet('role_id') ?? 0);

        $perPage = 10;
        $users   = $this->userModel->getFilteredUsers($search, $status, $roleId, $perPage);
        $pager   = $this->userModel->pager;
        $roles   = $this->roleModel->where('status', 'active')->findAll();

        $data = [
            'title'   => 'User Management - Real Estate ERP',
            'users'   => $users,
            'pager'   => $pager,
            'roles'   => $roles,
            'search'  => $search,
            'status'  => $status,
            'roleId'  => $roleId,
        ];

        return view('users/index', $data);
    }

    /**
     * Show create user form
     */
    public function create()
    {
        $roles    = $this->roleModel->where('status', 'active')->findAll();
        $branches = $this->branchModel->where('status', 'active')->findAll();

        $data = [
            'title'    => 'Create New User - Real Estate ERP',
            'roles'    => $roles,
            'branches' => $branches,
        ];

        return view('users/create', $data);
    }

    /**
     * Store new user
     */
    public function store()
    {
        $rules = [
            'name'      => 'required|min_length[2]|max_length[150]',
            'email'     => 'required|valid_email|is_unique[users.email]|max_length[150]',
            'phone'     => 'permit_empty|max_length[30]',
            'password'  => 'required|min_length[6]',
            'branch_id' => 'permit_empty|is_not_unique[branches.id]',
            'status'    => 'required|in_list[active,inactive,suspended]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $branchId = $this->request->getPost('branch_id');
        $userData = [
            'name'      => trim($this->request->getPost('name')),
            'email'     => strtolower(trim($this->request->getPost('email'))),
            'phone'     => trim($this->request->getPost('phone')),
            'password'  => $this->request->getPost('password'),
            'branch_id' => !empty($branchId) ? (int) $branchId : null,
            'status'    => $this->request->getPost('status'),
        ];

        $userId = $this->userModel->insert($userData);

        if (!$userId) {
            return redirect()->back()->withInput()->with('error', 'Failed to create user. Please try again.');
        }

        // Sync selected roles
        $selectedRoles = $this->request->getPost('roles') ?? [];
        $this->userModel->syncRoles((int) $userId, (array) $selectedRoles);

        // Audit Log
        AuditLogModel::record(
            'USER_CREATED',
            'Users',
            (int) $userId,
            "Created user {$userData['name']} ({$userData['email']}) with status {$userData['status']}"
        );

        return redirect()->to('/users')->with('success', "User '{$userData['name']}' was successfully created.");
    }

    /**
     * View user details
     */
    public function show($id)
    {
        $user = $this->userModel->getUserWithRoles((int) $id);

        if (!$user) {
            return redirect()->to('/users')->with('error', 'User not found.');
        }

        // Get recent audit activities for this user
        $auditModel = new AuditLogModel();
        $activities = $auditModel->where('user_id', (int) $id)->orderBy('id', 'DESC')->limit(15)->find();

        $data = [
            'title'      => "User Profile: {$user['name']} - Real Estate ERP",
            'user'       => $user,
            'activities' => $activities,
        ];

        return view('users/view', $data);
    }

    /**
     * Show edit user form
     */
    public function edit($id)
    {
        $user = $this->userModel->getUserWithRoles((int) $id);

        if (!$user) {
            return redirect()->to('/users')->with('error', 'User not found.');
        }

        $roles    = $this->roleModel->where('status', 'active')->findAll();
        $branches = $this->branchModel->where('status', 'active')->findAll();

        $data = [
            'title'    => "Edit User: {$user['name']} - Real Estate ERP",
            'user'     => $user,
            'roles'    => $roles,
            'branches' => $branches,
        ];

        return view('users/edit', $data);
    }

    /**
     * Update user details
     */
    public function update($id)
    {
        $id = (int) $id;
        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('/users')->with('error', 'User not found.');
        }

        $rules = [
            'name'      => 'required|min_length[2]|max_length[150]',
            'email'     => "required|valid_email|is_unique[users.email,id,{$id}]|max_length[150]",
            'phone'     => 'permit_empty|max_length[30]',
            'branch_id' => 'permit_empty|is_not_unique[branches.id]',
            'status'    => 'required|in_list[active,inactive,suspended]',
            'password'  => 'permit_empty|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $branchId = $this->request->getPost('branch_id');
        $userData = [
            'name'      => trim($this->request->getPost('name')),
            'email'     => strtolower(trim($this->request->getPost('email'))),
            'phone'     => trim($this->request->getPost('phone')),
            'branch_id' => !empty($branchId) ? (int) $branchId : null,
            'status'    => $this->request->getPost('status'),
        ];

        $newPassword = $this->request->getPost('password');
        if (!empty($newPassword)) {
            $userData['password'] = $newPassword; // Model's beforeUpdate hook hashes it
        }

        $this->userModel->update($id, $userData);

        // Sync roles
        $selectedRoles = $this->request->getPost('roles') ?? [];
        $this->userModel->syncRoles($id, (array) $selectedRoles);

        // Audit Log
        AuditLogModel::record(
            'USER_UPDATED',
            'Users',
            $id,
            "Updated user {$userData['name']} ({$userData['email']})"
        );

        return redirect()->to('/users')->with('success', "User '{$userData['name']}' updated successfully.");
    }

    /**
     * Soft delete user
     */
    public function delete($id)
    {
        $id = (int) $id;
        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('/users')->with('error', 'User not found.');
        }

        // Prevent self-deletion
        if ($id === (int) session()->get('user_id')) {
            return redirect()->to('/users')->with('error', 'You cannot delete your own currently logged-in account.');
        }

        $this->userModel->delete($id);

        AuditLogModel::record(
            'USER_DELETED',
            'Users',
            $id,
            "Soft deleted user {$user['name']} ({$user['email']})"
        );

        return redirect()->to('/users')->with('success', "User '{$user['name']}' has been deleted.");
    }
}
