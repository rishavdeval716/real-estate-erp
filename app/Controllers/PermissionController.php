<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PermissionModel;
use App\Models\AuditLogModel;

class PermissionController extends BaseController
{
    protected $permissionModel;

    public function __construct()
    {
        $this->permissionModel = new PermissionModel();
    }

    /**
     * List permissions with search, group filter, and pagination
     */
    public function index()
    {
        $search = trim($this->request->getGet('search') ?? '');
        $group  = trim($this->request->getGet('group') ?? '');

        $builder = $this->permissionModel;

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('name', $search)
                    ->orLike('slug', $search)
                    ->orLike('description', $search)
                    ->groupEnd();
        }

        if (!empty($group)) {
            $builder->where('group_name', $group);
        }

        $permissions = $builder->orderBy('group_name', 'ASC')->orderBy('name', 'ASC')->paginate(15);
        $pager       = $this->permissionModel->pager;

        // Distinct groups for filter dropdown
        $db = \Config\Database::connect();
        $groupRows = $db->table('permissions')->select('DISTINCT(group_name) as gname')->orderBy('group_name', 'ASC')->get()->getResultArray();
        $groups = array_column($groupRows, 'gname');

        $data = [
            'title'       => 'Permission Management - Real Estate ERP',
            'permissions' => $permissions,
            'pager'       => $pager,
            'groups'      => $groups,
            'search'      => $search,
            'selectedGroup' => $group,
        ];

        return view('permissions/index', $data);
    }

    /**
     * Show create permission form
     */
    public function create()
    {
        $defaultGroups = ['Dashboard', 'Users', 'Roles', 'Permissions', 'Company', 'Branches', 'Audit Logs'];

        $data = [
            'title'         => 'Create New Permission - Real Estate ERP',
            'defaultGroups' => $defaultGroups,
        ];

        return view('permissions/create', $data);
    }

    /**
     * Store new permission
     */
    public function store()
    {
        $rules = [
            'name'        => 'required|min_length[3]|max_length[100]',
            'slug'        => 'required|min_length[3]|max_length[100]|is_unique[permissions.slug]',
            'group_name'  => 'required|min_length[2]|max_length[100]',
            'description' => 'permit_empty|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $permData = [
            'name'        => trim($this->request->getPost('name')),
            'slug'        => strtolower(trim($this->request->getPost('slug'))),
            'group_name'  => trim($this->request->getPost('group_name')),
            'description' => trim($this->request->getPost('description')),
        ];

        $permId = $this->permissionModel->insert($permData);

        if (!$permId) {
            return redirect()->back()->withInput()->with('error', 'Failed to create permission.');
        }

        AuditLogModel::record(
            'PERMISSION_CREATED',
            'Permissions',
            (int) $permId,
            "Created permission '{$permData['name']}' ({$permData['slug']}) under group {$permData['group_name']}"
        );

        return redirect()->to('/permissions')->with('success', "Permission '{$permData['name']}' created successfully.");
    }

    /**
     * Show edit permission form
     */
    public function edit($id)
    {
        $id = (int) $id;
        $permission = $this->permissionModel->find($id);

        if (!$permission) {
            return redirect()->to('/permissions')->with('error', 'Permission not found.');
        }

        $defaultGroups = ['Dashboard', 'Users', 'Roles', 'Permissions', 'Company', 'Branches', 'Audit Logs'];

        $data = [
            'title'         => "Edit Permission: {$permission['name']} - Real Estate ERP",
            'permission'    => $permission,
            'defaultGroups' => $defaultGroups,
        ];

        return view('permissions/edit', $data);
    }

    /**
     * Update permission
     */
    public function update($id)
    {
        $id = (int) $id;
        $permission = $this->permissionModel->find($id);

        if (!$permission) {
            return redirect()->to('/permissions')->with('error', 'Permission not found.');
        }

        $rules = [
            'name'        => 'required|min_length[3]|max_length[100]',
            'slug'        => "required|min_length[3]|max_length[100]|is_unique[permissions.slug,id,{$id}]",
            'group_name'  => 'required|min_length[2]|max_length[100]',
            'description' => 'permit_empty|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $permData = [
            'name'        => trim($this->request->getPost('name')),
            'slug'        => strtolower(trim($this->request->getPost('slug'))),
            'group_name'  => trim($this->request->getPost('group_name')),
            'description' => trim($this->request->getPost('description')),
        ];

        $this->permissionModel->update($id, $permData);

        AuditLogModel::record(
            'PERMISSION_UPDATED',
            'Permissions',
            $id,
            "Updated permission {$permData['name']} ({$permData['slug']})"
        );

        return redirect()->to('/permissions')->with('success', "Permission '{$permData['name']}' updated successfully.");
    }

    /**
     * Delete permission
     */
    public function delete($id)
    {
        $id = (int) $id;
        $permission = $this->permissionModel->find($id);

        if (!$permission) {
            return redirect()->to('/permissions')->with('error', 'Permission not found.');
        }

        $this->permissionModel->delete($id);

        AuditLogModel::record(
            'PERMISSION_DELETED',
            'Permissions',
            $id,
            "Deleted permission {$permission['name']} ({$permission['slug']})"
        );

        return redirect()->to('/permissions')->with('success', "Permission '{$permission['name']}' was removed.");
    }
}
