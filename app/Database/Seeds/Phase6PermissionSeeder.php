<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Phase6PermissionSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        $phase6Permissions = [
            // Construction Milestones
            ['name' => 'View Construction Milestones',    'slug' => 'construction.milestones.view',   'group_name' => 'Construction', 'description' => 'View project construction stages and progress milestones'],
            ['name' => 'Manage Construction Milestones',  'slug' => 'construction.milestones.manage', 'group_name' => 'Construction', 'description' => 'Update progress percentage, status and milestone verification'],

            // Daily Site Logs
            ['name' => 'View Daily Site Logs',            'slug' => 'construction.daily_logs.view',   'group_name' => 'Construction', 'description' => 'View site supervisor daily logs, manpower and weather'],
            ['name' => 'Create Daily Site Log',           'slug' => 'construction.daily_logs.create', 'group_name' => 'Construction', 'description' => 'Submit daily construction log and materials report'],
            ['name' => 'Approve Daily Site Log',          'slug' => 'construction.daily_logs.approve','group_name' => 'Construction', 'description' => 'Approve or verify daily site execution reports'],

            // Contractors & Vendors
            ['name' => 'View Contractors',                'slug' => 'contractors.view',               'group_name' => 'Contractors',  'description' => 'View contractor directory, trade specialization and ratings'],
            ['name' => 'Create Contractor',               'slug' => 'contractors.create',             'group_name' => 'Contractors',  'description' => 'Onboard new civil, MEP and finishing contractors'],
            ['name' => 'Edit Contractor',                 'slug' => 'contractors.edit',               'group_name' => 'Contractors',  'description' => 'Update contractor license, contact and GSTIN details'],
            ['name' => 'Delete Contractor',               'slug' => 'contractors.delete',             'group_name' => 'Contractors',  'description' => 'Deactivate or blacklist contractor profiles'],

            // Construction Work Orders
            ['name' => 'View Construction Work Orders',   'slug' => 'contractors.work_orders.view',  'group_name' => 'Contractors',  'description' => 'View awarded construction contracts and scopes of work'],
            ['name' => 'Create Construction Work Order',  'slug' => 'contractors.work_orders.create','group_name' => 'Contractors',  'description' => 'Award work orders, contract values and retention terms'],
            ['name' => 'Edit Construction Work Order',    'slug' => 'contractors.work_orders.edit',  'group_name' => 'Contractors',  'description' => 'Update work order execution status and completion dates'],

            // Material Procurement & Requisitions
            ['name' => 'View Material Requisitions',      'slug' => 'procurement.view',               'group_name' => 'Procurement',  'description' => 'View material indent requests and procurement register'],
            ['name' => 'Create Material Requisition',     'slug' => 'procurement.create',             'group_name' => 'Procurement',  'description' => 'Submit material indent for cement, steel, masonry and supplies'],
            ['name' => 'Approve Material Requisition',    'slug' => 'procurement.approve',            'group_name' => 'Procurement',  'description' => 'Approve or authorize procurement of construction materials'],

            // Site Quality & Safety Inspections
            ['name' => 'View Site Inspections',           'slug' => 'inspections.view',               'group_name' => 'Inspections',  'description' => 'View structural, MEP and pre-possession snagging inspection logs'],
            ['name' => 'Schedule Site Inspection',        'slug' => 'inspections.create',             'group_name' => 'Inspections',  'description' => 'Schedule quality and safety inspection checkpoints'],
            ['name' => 'Conduct Site Inspection',         'slug' => 'inspections.conduct',            'group_name' => 'Inspections',  'description' => 'Record inspection pass/fail result, snags and rectification deadlines'],

            // Unit Handover & Key Certificates
            ['name' => 'View Handover Records',           'slug' => 'handover.view',                  'group_name' => 'Handover',     'description' => 'View possession handover register and clearance checklists'],
            ['name' => 'Create Handover Record',          'slug' => 'handover.create',                'group_name' => 'Handover',     'description' => 'Execute possession handover and meter readings for buyers'],
            ['name' => 'Print Handover Certificate',      'slug' => 'handover.certificate',           'group_name' => 'Handover',     'description' => 'Generate and print official Possession & Key Handover Certificate'],

            // Construction Dashboard
            ['name' => 'View Construction Dashboard',     'slug' => 'construction.dashboard.view',    'group_name' => 'Construction', 'description' => 'View site progress KPIs, manpower metrics and handover counts'],
        ];

        $now = date('Y-m-d H:i:s');
        foreach ($phase6Permissions as $p) {
            $existing = $db->table('permissions')->where('slug', $p['slug'])->get()->getRow();
            if (!$existing) {
                $db->table('permissions')->insert([
                    'name'        => $p['name'],
                    'slug'        => $p['slug'],
                    'group_name'  => $p['group_name'],
                    'description' => $p['description'],
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }
        }

        // Assign to Roles:
        // 1. Super Admin: all permissions (inherent)
        // 2. Admin (id 2): all Phase 6
        // 3. Manager (id 3): view and operational permissions
        // 4. Property Manager (id 5): construction, inspections, handover
        $adminRole = $db->table('roles')->where('id', 2)->get()->getRow();
        $managerRole = $db->table('roles')->where('id', 3)->get()->getRow();
        $propManagerRole = $db->table('roles')->where('id', 5)->get()->getRow();

        $allP6Perms = $db->table('permissions')->like('slug', 'construction.', 'after')
            ->orLike('slug', 'contractors.', 'after')
            ->orLike('slug', 'procurement.', 'after')
            ->orLike('slug', 'inspections.', 'after')
            ->orLike('slug', 'handover.', 'after')
            ->get()->getResultArray();

        foreach ($allP6Perms as $perm) {
            // Admin gets all
            if ($adminRole) {
                $exists = $db->table('role_permissions')->where(['role_id' => 2, 'permission_id' => $perm['id']])->countAllResults();
                if (!$exists) {
                    $db->table('role_permissions')->insert(['role_id' => 2, 'permission_id' => $perm['id']]);
                }
            }
            // Manager gets view and operational
            if ($managerRole && !in_array($perm['slug'], ['contractors.delete'])) {
                $exists = $db->table('role_permissions')->where(['role_id' => 3, 'permission_id' => $perm['id']])->countAllResults();
                if (!$exists) {
                    $db->table('role_permissions')->insert(['role_id' => 3, 'permission_id' => $perm['id']]);
                }
            }
            // Property Manager gets construction, inspections, handover
            if ($propManagerRole && (str_starts_with($perm['slug'], 'construction.') || str_starts_with($perm['slug'], 'inspections.') || str_starts_with($perm['slug'], 'handover.'))) {
                $exists = $db->table('role_permissions')->where(['role_id' => 5, 'permission_id' => $perm['id']])->countAllResults();
                if (!$exists) {
                    $db->table('role_permissions')->insert(['role_id' => 5, 'permission_id' => $perm['id']]);
                }
            }
        }
    }
}
