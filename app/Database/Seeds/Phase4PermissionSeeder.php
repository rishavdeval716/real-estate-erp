<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Phase4PermissionSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        $phase4Permissions = [
            // Customers
            ['name' => 'View Customers',            'slug' => 'customers.view',            'group_name' => 'Customers',         'description' => 'View customer directories and buyer profiles'],
            ['name' => 'Create Customer',           'slug' => 'customers.create',          'group_name' => 'Customers',         'description' => 'Convert leads or create customer records'],
            ['name' => 'Edit Customer',             'slug' => 'customers.edit',            'group_name' => 'Customers',         'description' => 'Update customer information and demographics'],
            ['name' => 'Delete Customer',           'slug' => 'customers.delete',          'group_name' => 'Customers',         'description' => 'Soft delete or archive customer files'],
            ['name' => 'View Customer KYC',         'slug' => 'customers.kyc.view',        'group_name' => 'Customer KYC',      'description' => 'View uploaded identity and address proofs'],
            ['name' => 'Upload Customer KYC',       'slug' => 'customers.kyc.upload',      'group_name' => 'Customer KYC',      'description' => 'Upload buyer PAN, passport, or identity documents'],
            ['name' => 'Verify Customer KYC',       'slug' => 'customers.kyc.verify',      'group_name' => 'Customer KYC',      'description' => 'Approve or reject customer compliance documents'],

            // Bookings
            ['name' => 'View Bookings',             'slug' => 'bookings.view',             'group_name' => 'Bookings',          'description' => 'View property booking records and vouchers'],
            ['name' => 'Create Booking',            'slug' => 'bookings.create',           'group_name' => 'Bookings',          'description' => 'Create draft or pending booking transactions'],
            ['name' => 'Edit Booking',              'slug' => 'bookings.edit',             'group_name' => 'Bookings',          'description' => 'Update booking terms, pricing, or discounts'],
            ['name' => 'Confirm Booking',           'slug' => 'bookings.confirm',          'group_name' => 'Bookings',          'description' => 'Confirm booking and lock unit to Booked status'],
            ['name' => 'Cancel Booking',            'slug' => 'bookings.cancel',           'group_name' => 'Bookings',          'description' => 'Cancel booking and execute unit release rules'],

            // Sales Agreements
            ['name' => 'View Agreements',           'slug' => 'agreements.view',           'group_name' => 'Sales Agreements',  'description' => 'View legal sales agreements and contracts'],
            ['name' => 'Create Agreement',          'slug' => 'agreements.create',         'group_name' => 'Sales Agreements',  'description' => 'Generate contract drafts with terms & conditions'],
            ['name' => 'Edit Agreement',            'slug' => 'agreements.edit',           'group_name' => 'Sales Agreements',  'description' => 'Modify special conditions and agreement terms'],
            ['name' => 'Sign Agreement',            'slug' => 'agreements.sign',           'group_name' => 'Sales Agreements',  'description' => 'Mark agreement executed and signed'],
            ['name' => 'Cancel Agreement',          'slug' => 'agreements.cancel',         'group_name' => 'Sales Agreements',  'description' => 'Revoke or cancel sales agreements'],

            // Payment Schedules
            ['name' => 'View Payment Schedules',    'slug' => 'payment_schedules.view',    'group_name' => 'Payment Schedules', 'description' => 'View milestone payment plans and installment structures'],
            ['name' => 'Create Payment Schedule',   'slug' => 'payment_schedules.create',  'group_name' => 'Payment Schedules', 'description' => 'Configure construction-linked payment milestones'],
            ['name' => 'Edit Payment Schedule',     'slug' => 'payment_schedules.edit',    'group_name' => 'Payment Schedules', 'description' => 'Adjust milestone percentages or due dates'],

            // Payments
            ['name' => 'View Payments',             'slug' => 'payments.view',             'group_name' => 'Payments',          'description' => 'View payment transaction ledger and collections'],
            ['name' => 'Record Payment',            'slug' => 'payments.create',           'group_name' => 'Payments',          'description' => 'Record payments received (Cheque, NEFT, RTGS, etc.)'],
            ['name' => 'Edit Payment',              'slug' => 'payments.edit',             'group_name' => 'Payments',          'description' => 'Update transaction references or payment notes'],
            ['name' => 'Cancel Payment',            'slug' => 'payments.cancel',           'group_name' => 'Payments',          'description' => 'Void or cancel recorded payment transactions'],

            // Invoices
            ['name' => 'View Invoices',             'slug' => 'invoices.view',             'group_name' => 'Invoices',          'description' => 'View milestone payment demand notices and invoices'],
            ['name' => 'Create Invoice',            'slug' => 'invoices.create',           'group_name' => 'Invoices',          'description' => 'Generate tax/demand invoices for payment milestones'],
            ['name' => 'Edit Invoice',              'slug' => 'invoices.edit',             'group_name' => 'Invoices',          'description' => 'Modify invoice line items and tax calculations'],
            ['name' => 'Cancel Invoice',            'slug' => 'invoices.cancel',           'group_name' => 'Invoices',          'description' => 'Cancel or void issued invoices'],

            // Receipts
            ['name' => 'View Receipts',             'slug' => 'receipts.view',             'group_name' => 'Receipts',          'description' => 'View issued payment receipts and vouchers'],
            ['name' => 'Create Receipt',            'slug' => 'receipts.create',           'group_name' => 'Receipts',          'description' => 'Issue official receipt upon payment clearance'],

            // Commissions
            ['name' => 'View Commissions',          'slug' => 'commissions.view',          'group_name' => 'Commissions',       'description' => 'View broker and agent commission statements'],
            ['name' => 'Create Commission',         'slug' => 'commissions.create',        'group_name' => 'Commissions',       'description' => 'Calculate and record commission allocations'],
            ['name' => 'Approve Commission',        'slug' => 'commissions.approve',       'group_name' => 'Commissions',       'description' => 'Approve commission payouts for release'],
            ['name' => 'Mark Commission Paid',      'slug' => 'commissions.mark_paid',     'group_name' => 'Commissions',       'description' => 'Record disbursement of commission amounts'],

            // Sales Dashboard
            ['name' => 'View Sales Dashboard',      'slug' => 'sales_dashboard.view',      'group_name' => 'Sales Dashboard',   'description' => 'View executive sales metrics and revenue summaries'],
        ];

        foreach ($phase4Permissions as $perm) {
            $existing = $db->table('permissions')->where('slug', $perm['slug'])->get()->getRowArray();
            if (!$existing) {
                $db->table('permissions')->insert([
                    'name'        => $perm['name'],
                    'slug'        => $perm['slug'],
                    'group_name'  => $perm['group_name'],
                    'description' => $perm['description'],
                    'created_at'  => date('Y-m-d H:i:s'),
                    'updated_at'  => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // Map to Roles
        $adminRole   = $db->table('roles')->where('name', 'Admin')->get()->getRowArray();
        $managerRole = $db->table('roles')->where('name', 'Manager')->get()->getRowArray();
        $salesRole   = $db->table('roles')->where('name', 'Sales Executive')->get()->getRowArray();
        $allPermissions = $db->table('permissions')->get()->getResultArray();

        // 1. Admin gets all permissions
        if ($adminRole) {
            foreach ($allPermissions as $p) {
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

        // 2. Manager gets all Phase 4 transactional permissions
        if ($managerRole) {
            $phase4Slugs = array_column($permissions, 'slug');
            foreach ($allPermissions as $p) {
                if (in_array($p['slug'], $phase4Slugs, true)) {
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

        // 3. Sales Executive gets customer creation, booking drafting, payment recording, viewing invoices/receipts
        if ($salesRole) {
            $salesAllowedSlugs = [
                'customers.view', 'customers.create', 'customers.edit', 'customers.kyc.view', 'customers.kyc.upload',
                'bookings.view', 'bookings.create', 'bookings.edit',
                'agreements.view',
                'payment_schedules.view',
                'payments.view', 'payments.create',
                'invoices.view',
                'receipts.view',
                'commissions.view',
                'sales_dashboard.view',
            ];

            foreach ($allPermissions as $p) {
                if (in_array($p['slug'], $salesAllowedSlugs, true)) {
                    $exists = $db->table('role_permissions')->where([
                        'role_id'       => $salesRole['id'],
                        'permission_id' => $p['id'],
                    ])->countAllResults();

                    if ($exists === 0) {
                        $db->table('role_permissions')->insert([
                            'role_id'       => $salesRole['id'],
                            'permission_id' => $p['id'],
                        ]);
                    }
                }
            }
        }
    }
}
