<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'branch_id',
        'name',
        'email',
        'phone',
        'password',
        'status',
        'last_login_at',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'name'      => 'required|min_length[2]|max_length[150]',
        'email'     => 'required|valid_email|max_length[150]|is_unique[users.email,id,{id}]',
        'phone'     => 'permit_empty|max_length[30]',
        'branch_id' => 'permit_empty|is_not_unique[branches.id]',
        'status'    => 'required|in_list[active,inactive,suspended]',
    ];

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password']) && !empty($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_BCRYPT);
        } else {
            // Remove empty password during update to avoid clearing existing password
            unset($data['data']['password']);
        }
        return $data;
    }

    /**
     * Get paginated users with branch and roles
     */
    public function getFilteredUsers(?string $search = null, ?string $status = null, ?int $roleId = null, int $perPage = 10)
    {
        $builder = $this->select('users.*, branches.name as branch_name')
                        ->join('branches', 'branches.id = users.branch_id', 'left');

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('users.name', $search)
                    ->orLike('users.email', $search)
                    ->orLike('users.phone', $search)
                    ->orLike('branches.name', $search)
                    ->groupEnd();
        }

        if (!empty($status) && in_array($status, ['active', 'inactive', 'suspended'])) {
            $builder->where('users.status', $status);
        }

        if (!empty($roleId) && $roleId > 0) {
            $builder->join('user_roles', 'user_roles.user_id = users.id')
                    ->where('user_roles.role_id', $roleId);
        }

        $users = $builder->orderBy('users.id', 'DESC')->paginate($perPage);

        // Fetch roles for each returned user
        if (!empty($users)) {
            $db = \Config\Database::connect();
            $userIds = array_column($users, 'id');
            $roleRows = $db->table('user_roles')
                           ->select('user_roles.user_id, roles.id as role_id, roles.name as role_name')
                           ->join('roles', 'roles.id = user_roles.role_id')
                           ->whereIn('user_roles.user_id', $userIds)
                           ->get()
                           ->getResultArray();

            $userRolesMap = [];
            foreach ($roleRows as $row) {
                $userRolesMap[$row['user_id']][] = [
                    'id'   => $row['role_id'],
                    'name' => $row['role_name'],
                ];
            }

            foreach ($users as &$user) {
                $user['roles'] = $userRolesMap[$user['id']] ?? [];
            }
        }

        return $users;
    }

    /**
     * Get single user with assigned roles
     */
    public function getUserWithRoles(int $id): ?array
    {
        $user = $this->select('users.*, branches.name as branch_name')
                     ->join('branches', 'branches.id = users.branch_id', 'left')
                     ->find($id);

        if (!$user) {
            return null;
        }

        $db = \Config\Database::connect();
        $roles = $db->table('user_roles')
                    ->select('roles.*')
                    ->join('roles', 'roles.id = user_roles.role_id')
                    ->where('user_roles.user_id', $id)
                    ->get()
                    ->getResultArray();

        $user['roles'] = $roles;
        $user['role_ids'] = array_column($roles, 'id');
        $user['role_names'] = array_column($roles, 'name');

        return $user;
    }

    /**
     * Sync user roles
     */
    public function syncRoles(int $userId, array $roleIds): bool
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $db->table('user_roles')->where('user_id', $userId)->delete();

        if (!empty($roleIds)) {
            $insertData = [];
            foreach ($roleIds as $rid) {
                $rid = (int) $rid;
                if ($rid > 0) {
                    $insertData[] = [
                        'user_id' => $userId,
                        'role_id' => $rid,
                    ];
                }
            }
            if (!empty($insertData)) {
                $db->table('user_roles')->insertBatch($insertData);
            }
        }

        $db->transComplete();
        return $db->transStatus();
    }

    /**
     * Get all permission slugs for a user
     */
    public function getUserPermissions(int $userId): array
    {
        $db = \Config\Database::connect();

        // Check if user is Super Admin
        $isSuperAdmin = $db->table('user_roles')
                           ->join('roles', 'roles.id = user_roles.role_id')
                           ->where('user_roles.user_id', $userId)
                           ->where('roles.name', 'Super Admin')
                           ->countAllResults() > 0;

        if ($isSuperAdmin) {
            // Super Admin has all permission slugs
            $allPerms = $db->table('permissions')->select('slug')->get()->getResultArray();
            return array_column($allPerms, 'slug');
        }

        // Get permissions linked through roles
        $rows = $db->table('user_roles')
                   ->select('permissions.slug')
                   ->join('role_permissions', 'role_permissions.role_id = user_roles.role_id')
                   ->join('permissions', 'permissions.id = role_permissions.permission_id')
                   ->where('user_roles.user_id', $userId)
                   ->get()
                   ->getResultArray();

        return array_values(array_unique(array_column($rows, 'slug')));
    }

    /**
     * Check if user has specific permission
     */
    public function hasPermission(int $userId, string $slug): bool
    {
        $perms = $this->getUserPermissions($userId);
        return in_array($slug, $perms, true);
    }
}
