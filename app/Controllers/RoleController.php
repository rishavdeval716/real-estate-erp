<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RoleModel;
use App\Models\PermissionModel;
use App\Models\AuditLogModel;

class RoleController extends BaseController
{
    protected $roleModel;
    protected $permissionModel;

    public function __construct()
    {
        $this->roleModel       = new RoleModel();
        $this->permissionModel = new PermissionModel();
    }

    /**
     * List all roles with user counts and permission counts
     */
    public function index()
    {
        $db = \Config\Database::connect();
        $roles = $this->roleModel->orderBy('id', 'ASC')->findAll();

        foreach ($roles as &$role) {
            $role['user_count'] = $db->table('user_roles')->where('role_id', $role['id'])->countAllResults();
            $role['permission_count'] = $db->table('role_permissions')->where('role_id', $role['id'])->countAllResults();
        }

        $data = [
            'title' => 'Role Management - Real Estate ERP',
            'roles' => $roles,
        ];

        return view('roles/index', $data);
    }

    /**
     * Create role form with grouped permission matrix
     */
    public function create()
    {
        $groupedPermissions = $this->permissionModel->getGroupedPermissions();

        $data = [
            'title'              => 'Create New Role - Real Estate ERP',
            'groupedPermissions' => $groupedPermissions,
        ];

        return view('roles/create', $data);
    }

    /**
     * Store new role and assign permissions
     */
    public function store()
    {
        $rules = [
            'name'        => 'required|min_length[2]|max_length[100]|is_unique[roles.name]',
            'description' => 'permit_empty|max_length[255]',
            'status'      => 'required|in_list[active,inactive]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $roleData = [
            'name'        => trim($this->request->getPost('name')),
            'description' => trim($this->request->getPost('description')),
            'status'      => $this->request->getPost('status'),
        ];

        $roleId = $this->roleModel->insert($roleData);

        if (!$roleId) {
            return redirect()->back()->withInput()->with('error', 'Failed to create role. Please try again.');
        }

        // Sync permissions
        $permissions = $this->request->getPost('permissions') ?? [];
        $this->roleModel->syncPermissions((int) $roleId, (array) $permissions);

        AuditLogModel::record(
            'ROLE_CREATED',
            'Roles',
            (int) $roleId,
            "Created role '{$roleData['name']}' with " . count($permissions) . " permissions."
        );

        return redirect()->to('/roles')->with('success', "Role '{$roleData['name']}' was successfully created.");
    }

    /**
     * View role details, assigned permissions, and member users
     */
    public function show($id)
    {
        $id = (int) $id;
        $role = $this->roleModel->find($id);

        if (!$role) {
            return redirect()->to('/roles')->with('error', 'Role not found.');
        }

        $db = \Config\Database::connect();
        $permissions = $this->roleModel->getPermissions($id);
        $users = $db->table('user_roles')
                    ->select('users.*, branches.name as branch_name')
                    ->join('users', 'users.id = user_roles.user_id')
                    ->join('branches', 'branches.id = users.branch_id', 'left')
                    ->where('user_roles.role_id', $id)
                    ->get()
                    ->getResultArray();

        $data = [
            'title'       => "Role Details: {$role['name']} - Real Estate ERP",
            'role'        => $role,
            'permissions' => $permissions,
            'users'       => $users,
        ];

        return view('roles/view', $data);
    }

    /**
     * Edit role form with current permissions checked
     */
    public function edit($id)
    {
        $id = (int) $id;
        $role = $this->roleModel->find($id);

        if (!$role) {
            return redirect()->to('/roles')->with('error', 'Role not found.');
        }

        $groupedPermissions = $this->permissionModel->getGroupedPermissions();
        $assignedPermIds    = $this->roleModel->getPermissionIds($id);

        $data = [
            'title'              => "Edit Role: {$role['name']} - Real Estate ERP",
            'role'               => $role,
            'groupedPermissions' => $groupedPermissions,
            'assignedPermIds'    => $assignedPermIds,
        ];

        return view('roles/edit', $data);
    }

    /**
     * Update role details and permission mappings
     */
    public function update($id)
    {
        $id = (int) $id;
        $role = $this->roleModel->find($id);

        if (!$role) {
            return redirect()->to('/roles')->with('error', 'Role not found.');
        }

        $rules = [
            'name'        => "required|min_length[2]|max_length[100]|is_unique[roles.name,id,{$id}]",
            'description' => 'permit_empty|max_length[255]',
            'status'      => 'required|in_list[active,inactive]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $roleData = [
            'name'        => trim($this->request->getPost('name')),
            'description' => trim($this->request->getPost('description')),
            'status'      => $this->request->getPost('status'),
        ];

        $this->roleModel->update($id, $roleData);

        // Sync permissions
        $permissions = $this->request->getPost('permissions') ?? [];
        $this->roleModel->syncPermissions($id, (array) $permissions);

        AuditLogModel::record(
            'ROLE_UPDATED',
            'Roles',
            $id,
            "Updated role '{$roleData['name']}' permissions set (" . count($permissions) . " assigned)."
        );

        return redirect()->to('/roles')->with('success', "Role '{$roleData['name']}' updated successfully.");
    }

    /**
     * Delete role (Super Admin protected)
     */
    public function delete($id)
    {
        $id = (int) $id;
        $role = $this->roleModel->find($id);

        if (!$role) {
            return redirect()->to('/roles')->with('error', 'Role not found.');
        }

        if ($role['name'] === 'Super Admin') {
            return redirect()->to('/roles')->with('error', 'The Super Admin role is protected and cannot be deleted.');
        }

        $db = \Config\Database::connect();
        $userCount = $db->table('user_roles')->where('role_id', $id)->countAllResults();
        if ($userCount > 0) {
            return redirect()->to('/roles')->with('error', "Cannot delete role '{$role['name']}' because it is assigned to {$userCount} user(s). Reassign them first.");
        }

        $this->roleModel->delete($id);

        AuditLogModel::record(
            'ROLE_DELETED',
            'Roles',
            $id,
            "Deleted role '{$role['name']}'"
        );

        return redirect()->to('/roles')->with('success', "Role '{$role['name']}' was successfully deleted.");
    }
}
