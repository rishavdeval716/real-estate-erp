<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Phase7PermissionSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        $phase7Permissions = [
            // Property Owners
            ['name' => 'View Property Owners',       'slug' => 'owners.view',             'group_name' => 'Property Owners',    'description' => 'View property owner profiles, portfolios and statements'],
            ['name' => 'Create Property Owner',     'slug' => 'owners.create',           'group_name' => 'Property Owners',    'description' => 'Register and onboard property owners'],
            ['name' => 'Edit Property Owner',       'slug' => 'owners.edit',             'group_name' => 'Property Owners',    'description' => 'Update owner details, KYC status and banking info'],
            ['name' => 'Delete Property Owner',     'slug' => 'owners.delete',           'group_name' => 'Property Owners',    'description' => 'Deactivate or soft-delete property owners'],

            // Agents / Brokers
            ['name' => 'View Agents & Brokers',     'slug' => 'agents.view',             'group_name' => 'Agents & Brokers',   'description' => 'View agent directory, performance and commission records'],
            ['name' => 'Create Agent / Broker',     'slug' => 'agents.create',           'group_name' => 'Agents & Brokers',   'description' => 'Onboard new channel partners and broker agents'],
            ['name' => 'Edit Agent / Broker',       'slug' => 'agents.edit',             'group_name' => 'Agents & Brokers',   'description' => 'Update agent license, commission rate and contact info'],
            ['name' => 'Delete Agent / Broker',     'slug' => 'agents.delete',           'group_name' => 'Agents & Brokers',   'description' => 'Suspend or delete agent / broker accounts'],

            // Property Expenses
            ['name' => 'View Property Expenses',    'slug' => 'expenses.view',           'group_name' => 'Property Expenses',  'description' => 'View expense records, vouchers and category summaries'],
            ['name' => 'Create Property Expense',  'slug' => 'expenses.create',         'group_name' => 'Property Expenses',  'description' => 'Record new maintenance, tax, repair and utility expenses'],
            ['name' => 'Edit Property Expense',    'slug' => 'expenses.edit',           'group_name' => 'Property Expenses',  'description' => 'Update expense details and approval status'],
            ['name' => 'Delete Property Expense',  'slug' => 'expenses.delete',         'group_name' => 'Property Expenses',  'description' => 'Cancel or soft-delete property expenses'],
            ['name' => 'Expense Reports',           'slug' => 'expenses.report',         'group_name' => 'Property Expenses',  'description' => 'Generate and print expense statements and monthly summaries'],

            // Marketing Campaigns
            ['name' => 'View Marketing Campaigns',  'slug' => 'campaigns.view',          'group_name' => 'Marketing',          'description' => 'View campaigns, social ads, lead analytics and ROI metrics'],
            ['name' => 'Create Marketing Campaign', 'slug' => 'campaigns.create',        'group_name' => 'Marketing',          'description' => 'Launch new marketing campaigns and ad listings'],
            ['name' => 'Edit Marketing Campaign',   'slug' => 'campaigns.edit',          'group_name' => 'Marketing',          'description' => 'Update campaign budget, spend, leads and status'],
            ['name' => 'Delete Marketing Campaign', 'slug' => 'campaigns.delete',        'group_name' => 'Marketing',          'description' => 'Remove or pause marketing campaigns'],

            // Legal & Property Documents
            ['name' => 'View Property Documents',   'slug' => 'documents.view',          'group_name' => 'Legal & Documents',  'description' => 'View title deeds, NOCs, permits and registry files'],
            ['name' => 'Upload Property Document',  'slug' => 'documents.create',        'group_name' => 'Legal & Documents',  'description' => 'Upload legal compliance and title documents'],
            ['name' => 'Edit Property Document',    'slug' => 'documents.edit',          'group_name' => 'Legal & Documents',  'description' => 'Update document metadata and expiry tracking'],
            ['name' => 'Delete Property Document',  'slug' => 'documents.delete',        'group_name' => 'Legal & Documents',  'description' => 'Remove compliance documents'],
            ['name' => 'Verify Property Document',  'slug' => 'documents.verify',        'group_name' => 'Legal & Documents',  'description' => 'Review and verify/reject submitted legal documents'],

            // Property Verifications
            ['name' => 'View Verifications',        'slug' => 'verifications.view',       'group_name' => 'Verifications',      'description' => 'View property verification audits and compliance status'],
            ['name' => 'Create Verification Audit', 'slug' => 'verifications.create',     'group_name' => 'Verifications',      'description' => 'Schedule property compliance and title audits'],
            ['name' => 'Edit Verification Audit',   'slug' => 'verifications.edit',       'group_name' => 'Verifications',      'description' => 'Update verification checkpoints and assignments'],
            ['name' => 'Conduct Verification',      'slug' => 'verifications.conduct',    'group_name' => 'Verifications',      'description' => 'Execute verification checklist and issue audit verdict'],

            // Notifications & Reminders
            ['name' => 'View Notifications',        'slug' => 'notifications.view',      'group_name' => 'Notifications',      'description' => 'Access notification center and overdue reminders'],
            ['name' => 'Create Notification',       'slug' => 'notifications.create',    'group_name' => 'Notifications',      'description' => 'Broadcast alerts and schedule event reminders'],
            ['name' => 'Manage Notifications',      'slug' => 'notifications.manage',    'group_name' => 'Notifications',      'description' => 'Mark notifications as read or dismiss reminders'],

            // Customer Communications
            ['name' => 'View Communications',       'slug' => 'communications.view',     'group_name' => 'Communications',     'description' => 'View client interaction timeline across call, SMS, email'],
            ['name' => 'Log Communication',         'slug' => 'communications.create',   'group_name' => 'Communications',     'description' => 'Log calls, meetings, WhatsApp notes and notices'],
            ['name' => 'Edit Communication',        'slug' => 'communications.edit',     'group_name' => 'Communications',     'description' => 'Update customer communication records'],

            // Settings & System Configuration
            ['name' => 'View System Settings',      'slug' => 'settings.view',           'group_name' => 'Settings',           'description' => 'View company, property and ERP system configurations'],
            ['name' => 'Edit System Settings',      'slug' => 'settings.edit',           'group_name' => 'Settings',           'description' => 'Modify business rules, payment terms and document policies'],
            ['name' => 'Backup System Data',        'slug' => 'settings.backup',         'group_name' => 'Settings',           'description' => 'Trigger database backups and archive exports'],
            ['name' => 'Export ERP Data',           'slug' => 'settings.export',         'group_name' => 'Settings',           'description' => 'Export system masters and financial registers'],

            // Reports Hub
            ['name' => 'View Reports Hub',          'slug' => 'reports.view',            'group_name' => 'Reports',            'description' => 'Access executive reporting center'],
            ['name' => 'Sales Reports',             'slug' => 'reports.sales',           'group_name' => 'Reports',            'description' => 'Generate property sales and booking revenue reports'],
            ['name' => 'Rental Reports',            'slug' => 'reports.rental',          'group_name' => 'Reports',            'description' => 'Generate lease occupancy and rent collection reports'],
            ['name' => 'Expense Reports Hub',       'slug' => 'reports.expenses',        'group_name' => 'Reports',            'description' => 'Generate property maintenance and operational expense reports'],
            ['name' => 'Marketing Reports',         'slug' => 'reports.marketing',       'group_name' => 'Reports',            'description' => 'Generate campaign performance and lead acquisition cost reports'],
        ];

        $now = date('Y-m-d H:i:s');
        foreach ($phase7Permissions as $p) {
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
        // Role 1 (Super Admin): Unrestricted (has inherent bypass, plus explicit mapping)
        // Role 2 (Admin): All operational Phase 7 permissions
        // Role 3 (Manager): View, create, edit for all Phase 7
        // Role 4 (Sales Executive): Agents view, campaigns view, communications view/log, notifications view
        // Role 5 (Property Manager): Owners, verifications, documents, expenses view/create, notifications
        // Role 6 (Accountant): Expenses all, reports all, owners view, agents view, settings view
        $allP7Perms = $db->table('permissions')->whereIn('group_name', [
            'Property Owners', 'Agents & Brokers', 'Property Expenses', 'Marketing',
            'Legal & Documents', 'Verifications', 'Notifications', 'Communications', 'Settings', 'Reports'
        ])->get()->getResultArray();

        foreach ($allP7Perms as $perm) {
            // Admin gets all
            $exists = $db->table('role_permissions')->where(['role_id' => 2, 'permission_id' => $perm['id']])->countAllResults();
            if (!$exists) {
                $db->table('role_permissions')->insert(['role_id' => 2, 'permission_id' => $perm['id']]);
            }

            // Manager gets view, create, edit
            if (!in_array($perm['slug'], ['settings.backup', 'settings.edit', 'owners.delete', 'agents.delete', 'expenses.delete', 'campaigns.delete'])) {
                $exists = $db->table('role_permissions')->where(['role_id' => 3, 'permission_id' => $perm['id']])->countAllResults();
                if (!$exists) {
                    $db->table('role_permissions')->insert(['role_id' => 3, 'permission_id' => $perm['id']]);
                }
            }

            // Sales Executive (id 4)
            if (in_array($perm['slug'], [
                'agents.view', 'campaigns.view', 'communications.view', 'communications.create',
                'notifications.view', 'notifications.manage', 'reports.view', 'reports.sales'
            ])) {
                $exists = $db->table('role_permissions')->where(['role_id' => 4, 'permission_id' => $perm['id']])->countAllResults();
                if (!$exists) {
                    $db->table('role_permissions')->insert(['role_id' => 4, 'permission_id' => $perm['id']]);
                }
            }

            // Property Manager (id 5)
            if (in_array($perm['slug'], [
                'owners.view', 'owners.create', 'owners.edit',
                'documents.view', 'documents.create', 'documents.edit', 'documents.verify',
                'verifications.view', 'verifications.create', 'verifications.edit', 'verifications.conduct',
                'expenses.view', 'expenses.create', 'expenses.edit',
                'notifications.view', 'notifications.manage', 'communications.view', 'communications.create'
            ])) {
                $exists = $db->table('role_permissions')->where(['role_id' => 5, 'permission_id' => $perm['id']])->countAllResults();
                if (!$exists) {
                    $db->table('role_permissions')->insert(['role_id' => 5, 'permission_id' => $perm['id']]);
                }
            }

            // Accountant (id 6)
            if (in_array($perm['slug'], [
                'expenses.view', 'expenses.create', 'expenses.edit', 'expenses.delete', 'expenses.report',
                'owners.view', 'agents.view',
                'reports.view', 'reports.sales', 'reports.rental', 'reports.expenses', 'reports.marketing',
                'settings.view', 'notifications.view', 'notifications.manage'
            ])) {
                $exists = $db->table('role_permissions')->where(['role_id' => 6, 'permission_id' => $perm['id']])->countAllResults();
                if (!$exists) {
                    $db->table('role_permissions')->insert(['role_id' => 6, 'permission_id' => $perm['id']]);
                }
            }
        }
    }
}
