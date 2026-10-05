<?php

namespace App\Models;

use CodeIgniter\Model;

class PermissionModel extends Model
{
    protected $table            = 'permissions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'slug',
        'group_name',
        'description',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'name'       => 'required|min_length[3]|max_length[100]',
        'slug'       => 'required|min_length[3]|max_length[100]|is_unique[permissions.slug,id,{id}]',
        'group_name' => 'required|min_length[2]|max_length[100]',
    ];

    /**
     * Get all permissions grouped by their category/group_name
     */
    public function getGroupedPermissions(): array
    {
        $permissions = $this->orderBy('group_name', 'ASC')->orderBy('name', 'ASC')->findAll();
        $grouped = [];

        foreach ($permissions as $perm) {
            $group = $perm['group_name'] ?: 'General';
            if (!isset($grouped[$group])) {
                $grouped[$group] = [];
            }
            $grouped[$group][] = $perm;
        }

        return $grouped;
    }
}
