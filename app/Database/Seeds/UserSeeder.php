<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        $hqBranch = $db->table('branches')->where('code', 'BR-HQ-001')->get()->getRowArray();
        $branchId = $hqBranch ? $hqBranch['id'] : null;

        $superAdminRole = $db->table('roles')->where('name', 'Super Admin')->get()->getRowArray();
        $adminRole      = $db->table('roles')->where('name', 'Admin')->get()->getRowArray();
        $managerRole    = $db->table('roles')->where('name', 'Manager')->get()->getRowArray();

        $defaultPasswordHash = password_hash('Password@123', PASSWORD_BCRYPT);

        $users = [
            [
                'name'       => 'Super Administrator',
                'email'      => 'admin@realestate-erp.local',
                'phone'      => '+91 99000 11222',
                'password'   => $defaultPasswordHash,
                'status'     => 'active',
                'branch_id'  => $branchId,
                'role_id'    => $superAdminRole ? $superAdminRole['id'] : null,
            ],
            [
                'name'       => 'Operations Admin',
                'email'      => 'admin.ops@realestate-erp.local',
                'phone'      => '+91 99000 33444',
                'password'   => $defaultPasswordHash,
                'status'     => 'active',
                'branch_id'  => $branchId,
                'role_id'    => $adminRole ? $adminRole['id'] : null,
            ],
            [
                'name'       => 'Branch Manager',
                'email'      => 'manager@realestate-erp.local',
                'phone'      => '+91 99000 55666',
                'password'   => $defaultPasswordHash,
                'status'     => 'active',
                'branch_id'  => $branchId,
                'role_id'    => $managerRole ? $managerRole['id'] : null,
            ],
        ];

        foreach ($users as $u) {
            $roleId = $u['role_id'];
            unset($u['role_id']);

            $existing = $db->table('users')->where('email', $u['email'])->get()->getRowArray();
            if (!$existing) {
                $u['created_at'] = date('Y-m-d H:i:s');
                $u['updated_at'] = date('Y-m-d H:i:s');
                $db->table('users')->insert($u);
                $userId = $db->insertID();

                if ($roleId && $userId) {
                    $db->table('user_roles')->insert([
                        'user_id' => $userId,
                        'role_id' => $roleId,
                    ]);
                }
            } else {
                $userId = $existing['id'];
                if ($roleId && $userId) {
                    $hasRole = $db->table('user_roles')->where([
                        'user_id' => $userId,
                        'role_id' => $roleId,
                    ])->countAllResults();

                    if ($hasRole === 0) {
                        $db->table('user_roles')->insert([
                            'user_id' => $userId,
                            'role_id' => $roleId,
                        ]);
                    }
                }
            }
        }
    }
}
