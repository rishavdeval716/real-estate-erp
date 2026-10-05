<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Roles based strictly on specification
        $roles = [
            ['name' => 'Super Admin',       'description' => 'Full unrestricted system access and configuration', 'status' => 'active'],
            ['name' => 'Admin',             'description' => 'Administrative control over users, branches and operations', 'status' => 'active'],
            ['name' => 'Manager',           'description' => 'Branch and operations manager', 'status' => 'active'],
            ['name' => 'Sales Executive',   'description' => 'Handles property inquiries, leads, and sales activities', 'status' => 'active'],
            ['name' => 'Property Manager',  'description' => 'Oversees property listings, inspections, and tenants', 'status' => 'active'],
            ['name' => 'Accountant',        'description' => 'Manages invoices, receipts, and financial records', 'status' => 'active'],
            ['name' => 'Agent/Broker',      'description' => 'External channel partner and broker agent', 'status' => 'active'],
            ['name' => 'Customer',          'description' => 'Registered property buyer and investor client', 'status' => 'active'],
            ['name' => 'Tenant',            'description' => 'Resident tenant leasing managed property', 'status' => 'active'],
        ];

        foreach ($roles as $role) {
            $existing = $db->table('roles')->where('name', $role['name'])->get()->getRowArray();
            if (!$existing) {
                $role['created_at'] = date('Y-m-d H:i:s');
                $role['updated_at'] = date('Y-m-d H:i:s');
                $db->table('roles')->insert($role);
            }
        }

        // 2. Foundation Permissions
        $permissions = [
            // Dashboard
            ['name' => 'View Dashboard',          'slug' => 'dashboard.view',       'group_name' => 'Dashboard',    'description' => 'Access system dashboard and executive metrics'],

            // Users
            ['name' => 'View Users',              'slug' => 'users.view',           'group_name' => 'Users',        'description' => 'View user lists and profile details'],
            ['name' => 'Create User',             'slug' => 'users.create',         'group_name' => 'Users',        'description' => 'Add new user accounts'],
            ['name' => 'Edit User',               'slug' => 'users.edit',           'group_name' => 'Users',        'description' => 'Update user details and status'],
            ['name' => 'Delete User',             'slug' => 'users.delete',         'group_name' => 'Users',        'description' => 'Delete/deactivate users'],

            // Roles
            ['name' => 'View Roles',              'slug' => 'roles.view',           'group_name' => 'Roles',        'description' => 'View system roles'],
            ['name' => 'Create Role',             'slug' => 'roles.create',         'group_name' => 'Roles',        'description' => 'Add new security roles'],
            ['name' => 'Edit Role',               'slug' => 'roles.edit',           'group_name' => 'Roles',        'description' => 'Edit roles and assign permissions'],
            ['name' => 'Delete Role',             'slug' => 'roles.delete',         'group_name' => 'Roles',        'description' => 'Delete system roles'],

            // Permissions
            ['name' => 'View Permissions',        'slug' => 'permissions.view',     'group_name' => 'Permissions',  'description' => 'View system permissions list'],
            ['name' => 'Create Permission',       'slug' => 'permissions.create',   'group_name' => 'Permissions',  'description' => 'Add newly defined permission capabilities'],
            ['name' => 'Edit Permission',         'slug' => 'permissions.edit',     'group_name' => 'Permissions',  'description' => 'Edit existing permission details'],
            ['name' => 'Delete Permission',       'slug' => 'permissions.delete',   'group_name' => 'Permissions',  'description' => 'Delete system permissions'],

            // Company
            ['name' => 'View Company',            'slug' => 'company.view',         'group_name' => 'Company',      'description' => 'View company corporate information'],
            ['name' => 'Create Company',          'slug' => 'company.create',       'group_name' => 'Company',      'description' => 'Create new company organization profiles'],
            ['name' => 'Edit Company',            'slug' => 'company.edit',         'group_name' => 'Company',      'description' => 'Update company corporate information'],
            ['name' => 'Delete Company',          'slug' => 'company.delete',       'group_name' => 'Company',      'description' => 'Remove company profile'],

            // Branches
            ['name' => 'View Branches',           'slug' => 'branches.view',        'group_name' => 'Branches',     'description' => 'View company branches'],
            ['name' => 'Create Branch',           'slug' => 'branches.create',      'group_name' => 'Branches',     'description' => 'Create new branch office'],
            ['name' => 'Edit Branch',             'slug' => 'branches.edit',        'group_name' => 'Branches',     'description' => 'Update branch office details'],
            ['name' => 'Delete Branch',           'slug' => 'branches.delete',      'group_name' => 'Branches',     'description' => 'Delete or close a branch office'],

            // Audit Logs
            ['name' => 'View Audit Logs',         'slug' => 'audit_logs.view',      'group_name' => 'Audit Logs',   'description' => 'Review comprehensive security and audit logs'],
        ];

        foreach ($permissions as $perm) {
            $existing = $db->table('permissions')->where('slug', $perm['slug'])->get()->getRowArray();
            if (!$existing) {
                $perm['created_at'] = date('Y-m-d H:i:s');
                $perm['updated_at'] = date('Y-m-d H:i:s');
                $db->table('permissions')->insert($perm);
            }
        }

        // 3. Assign permissions to Admin & Manager
        $allPermRows = $db->table('permissions')->get()->getResultArray();
        $adminRole   = $db->table('roles')->where('name', 'Admin')->get()->getRowArray();
        $managerRole = $db->table('roles')->where('name', 'Manager')->get()->getRowArray();

        if ($adminRole) {
            foreach ($allPermRows as $p) {
                $exists = $db->table('role_permissions')->where([
                    'role_id'       => $adminRole['id'],
                    'permission_id' => $p['id'],
                ])->countAllResults();

                if ($exists === 0) {
                    $db->table('role_permissions')->insert([
                        'role_id'       => $adminRole['id'],
                        'permission_id' => $p['id'],
                    ]);
                }
            }
        }

        if ($managerRole) {
            $managerPermSlugs = [
                'dashboard.view',
                'users.view',
                'company.view',
                'branches.view',
                'branches.create',
                'branches.edit',
            ];
            foreach ($allPermRows as $p) {
                if (in_array($p['slug'], $managerPermSlugs, true)) {
                    $exists = $db->table('role_permissions')->where([
                        'role_id'       => $managerRole['id'],
                        'permission_id' => $p['id'],
                    ])->countAllResults();

                    if ($exists === 0) {
                        $db->table('role_permissions')->insert([
                            'role_id'       => $managerRole['id'],
                            'permission_id' => $p['id'],
                        ]);
                    }
                }
            }
        }
    }
}
