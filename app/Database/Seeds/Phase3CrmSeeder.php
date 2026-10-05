<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Phase3CrmSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        // 1. Ensure Sales Executive user exists
        $salesRole = $db->table('roles')->where('name', 'Sales Executive')->get()->getRowArray();
        $hqBranch  = $db->table('branches')->where('code', 'BR-HQ-001')->get()->getRowArray();
        $branchId  = $hqBranch ? $hqBranch['id'] : 1;

        $salesUser = $db->table('users')->where('email', 'sales@realestate-erp.local')->get()->getRowArray();
        if (!$salesUser) {
            $salesUserId = $db->table('users')->insert([
                'name'       => 'Rajesh Sharma (Senior Sales)',
                'email'      => 'sales@realestate-erp.local',
                'phone'      => '+91 98200 99881',
                'password'   => password_hash('Password@123', PASSWORD_BCRYPT),
                'status'     => 'active',
                'branch_id'  => $branchId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $salesUserId = $db->insertID();

            if ($salesRole) {
                $db->table('user_roles')->insert([
                    'user_id' => $salesUserId,
                    'role_id' => $salesRole['id'],
                ]);
            }
        } else {
            $salesUserId = $salesUser['id'];
        }

        $adminUser   = $db->table('users')->where('email', 'admin@realestate-erp.local')->get()->getRowArray();
        $adminUserId = $adminUser ? $adminUser['id'] : 1;

        // 2. Seed Lead Sources (12 Sources)
        $sources = [
            ['name' => 'Website',          'slug' => 'website',          'description' => 'Direct organic traffic from official corporate web portal'],
            ['name' => 'Direct Enquiry',   'slug' => 'direct-enquiry',   'description' => 'Direct walk-in or telephone inquiry at branch office'],
            ['name' => 'Phone Call',       'slug' => 'phone-call',       'description' => 'Inbound telephone call inquiry'],
            ['name' => 'Walk-in',          'slug' => 'walk-in',          'description' => 'Unscheduled physical visit to project sales gallery'],
            ['name' => 'Referral',         'slug' => 'referral',         'description' => 'Existing client, partner, or staff member referral'],
            ['name' => 'Google',           'slug' => 'google',           'description' => 'Google Search and Performance Max ad campaigns'],
            ['name' => 'Facebook',         'slug' => 'facebook',         'description' => 'Meta Facebook targeted real estate lead ad generation'],
            ['name' => 'Instagram',        'slug' => 'instagram',        'description' => 'Instagram sponsored luxury property showcase campaigns'],
            ['name' => 'Property Portal',  'slug' => 'property-portal',  'description' => '99acres, MagicBricks, and Housing.com syndication listings'],
            ['name' => 'Broker/Agent',     'slug' => 'broker-agent',     'description' => 'RERA-registered channel partner or external broker network'],
            ['name' => 'Advertisement',    'slug' => 'advertisement',    'description' => 'Print newspaper, hoarding, airport display ads'],
            ['name' => 'Other',            'slug' => 'other',            'description' => 'Other miscellaneous acquisition channels'],
        ];

        foreach ($sources as $s) {
            $existing = $db->table('lead_sources')->where('slug', $s['slug'])->get()->getRowArray();
            if (!$existing) {
                $db->table('lead_sources')->insert([
                    'name'        => $s['name'],
                    'slug'        => $s['slug'],
                    'description' => $s['description'],
                    'status'      => 'active',
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }
        }

        // Fetch source IDs
        $sourceWeb    = $db->table('lead_sources')->where('slug', 'website')->get()->getRowArray()['id'] ?? 1;
        $sourceRef    = $db->table('lead_sources')->where('slug', 'referral')->get()->getRowArray()['id'] ?? 2;
        $sourceGgl    = $db->table('lead_sources')->where('slug', 'google')->get()->getRowArray()['id'] ?? 3;
        $sourcePortal = $db->table('lead_sources')->where('slug', 'property-portal')->get()->getRowArray()['id'] ?? 4;
        $sourceDirect = $db->table('lead_sources')->where('slug', 'direct-enquiry')->get()->getRowArray()['id'] ?? 5;

        // Fetch reference property & project IDs
        $project1 = $db->table('projects')->where('project_code', 'PRJ-MUM-001')->get()->getRowArray();
        $project2 = $db->table('projects')->where('project_code', 'PRJ-BLR-002')->get()->getRowArray();
        $prop1    = $db->table('properties')->where('property_code', 'PROP-MUM-001')->get()->getRowArray();
        $prop2    = $db->table('properties')->where('property_code', 'PROP-BLR-002')->get()->getRowArray();

        $proj1Id = $project1 ? $project1['id'] : null;
        $proj2Id = $project2 ? $project2['id'] : null;
        $prop1Id = $prop1 ? $prop1['id'] : null;
        $prop2Id = $prop2 ? $prop2['id'] : null;

        $unit2 = $db->table('property_units')->where('unit_number', 'A-102')->get()->getRowArray();
        $unit2Id = $unit2 ? $unit2['id'] : null;

        // 3. Seed Demo Leads (LEAD-2026-000001 through LEAD-2026-000005)
        $leadsData = [
            [
                'lead_code'           => 'LEAD-2026-000001',
                'first_name'          => 'Vikramaditya',
                'last_name'           => 'Singhania',
                'email'               => 'vikram.singhania@apexventures.in',
                'phone'               => '+91 98200 12345',
                'alternate_phone'     => '+91 22 2640 9900',
                'lead_source_id'      => $sourceWeb,
                'assigned_user_id'    => $salesUserId,
                'branch_id'           => $branchId,
                'lead_status'         => 'Qualified',
                'lead_stage'          => 'Site Visit Scheduled',
                'priority'            => 'High',
                'budget_min'          => 40000000.00,
                'budget_max'          => 50000000.00,
                'preferred_location'  => 'Bandra West, Mumbai',
                'property_type_id'    => 1, // Residential
                'project_id'          => $proj1Id,
                'property_id'         => $prop1Id,
                'property_unit_id'    => null,
                'purchase_purpose'    => 'End Use / Family Home',
                'purchase_timeline'   => '1-3 Months',
                'financing_required'  => 'No',
                'site_visit_required' => 'Scheduled',
                'remarks'             => 'Looking for a sea-facing 3/4 BHK premium unit with 2 car parks.',
            ],
            [
                'lead_code'           => 'LEAD-2026-000002',
                'first_name'          => 'Ananya',
                'last_name'           => 'Deshmukh',
                'email'               => 'ananya.deshmukh@techcorp.io',
                'phone'               => '+91 98450 67890',
                'alternate_phone'     => null,
                'lead_source_id'      => $sourceRef,
                'assigned_user_id'    => $salesUserId,
                'branch_id'           => $branchId,
                'lead_status'         => 'Qualified',
                'lead_stage'          => 'Negotiation',
                'priority'            => 'Urgent',
                'budget_min'          => 60000000.00,
                'budget_max'          => 70000000.00,
                'preferred_location'  => 'Whitefield, Bengaluru',
                'property_type_id'    => 2, // Commercial / Villa
                'project_id'          => $proj2Id,
                'property_id'         => $prop2Id,
                'property_unit_id'    => null,
                'purchase_purpose'    => 'Luxury Residence',
                'purchase_timeline'   => 'Immediate',
                'financing_required'  => 'No',
                'site_visit_required' => 'Completed',
                'remarks'             => 'Interested in Grand Verdant Villa 12. Discussing price adjustments and payment schedule.',
            ],
            [
                'lead_code'           => 'LEAD-2026-000003',
                'first_name'          => 'Rohit',
                'last_name'           => 'Verma',
                'email'               => 'rohit.verma@fintech.co',
                'phone'               => '+91 97110 54321',
                'alternate_phone'     => null,
                'lead_source_id'      => $sourceGgl,
                'assigned_user_id'    => $salesUserId,
                'branch_id'           => $branchId,
                'lead_status'         => 'New',
                'lead_stage'          => 'New',
                'priority'            => 'Medium',
                'budget_min'          => 15000000.00,
                'budget_max'          => 20000000.00,
                'preferred_location'  => 'Cyberabad / HITEC City',
                'property_type_id'    => 1,
                'project_id'          => $proj1Id,
                'property_id'         => null,
                'property_unit_id'    => null,
                'purchase_purpose'    => 'Investment',
                'purchase_timeline'   => '3-6 Months',
                'financing_required'  => 'Yes',
                'site_visit_required' => 'No',
                'remarks'             => 'Inquired via Google search ad. Needs brochure and floor plan sent via WhatsApp.',
            ],
            [
                'lead_code'           => 'LEAD-2026-000004',
                'first_name'          => 'Dr. Meera',
                'last_name'           => 'Nambiar',
                'email'               => 'dr.meera@medicare.org',
                'phone'               => '+91 98860 99887',
                'alternate_phone'     => '+91 80 4122 3344',
                'lead_source_id'      => $sourcePortal,
                'assigned_user_id'    => $adminUserId,
                'branch_id'           => $branchId,
                'lead_status'         => 'Qualified',
                'lead_stage'          => 'Token Pending',
                'priority'            => 'Urgent',
                'budget_min'          => 42000000.00,
                'budget_max'          => 48000000.00,
                'preferred_location'  => 'Bandra West, Mumbai',
                'property_type_id'    => 1,
                'project_id'          => $proj1Id,
                'property_id'         => $prop1Id,
                'property_unit_id'    => $unit2Id,
                'purchase_purpose'    => 'Family Residence',
                'purchase_timeline'   => 'Immediate',
                'financing_required'  => 'No',
                'site_visit_required' => 'Completed',
                'remarks'             => 'Selected Unit A-102. Temporary hold requested while preparing token check.',
            ],
            [
                'lead_code'           => 'LEAD-2026-000005',
                'first_name'          => 'Kunal',
                'last_name'           => 'Malhotra',
                'email'               => 'kunal.malhotra@gmail.com',
                'phone'               => '+91 99300 44556',
                'alternate_phone'     => null,
                'lead_source_id'      => $sourceDirect,
                'assigned_user_id'    => $salesUserId,
                'branch_id'           => $branchId,
                'lead_status'         => 'Lost',
                'lead_stage'          => 'Lost',
                'priority'            => 'Low',
                'budget_min'          => 8000000.00,
                'budget_max'          => 10000000.00,
                'preferred_location'  => 'Suburban Outskirts',
                'property_type_id'    => 1,
                'project_id'          => null,
                'property_id'         => null,
                'property_unit_id'    => null,
                'purchase_purpose'    => 'First Home',
                'purchase_timeline'   => '6+ Months',
                'financing_required'  => 'Yes',
                'site_visit_required' => 'No',
                'remarks'             => 'Budget mismatch for prime Bandra property. Lead closed as lost for current portfolio.',
            ],
        ];

        foreach ($leadsData as $ld) {
            $existing = $db->table('leads')->where('lead_code', $ld['lead_code'])->get()->getRowArray();
            if (!$existing) {
                $ld['created_at'] = $now;
                $ld['updated_at'] = $now;
                $db->table('leads')->insert($ld);
                $leadId = $db->insertID();

                // Prospect Demographic
                $db->table('prospects')->insert([
                    'lead_id'                  => $leadId,
                    'name'                     => trim($ld['first_name'] . ' ' . $ld['last_name']),
                    'email'                    => $ld['email'],
                    'phone'                    => $ld['phone'],
                    'alternate_phone'          => $ld['alternate_phone'],
                    'address'                  => '402 High-Street Towers, Business District',
                    'city'                     => 'Mumbai',
                    'state'                    => 'Maharashtra',
                    'pincode'                  => '400050',
                    'occupation'               => 'Managing Director / Corporate Professional',
                    'preferred_contact_method' => 'Phone',
                    'notes'                    => 'Prefers phone communication between 4 PM and 7 PM.',
                    'created_at'               => $now,
                    'updated_at'               => $now,
                ]);

                // Initial Assignment log
                $db->table('lead_assignments')->insert([
                    'lead_id'         => $leadId,
                    'assigned_to'     => $ld['assigned_user_id'],
                    'assigned_by'     => $adminUserId,
                    'assignment_type' => 'Initial',
                    'remarks'         => 'System lead routing assignment',
                    'created_at'      => $now,
                ]);

                // Property Interest
                if ($ld['property_id']) {
                    $db->table('lead_property_interests')->insert([
                        'lead_id'          => $leadId,
                        'project_id'       => $ld['project_id'],
                        'property_id'      => $ld['property_id'],
                        'property_unit_id' => $ld['property_unit_id'],
                        'interest_level'   => 'Primary',
                        'remarks'          => 'Client shortlisted this property on priority.',
                        'created_at'       => $now,
                        'updated_at'       => $now,
                    ]);
                }

                // Formal Enquiry
                $db->table('enquiries')->insert([
                    'enquiry_code'            => sprintf('ENQ-2026-%06d', $leadId),
                    'lead_id'                 => $leadId,
                    'project_id'              => $ld['project_id'],
                    'property_id'             => $ld['property_id'],
                    'property_unit_id'        => $ld['property_unit_id'],
                    'enquiry_type'            => 'Purchase',
                    'requirement'             => $ld['remarks'],
                    'budget'                  => $ld['budget_max'],
                    'preferred_location'      => $ld['preferred_location'],
                    'preferred_property_type' => 'Apartment',
                    'status'                  => ($ld['lead_status'] === 'Lost' ? 'Closed' : 'In Progress'),
                    'remarks'                 => 'Initial requirement logged by sales reception.',
                    'created_by'              => $adminUserId,
                    'created_at'              => $now,
                    'updated_at'              => $now,
                ]);
            }
        }

        // 4. Seed Follow-ups for Lead 1 and Lead 2
        $lead1 = $db->table('leads')->where('lead_code', 'LEAD-2026-000001')->get()->getRowArray();
        $lead2 = $db->table('leads')->where('lead_code', 'LEAD-2026-000002')->get()->getRowArray();

        if ($lead1) {
            // Completed call
            $db->table('lead_followups')->insert([
                'lead_id'          => $lead1['id'],
                'assigned_to'      => $salesUserId,
                'followup_type'    => 'Phone Call',
                'scheduled_at'     => date('Y-m-d 10:00:00', strtotime('-2 days')),
                'completed_at'     => date('Y-m-d 10:25:00', strtotime('-2 days')),
                'status'           => 'Completed',
                'outcome'          => 'Client confirmed site visit interest',
                'notes'            => 'Detailed conversation about project amenities, floor plan, and parking.',
                'next_followup_at' => date('Y-m-d 11:00:00', strtotime('+1 day')),
                'created_by'       => $adminUserId,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);

            // Today's pending follow-up
            $db->table('lead_followups')->insert([
                'lead_id'          => $lead1['id'],
                'assigned_to'      => $salesUserId,
                'followup_type'    => 'Meeting',
                'scheduled_at'     => date('Y-m-d 16:30:00'),
                'completed_at'     => null,
                'status'           => 'Pending',
                'outcome'          => null,
                'notes'            => 'Confirm weekend site visit logistics and gate pass with security.',
                'next_followup_at' => null,
                'created_by'       => $salesUserId,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);

            // Site Visit for Lead 1
            $db->table('site_visits')->insert([
                'visit_code'        => 'SV-2026-000001',
                'lead_id'           => $lead1['id'],
                'project_id'        => $proj1Id,
                'property_id'       => $prop1Id,
                'property_unit_id'  => null,
                'assigned_user_id'  => $salesUserId,
                'scheduled_at'      => date('Y-m-d 11:00:00', strtotime('+2 days')),
                'visit_type'        => 'Property Visit',
                'status'            => 'Confirmed',
                'visitor_count'     => 2,
                'remarks'           => 'Client visiting with architect and spouse.',
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);
        }

        if ($lead2) {
            // Overdue follow-up for Lead 2
            $db->table('lead_followups')->insert([
                'lead_id'          => $lead2['id'],
                'assigned_to'      => $salesUserId,
                'followup_type'    => 'WhatsApp',
                'scheduled_at'     => date('Y-m-d 14:00:00', strtotime('-1 day')),
                'completed_at'     => null,
                'status'           => 'Pending',
                'outcome'          => null,
                'notes'            => 'Share revised payment plan schedule approved by finance team.',
                'next_followup_at' => null,
                'created_by'       => $salesUserId,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
        }

        // 5. Seed Unit Hold for Lead 4 (Dr. Meera Nambiar on Unit A-102)
        $lead4 = $db->table('leads')->where('lead_code', 'LEAD-2026-000004')->get()->getRowArray();
        if ($lead4 && $unit2Id) {
            $existingHold = $db->table('unit_holds')->where('hold_code', 'HOLD-2026-000001')->get()->getRowArray();
            if (!$existingHold) {
                $holdStart = date('Y-m-d H:i:s');
                $holdExpiry = date('Y-m-d H:i:s', strtotime('+48 hours'));

                $db->table('unit_holds')->insert([
                    'hold_code'        => 'HOLD-2026-000001',
                    'lead_id'          => $lead4['id'],
                    'property_id'      => $prop1Id,
                    'property_unit_id' => $unit2Id,
                    'held_by'          => $adminUserId,
                    'hold_status'      => 'Active',
                    'hold_reason'      => 'Token deposit discussion and KYC document review',
                    'started_at'       => $holdStart,
                    'expires_at'       => $holdExpiry,
                    'released_at'      => null,
                    'remarks'          => 'Unit held for 48 hours to complete formal booking paperwork.',
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]);

                // Update unit status to Reserved
                $db->table('property_units')->where('id', $unit2Id)->update([
                    'availability_status' => 'Reserved',
                    'updated_at'          => $now,
                ]);

                // Record status history
                $db->table('property_status_history')->insert([
                    'property_id' => $prop1Id,
                    'unit_id'     => $unit2Id,
                    'old_status'  => 'Available',
                    'new_status'  => 'Reserved',
                    'changed_by'  => $adminUserId,
                    'remarks'     => 'Temporary reservation hold HOLD-2026-000001 created for Lead LEAD-2026-000004.',
                    'created_at'  => $now,
                ]);

                // Audit log
                $db->table('audit_logs')->insert([
                    'user_id'     => $adminUserId,
                    'action'      => 'UNIT_HOLD_CREATED',
                    'module'      => 'UnitHolds',
                    'record_id'   => 1,
                    'ip_address'  => '127.0.0.1',
                    'user_agent'  => 'System Seeder',
                    'description' => "Unit Hold HOLD-2026-000001 placed on Unit A-102 for Dr. Meera Nambiar",
                    'created_at'  => $now,
                ]);
            }
        }
    }
}
