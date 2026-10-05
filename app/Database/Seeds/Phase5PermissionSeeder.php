<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Phase5PermissionSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        $phase5Permissions = [
            // Tenants & Tenant KYC
            ['name' => 'View Tenants',                'slug' => 'tenants.view',                'group_name' => 'Tenants',         'description' => 'View tenant directories and rental profiles'],
            ['name' => 'Create Tenant',               'slug' => 'tenants.create',              'group_name' => 'Tenants',         'description' => 'Register new tenant records and leaseholders'],
            ['name' => 'Edit Tenant',                 'slug' => 'tenants.edit',                'group_name' => 'Tenants',         'description' => 'Update tenant information and contacts'],
            ['name' => 'Delete Tenant',               'slug' => 'tenants.delete',              'group_name' => 'Tenants',         'description' => 'Soft delete or archive tenant profiles'],
            ['name' => 'Manage Tenant KYC',          'slug' => 'tenants.kyc',                 'group_name' => 'Tenants',         'description' => 'Upload and verify tenant KYC compliance documents'],

            // Lease Agreements
            ['name' => 'View Lease Agreements',       'slug' => 'leases.view',                 'group_name' => 'Leases',          'description' => 'View residential and commercial lease agreements'],
            ['name' => 'Create Lease Agreement',      'slug' => 'leases.create',               'group_name' => 'Leases',          'description' => 'Draft new long-term lease contracts'],
            ['name' => 'Edit Lease Agreement',        'slug' => 'leases.edit',                 'group_name' => 'Leases',          'description' => 'Update lease terms, lock-in period and rules'],
            ['name' => 'Activate Lease Agreement',    'slug' => 'leases.activate',             'group_name' => 'Leases',          'description' => 'Activate lease agreement and mark unit Rented'],
            ['name' => 'Terminate Lease Agreement',   'slug' => 'leases.terminate',            'group_name' => 'Leases',          'description' => 'Terminate lease contract and release unit'],
            ['name' => 'Renew Lease Agreement',       'slug' => 'leases.renew',                'group_name' => 'Leases',          'description' => 'Execute lease renewal with rent escalation'],

            // Security Deposits
            ['name' => 'View Security Deposits',      'slug' => 'deposits.view',               'group_name' => 'Security Deposits', 'description' => 'View security deposit register and status'],
            ['name' => 'Manage Security Deposits',    'slug' => 'deposits.manage',             'group_name' => 'Security Deposits', 'description' => 'Process deposit refunds, deductions and forfeitures'],

            // Rent Demands
            ['name' => 'View Rent Demands',           'slug' => 'rent_demands.view',           'group_name' => 'Rent Billing',    'description' => 'View monthly rent demand notices and ledgers'],
            ['name' => 'Generate Rent Demand',        'slug' => 'rent_demands.create',         'group_name' => 'Rent Billing',    'description' => 'Generate monthly recurring rent invoices with late fees'],

            // Rent Collections
            ['name' => 'View Rent Collections',       'slug' => 'rent_collections.view',       'group_name' => 'Rent Billing',    'description' => 'View recorded rent payments and collection log'],
            ['name' => 'Record Rent Collection',      'slug' => 'rent_collections.create',     'group_name' => 'Rent Billing',    'description' => 'Record tenant rent payments and issue receipts'],

            // Facility Assets
            ['name' => 'View Facility Assets',        'slug' => 'facility_assets.view',        'group_name' => 'Facility Assets', 'description' => 'View building assets, elevators, generators and HVAC'],
            ['name' => 'Create Facility Asset',       'slug' => 'facility_assets.create',      'group_name' => 'Facility Assets', 'description' => 'Register new building equipment and warranty logs'],
            ['name' => 'Edit Facility Asset',         'slug' => 'facility_assets.edit',        'group_name' => 'Facility Assets', 'description' => 'Update equipment specifications and maintenance status'],
            ['name' => 'Delete Facility Asset',       'slug' => 'facility_assets.delete',      'group_name' => 'Facility Assets', 'description' => 'Decommission or remove facility assets'],

            // Technicians
            ['name' => 'View Technicians',            'slug' => 'technicians.view',            'group_name' => 'Technicians',     'description' => 'View internal service staff and technician roster'],
            ['name' => 'Create Technician',           'slug' => 'technicians.create',          'group_name' => 'Technicians',     'description' => 'Add service technicians with skills and departments'],
            ['name' => 'Edit Technician',             'slug' => 'technicians.edit',            'group_name' => 'Technicians',     'description' => 'Update technician availability and contact details'],
            ['name' => 'Delete Technician',           'slug' => 'technicians.delete',          'group_name' => 'Technicians',     'description' => 'Remove or deactivate technicians'],

            // Maintenance & Work Orders
            ['name' => 'View Work Orders',            'slug' => 'maintenance.view',            'group_name' => 'Maintenance',     'description' => 'View maintenance tickets and work orders'],
            ['name' => 'Create Work Order',           'slug' => 'maintenance.create',          'group_name' => 'Maintenance',     'description' => 'Log new maintenance requests and service jobs'],
            ['name' => 'Manage Work Order',           'slug' => 'maintenance.manage',          'group_name' => 'Maintenance',     'description' => 'Assign technicians and resolve work orders within SLA'],

            // Complaints
            ['name' => 'View Complaints',             'slug' => 'complaints.view',             'group_name' => 'Complaints',      'description' => 'View tenant and resident complaint tickets'],
            ['name' => 'Create Complaint',            'slug' => 'complaints.create',           'group_name' => 'Complaints',      'description' => 'Register resident grievances and service complaints'],
            ['name' => 'Manage Complaint',            'slug' => 'complaints.manage',           'group_name' => 'Complaints',      'description' => 'Investigate and resolve complaints and track ratings'],

            // Preventive Maintenance
            ['name' => 'View Preventive Schedules',   'slug' => 'preventive.view',             'group_name' => 'Preventive AMC',  'description' => 'View recurring maintenance and inspection schedules'],
            ['name' => 'Create Preventive Schedule',  'slug' => 'preventive.create',           'group_name' => 'Preventive AMC',  'description' => 'Set up recurring maintenance inspection frequencies'],
            ['name' => 'Manage Preventive Schedule',  'slug' => 'preventive.manage',           'group_name' => 'Preventive AMC',  'description' => 'Record completion and advance next service dates'],

            // CAM Charges
            ['name' => 'View CAM Charges',            'slug' => 'cam.view',                    'group_name' => 'CAM Charges',     'description' => 'View common area maintenance billing records'],
            ['name' => 'Create CAM Charge',           'slug' => 'cam.create',                  'group_name' => 'CAM Charges',     'description' => 'Calculate CAM distribution per sq.ft or flat rate'],
            ['name' => 'Manage CAM Charge',           'slug' => 'cam.manage',                  'group_name' => 'CAM Charges',     'description' => 'Update CAM billing status and payments'],

            // Dedicated Portals
            ['name' => 'Access Owner Portal',         'slug' => 'portal.owner',                'group_name' => 'Portals',         'description' => 'Access self-service Customer/Owner Portal'],
            ['name' => 'Access Tenant Portal',        'slug' => 'portal.tenant',               'group_name' => 'Portals',         'description' => 'Access self-service Resident Tenant Portal'],
            ['name' => 'Access Partner Portal',       'slug' => 'portal.partner',              'group_name' => 'Portals',         'description' => 'Access self-service Channel Partner Portal'],

            // Statutory Reporting & Finance
            ['name' => 'View GST Reports',            'slug' => 'reports.gst',                 'group_name' => 'Finance Reports', 'description' => 'View GSTR-1 and GSTR-3B statutory tax reports'],
            ['name' => 'View TDS Management',         'slug' => 'reports.tds',                 'group_name' => 'Finance Reports', 'description' => 'Manage Section 194H TDS entries and Form 16A'],
            ['name' => 'View Accounts Receivable',    'slug' => 'reports.receivables',         'group_name' => 'Finance Reports', 'description' => 'Access receivables ageing analysis and bad debt tracking'],
        ];

        // 1. Insert permissions
        foreach ($phase5Permissions as $perm) {
            $existing = $db->table('permissions')->where('slug', $perm['slug'])->get()->getRowArray();
            if (!$existing) {
                $perm['created_at'] = date('Y-m-d H:i:s');
                $perm['updated_at'] = date('Y-m-d H:i:s');
                $db->table('permissions')->insert($perm);
            }
        }

        // 2. Fetch role IDs
        $roles = $db->table('roles')->get()->getResultArray();
        $roleMap = [];
        foreach ($roles as $r) {
            $roleMap[$r['name']] = (int)$r['id'];
        }

        // 3. Fetch all permission IDs
        $allPerms = $db->table('permissions')->get()->getResultArray();
        $permMap = [];
        foreach ($allPerms as $p) {
            $permMap[$p['slug']] = (int)$p['id'];
        }

        // Helper to assign
        $assign = function($roleName, array $slugs) use ($db, $roleMap, $permMap) {
            if (!isset($roleMap[$roleName])) return;
            $roleId = $roleMap[$roleName];

            foreach ($slugs as $slug) {
                if (isset($permMap[$slug])) {
                    $permId = $permMap[$slug];
                    $exists = $db->table('role_permissions')
                        ->where('role_id', $roleId)
                        ->where('permission_id', $permId)
                        ->get()->getRowArray();
                    if (!$exists) {
                        $db->table('role_permissions')->insert([
                            'role_id'       => $roleId,
                            'permission_id' => $permId,
                        ]);
                    }
                }
            }
        };

        // Super Admin gets all permissions automatically
        $allSlugs = array_column($phase5Permissions, 'slug');
        $assign('Super Admin', $allSlugs);

        // Admin
        $assign('Admin', $allSlugs);

        // Property Manager
        $assign('Property Manager', [
            'tenants.view', 'tenants.create', 'tenants.edit', 'tenants.kyc',
            'leases.view', 'leases.create', 'leases.edit', 'leases.activate', 'leases.terminate', 'leases.renew',
            'deposits.view', 'deposits.manage',
            'rent_demands.view', 'rent_demands.create',
            'facility_assets.view', 'facility_assets.create', 'facility_assets.edit',
            'technicians.view', 'technicians.create', 'technicians.edit',
            'maintenance.view', 'maintenance.create', 'maintenance.manage',
            'complaints.view', 'complaints.create', 'complaints.manage',
            'preventive.view', 'preventive.create', 'preventive.manage',
            'cam.view', 'cam.create', 'cam.manage',
        ]);

        // Accountant
        $assign('Accountant', [
            'tenants.view',
            'leases.view',
            'deposits.view', 'deposits.manage',
            'rent_demands.view', 'rent_demands.create',
            'rent_collections.view', 'rent_collections.create',
            'cam.view', 'cam.create', 'cam.manage',
            'reports.gst', 'reports.tds', 'reports.receivables',
        ]);

        // Customer
        $assign('Customer', [
            'portal.owner',
        ]);

        // Tenant
        $assign('Tenant', [
            'portal.tenant',
        ]);

        // Agent / Broker
        $assign('Agent/Broker', [
            'portal.partner',
        ]);

        echo "Phase 5 permissions seeded successfully (" . count($phase5Permissions) . " permission keys).\n";
    }
}
