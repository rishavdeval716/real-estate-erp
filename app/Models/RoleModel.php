<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table            = 'roles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'description',
        'status',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'name'   => 'required|min_length[2]|max_length[100]|is_unique[roles.name,id,{id}]',
        'status' => 'required|in_list[active,inactive]',
    ];

    /**
     * Get array of permission IDs assigned to a role
     */
    public function getPermissionIds(int $roleId): array
    {
        $db = \Config\Database::connect();
        $rows = $db->table('role_permissions')
                   ->select('permission_id')
                   ->where('role_id', $roleId)
                   ->get()
                   ->getResultArray();

        return array_column($rows, 'permission_id');
    }

    /**
     * Get full permission records assigned to a role
     */
    public function getPermissions(int $roleId): array
    {
        $db = \Config\Database::connect();
        return $db->table('role_permissions')
                  ->select('permissions.*')
                  ->join('permissions', 'permissions.id = role_permissions.permission_id')
                  ->where('role_permissions.role_id', $roleId)
                  ->get()
                  ->getResultArray();
    }

    /**
     * Synchronize permissions for a role
     */
    public function syncPermissions(int $roleId, array $permissionIds): bool
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $db->table('role_permissions')->where('role_id', $roleId)->delete();

        if (!empty($permissionIds)) {
            $insertData = [];
            foreach ($permissionIds as $pid) {
                $pid = (int) $pid;
                if ($pid > 0) {
                    $insertData[] = [
                        'role_id'       => $roleId,
                        'permission_id' => $pid,
                    ];
                }
            }
            if (!empty($insertData)) {
                $db->table('role_permissions')->insertBatch($insertData);
            }
        }

        $db->transComplete();
        return $db->transStatus();
    }
}
