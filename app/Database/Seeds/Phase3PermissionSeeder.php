<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Phase3PermissionSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        $phase3Permissions = [
            // Lead Sources
            ['name' => 'View Lead Sources',     'slug' => 'lead_sources.view',      'group_name' => 'Lead Sources',     'description' => 'View lead acquisition sources and marketing channels'],
            ['name' => 'Create Lead Source',    'slug' => 'lead_sources.create',    'group_name' => 'Lead Sources',     'description' => 'Create new lead sources and marketing attribution tags'],
            ['name' => 'Edit Lead Source',      'slug' => 'lead_sources.edit',      'group_name' => 'Lead Sources',     'description' => 'Edit existing lead source details and status'],
            ['name' => 'Delete Lead Source',    'slug' => 'lead_sources.delete',    'group_name' => 'Lead Sources',     'description' => 'Soft delete unused lead sources'],

            // Leads
            ['name' => 'View Leads',            'slug' => 'leads.view',             'group_name' => 'Leads',            'description' => 'View CRM leads listing and comprehensive profile'],
            ['name' => 'Create Lead',           'slug' => 'leads.create',           'group_name' => 'Leads',            'description' => 'Add new leads to the CRM database'],
            ['name' => 'Edit Lead',             'slug' => 'leads.edit',             'group_name' => 'Leads',            'description' => 'Update contact information, budgets, and preferences'],
            ['name' => 'Delete Lead',           'slug' => 'leads.delete',           'group_name' => 'Leads',            'description' => 'Soft delete or archive leads'],
            ['name' => 'Assign Lead',           'slug' => 'leads.assign',           'group_name' => 'Leads',            'description' => 'Assign or reassign leads to sales executives'],
            ['name' => 'Change Lead Stage',     'slug' => 'leads.change_stage',     'group_name' => 'Leads',            'description' => 'Advance or modify CRM pipeline stage'],
            ['name' => 'Change Lead Status',    'slug' => 'leads.change_status',    'group_name' => 'Leads',            'description' => 'Update lead qualification and closure status'],

            // Prospects / Customers
            ['name' => 'View Prospects',        'slug' => 'prospects.view',         'group_name' => 'Prospects',        'description' => 'View customer prospect demographic profiles'],
            ['name' => 'Create Prospect',       'slug' => 'prospects.create',       'group_name' => 'Prospects',        'description' => 'Create customer prospect profiles'],
            ['name' => 'Edit Prospect',         'slug' => 'prospects.edit',         'group_name' => 'Prospects',        'description' => 'Update prospect personal and occupational data'],
            ['name' => 'Delete Prospect',       'slug' => 'prospects.delete',       'group_name' => 'Prospects',        'description' => 'Delete prospect records'],

            // Enquiries
            ['name' => 'View Enquiries',        'slug' => 'enquiries.view',         'group_name' => 'Enquiries',        'description' => 'View property enquiries and specific requirements'],
            ['name' => 'Create Enquiry',        'slug' => 'enquiries.create',       'group_name' => 'Enquiries',        'description' => 'Record new purchase or rental enquiries'],
            ['name' => 'Edit Enquiry',          'slug' => 'enquiries.edit',         'group_name' => 'Enquiries',        'description' => 'Update requirement and enquiry status'],
            ['name' => 'Delete Enquiry',        'slug' => 'enquiries.delete',       'group_name' => 'Enquiries',        'description' => 'Delete enquiry records'],

            // Follow-ups
            ['name' => 'View Follow-ups',       'slug' => 'followups.view',         'group_name' => 'Follow-ups',       'description' => 'View scheduled and completed follow-up activities'],
            ['name' => 'Create Follow-up',      'slug' => 'followups.create',       'group_name' => 'Follow-ups',       'description' => 'Schedule calls, meetings, WhatsApp, and emails'],
            ['name' => 'Edit Follow-up',        'slug' => 'followups.edit',         'group_name' => 'Follow-ups',       'description' => 'Reschedule or edit pending follow-ups'],
            ['name' => 'Delete Follow-up',      'slug' => 'followups.delete',       'group_name' => 'Follow-ups',       'description' => 'Cancel or remove follow-ups'],
            ['name' => 'Complete Follow-up',    'slug' => 'followups.complete',     'group_name' => 'Follow-ups',       'description' => 'Log outcomes and complete follow-up tasks'],

            // Site Visits
            ['name' => 'View Site Visits',      'slug' => 'site_visits.view',       'group_name' => 'Site Visits',      'description' => 'View scheduled, completed and past property site visits'],
            ['name' => 'Schedule Site Visit',   'slug' => 'site_visits.create',     'group_name' => 'Site Visits',      'description' => 'Book project or property site visits with clients'],
            ['name' => 'Edit Site Visit',       'slug' => 'site_visits.edit',       'group_name' => 'Site Visits',      'description' => 'Reschedule or modify site visit appointments'],
            ['name' => 'Delete Site Visit',     'slug' => 'site_visits.delete',     'group_name' => 'Site Visits',      'description' => 'Cancel or remove site visits'],
            ['name' => 'Complete Site Visit',   'slug' => 'site_visits.complete',   'group_name' => 'Site Visits',      'description' => 'Record visit feedback, rating and agent observations'],

            // Pipeline
            ['name' => 'View CRM Pipeline',     'slug' => 'pipeline.view',          'group_name' => 'Pipeline',         'description' => 'View Kanban CRM visual sales pipeline'],
            ['name' => 'Manage Pipeline',       'slug' => 'pipeline.manage',        'group_name' => 'Pipeline',         'description' => 'Move leads across pipeline stages and reorder'],

            // Unit Holds (Temporary Reservation)
            ['name' => 'View Unit Holds',       'slug' => 'unit_holds.view',        'group_name' => 'Unit Holds',       'description' => 'View active and historical temporary unit reservations'],
            ['name' => 'Create Unit Hold',      'slug' => 'unit_holds.create',      'group_name' => 'Unit Holds',       'description' => 'Place temporary reservation holds on available units'],
            ['name' => 'Release Unit Hold',     'slug' => 'unit_holds.release',     'group_name' => 'Unit Holds',       'description' => 'Release unit holds back to available inventory'],

            // CRM Dashboard
            ['name' => 'View CRM Dashboard',    'slug' => 'crm_dashboard.view',     'group_name' => 'CRM Dashboard',    'description' => 'View CRM lead conversion and follow-up metrics'],
        ];

        foreach ($phase3Permissions as $perm) {
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

        // Assign to Admin role
        $adminRole = $db->table('roles')->where('name', 'Admin')->get()->getRowArray();
        $managerRole = $db->table('roles')->where('name', 'Manager')->get()->getRowArray();
        $salesRole = $db->table('roles')->where('name', 'Sales Executive')->get()->getRowArray();
        $allPermissions = $db->table('permissions')->get()->getResultArray();

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

        // Assign to Manager
        if ($managerRole) {
            $managerAllowed = [
                'lead_sources.view',
                'leads.view', 'leads.create', 'leads.edit', 'leads.assign', 'leads.change_stage', 'leads.change_status',
                'prospects.view', 'prospects.create', 'prospects.edit',
                'enquiries.view', 'enquiries.create', 'enquiries.edit',
                'followups.view', 'followups.create', 'followups.edit', 'followups.complete',
                'site_visits.view', 'site_visits.create', 'site_visits.edit', 'site_visits.complete',
                'pipeline.view', 'pipeline.manage',
                'unit_holds.view', 'unit_holds.create', 'unit_holds.release',
                'crm_dashboard.view',
            ];

            foreach ($allPermissions as $p) {
                if (in_array($p['slug'], $managerAllowed, true)) {
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

        // Assign to Sales Executive
        if ($salesRole) {
            $salesAllowed = [
                'leads.view', 'leads.create', 'leads.edit', 'leads.change_stage',
                'prospects.view', 'prospects.create', 'prospects.edit',
                'enquiries.view', 'enquiries.create', 'enquiries.edit',
                'followups.view', 'followups.create', 'followups.edit', 'followups.complete',
                'site_visits.view', 'site_visits.create', 'site_visits.edit', 'site_visits.complete',
                'pipeline.view',
                'unit_holds.view',
                'crm_dashboard.view',
            ];

            foreach ($allPermissions as $p) {
                if (in_array($p['slug'], $salesAllowed, true)) {
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
