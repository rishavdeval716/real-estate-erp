<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Phase5OperationsSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        echo "Seeding Phase 5 Operational Data...\n";

        // 1. SLA Rules
        $slaRules = [
            ['category' => 'electrical',     'priority' => 'urgent', 'response_time_hours' => 1, 'resolution_time_hours' => 4,  'status' => 'active'],
            ['category' => 'electrical',     'priority' => 'high',   'response_time_hours' => 2, 'resolution_time_hours' => 8,  'status' => 'active'],
            ['category' => 'electrical',     'priority' => 'medium', 'response_time_hours' => 4, 'resolution_time_hours' => 24, 'status' => 'active'],
            ['category' => 'electrical',     'priority' => 'low',    'response_time_hours' => 8, 'resolution_time_hours' => 48, 'status' => 'active'],

            ['category' => 'plumbing',       'priority' => 'urgent', 'response_time_hours' => 1, 'resolution_time_hours' => 4,  'status' => 'active'],
            ['category' => 'plumbing',       'priority' => 'high',   'response_time_hours' => 2, 'resolution_time_hours' => 8,  'status' => 'active'],
            ['category' => 'plumbing',       'priority' => 'medium', 'response_time_hours' => 4, 'resolution_time_hours' => 24, 'status' => 'active'],

            ['category' => 'hvac',           'priority' => 'high',   'response_time_hours' => 2, 'resolution_time_hours' => 12, 'status' => 'active'],
            ['category' => 'hvac',           'priority' => 'medium', 'response_time_hours' => 4, 'resolution_time_hours' => 24, 'status' => 'active'],

            ['category' => 'elevators',      'priority' => 'urgent', 'response_time_hours' => 1, 'resolution_time_hours' => 3,  'status' => 'active'],
            ['category' => 'fire_safety',    'priority' => 'urgent', 'response_time_hours' => 1, 'resolution_time_hours' => 2,  'status' => 'active'],
            ['category' => 'other',          'priority' => 'medium', 'response_time_hours' => 6, 'resolution_time_hours' => 36, 'status' => 'active'],
        ];

        foreach ($slaRules as $rule) {
            $exists = $db->table('sla_rules')->where('category', $rule['category'])->where('priority', $rule['priority'])->get()->getRowArray();
            if (!$exists) {
                $rule['created_at'] = date('Y-m-d H:i:s');
                $rule['updated_at'] = date('Y-m-d H:i:s');
                $db->table('sla_rules')->insert($rule);
            }
        }

        // 2. Technicians
        $technicians = [
            [
                'technician_code' => 'TECH-2026-000001',
                'name'            => 'Rajesh Kumar',
                'mobile'          => '9876501111',
                'email'           => 'rajesh.kumar@realestate-erp.local',
                'skill'           => 'Licensed Master Electrician',
                'department'      => 'Electrical & Power Systems',
                'availability'    => 'available',
                'status'          => 'active',
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'technician_code' => 'TECH-2026-000002',
                'name'            => 'Amit Verma',
                'mobile'          => '9876502222',
                'email'           => 'amit.verma@realestate-erp.local',
                'skill'           => 'HVAC & Central Chiller Specialist',
                'department'      => 'Climate Control & Mechanical',
                'availability'    => 'busy',
                'status'          => 'active',
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'technician_code' => 'TECH-2026-000003',
                'name'            => 'Suresh Patil',
                'mobile'          => '9876503333',
                'email'           => 'suresh.patil@realestate-erp.local',
                'skill'           => 'Water Treatment & Piping Engineer',
                'department'      => 'Sanitation & Plumbing',
                'availability'    => 'available',
                'status'          => 'active',
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'technician_code' => 'TECH-2026-000004',
                'name'            => 'Dinesh Sharma',
                'mobile'          => '9876504444',
                'email'           => 'dinesh.sharma@realestate-erp.local',
                'skill'           => 'Elevator Automation & VFD Drives',
                'department'      => 'Vertical Transportation',
                'availability'    => 'available',
                'status'          => 'active',
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($technicians as $tech) {
            $exists = $db->table('technicians')->where('technician_code', $tech['technician_code'])->get()->getRowArray();
            if (!$exists) {
                $db->table('technicians')->insert($tech);
            }
        }

        // Get properties
        $properties = $db->table('properties')->get()->getResultArray();
        $prop1Id = $properties[0]['id'] ?? 1;
        $prop2Id = $properties[1]['id'] ?? 1;

        // 3. Facility Assets
        $assets = [
            [
                'asset_code'        => 'AST-2026-000001',
                'name'              => 'Passenger Elevator Unit A (13 Passengers)',
                'category'          => 'elevators',
                'property_id'       => $prop1Id,
                'location_details'  => 'Tower 1, Core Lift Lobby',
                'installation_date' => '2024-03-15',
                'warranty_expiry'   => '2027-03-14',
                'status'            => 'operational',
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ],
            [
                'asset_code'        => 'AST-2026-000002',
                'name'              => 'Emergency Diesel Generator (250 kVA Cummins)',
                'category'          => 'generators',
                'property_id'       => $prop1Id,
                'location_details'  => 'Basement 1, Power Substation Room',
                'installation_date' => '2023-11-10',
                'warranty_expiry'   => '2026-11-09',
                'status'            => 'operational',
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ],
            [
                'asset_code'        => 'AST-2026-000003',
                'name'              => 'Automated Fire Sprinkler & Alarm System',
                'category'          => 'fire_safety',
                'property_id'       => $prop1Id,
                'location_details'  => 'Floors 1-20 & Basements',
                'installation_date' => '2024-01-20',
                'warranty_expiry'   => '2028-01-19',
                'status'            => 'operational',
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ],
            [
                'asset_code'        => 'AST-2026-000004',
                'name'              => 'Central Rooftop VRF Air Conditioner Plant',
                'category'          => 'hvac',
                'property_id'       => $prop2Id,
                'location_details'  => 'Rooftop Utility Deck',
                'installation_date' => '2023-08-01',
                'warranty_expiry'   => '2026-07-31',
                'status'            => 'under_maintenance',
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($assets as $asset) {
            $exists = $db->table('facility_assets')->where('asset_code', $asset['asset_code'])->get()->getRowArray();
            if (!$exists) {
                $db->table('facility_assets')->insert($asset);
            }
        }

        // Get Available Unit
        $units = $db->table('property_units')->where('availability_status', 'Available')->get()->getResultArray();
        $unit1Id = $units[0]['id'] ?? null;
        $unit2Id = $units[1]['id'] ?? null;

        // 4. Tenants
        $tenants = [
            [
                'tenant_code'          => 'TEN-2026-000001',
                'tenant_type'          => 'individual',
                'full_name'            => 'Vikramaditya Birla',
                'company_name'         => null,
                'contact_person'       => null,
                'mobile'               => '9820011223',
                'email'                => 'vikramaditya.birla@example.com',
                'address'              => 'Flat 401, Tower A, Grand Horizon',
                'city'                 => 'Mumbai',
                'state'                => 'Maharashtra',
                'pincode'              => '400050',
                'id_proof_type'        => 'PAN Card',
                'id_proof_number'      => 'ABCDE1234F',
                'kyc_status'           => 'verified',
                'occupied_property_id' => $prop1Id,
                'occupied_unit_id'     => $unit1Id,
                'user_id'              => 1,
                'lease_start_date'     => '2026-01-01',
                'lease_end_date'       => '2026-12-31',
                'status'               => 'active',
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            [
                'tenant_code'          => 'TEN-2026-000002',
                'tenant_type'          => 'company',
                'full_name'            => 'Nexus Tech Solutions Pvt. Ltd.',
                'company_name'         => 'Nexus Tech Solutions Pvt. Ltd.',
                'contact_person'       => 'Rohan Deshmukh (Head of Admin)',
                'mobile'               => '9820099887',
                'email'                => 'admin@nexustech.example.com',
                'address'              => 'Level 5, Platinum Business Tower',
                'city'                 => 'Pune',
                'state'                => 'Maharashtra',
                'pincode'              => '411006',
                'id_proof_type'        => 'Certificate of Incorporation',
                'id_proof_number'      => 'U72200PN2020PTC123456',
                'kyc_status'           => 'verified',
                'occupied_property_id' => $prop2Id,
                'occupied_unit_id'     => $unit2Id,
                'user_id'              => 1,
                'lease_start_date'     => '2026-04-01',
                'lease_end_date'       => '2029-03-31',
                'status'               => 'active',
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            [
                'tenant_code'          => 'TEN-2026-000003',
                'tenant_type'          => 'individual',
                'full_name'            => 'Dr. Ananya Sen',
                'company_name'         => null,
                'contact_person'       => null,
                'mobile'               => '9811122334',
                'email'                => 'ananya.sen@example.com',
                'address'              => 'Apartment 102, Garden Heights',
                'city'                 => 'Bengaluru',
                'state'                => 'Karnataka',
                'pincode'              => '560001',
                'id_proof_type'        => 'Passport',
                'id_proof_number'      => 'Z1234567',
                'kyc_status'           => 'pending',
                'occupied_property_id' => null,
                'occupied_unit_id'     => null,
                'user_id'              => 1,
                'lease_start_date'     => null,
                'lease_end_date'       => null,
                'status'               => 'active',
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($tenants as $ten) {
            $exists = $db->table('tenants')->where('tenant_code', $ten['tenant_code'])->get()->getRowArray();
            if (!$exists) {
                $db->table('tenants')->insert($ten);
            }
        }

        $tenant1 = $db->table('tenants')->where('tenant_code', 'TEN-2026-000001')->get()->getRowArray();
        $tenant2 = $db->table('tenants')->where('tenant_code', 'TEN-2026-000002')->get()->getRowArray();

        // 5. Tenant Documents
        if ($tenant1) {
            $doc1 = [
                'tenant_id'           => $tenant1['id'],
                'document_type'       => 'PAN Card',
                'document_number'     => 'ABCDE1234F',
                'file_name'           => 'pan_vikramaditya.pdf',
                'file_path'           => 'kyc/tenants/demo_pan.pdf',
                'verification_status' => 'verified',
                'verified_by'         => 1,
                'verification_date'   => date('Y-m-d H:i:s'),
                'created_at'          => date('Y-m-d H:i:s'),
                'updated_at'          => date('Y-m-d H:i:s'),
            ];
            $existsDoc = $db->table('tenant_documents')->where('tenant_id', $tenant1['id'])->get()->getRowArray();
            if (!$existsDoc) {
                $db->table('tenant_documents')->insert($doc1);
            }
        }

        // 6. Lease Agreements
        if ($tenant1) {
            $lease1 = [
                'agreement_number'      => 'LSE-2026-000001',
                'tenant_id'             => $tenant1['id'],
                'property_id'           => $prop1Id,
                'property_unit_id'      => $unit1Id,
                'agreement_type'        => 'residential',
                'start_date'            => '2026-01-01',
                'end_date'              => '2026-12-31',
                'lock_in_period_months' => 6,
                'notice_period_days'    => 30,
                'monthly_rent'          => 45000.00,
                'security_deposit'      => 90000.00,
                'maintenance_charges'   => 5000.00,
                'rent_escalation_pct'   => 5.00,
                'escalation_frequency'  => 'annual',
                'payment_due_day'       => 5,
                'late_fee_amount'       => 500.00,
                'terms_conditions'      => 'Standard residential tenancy agreement with 6 months lock-in and 1 month notice.',
                'status'                => 'active',
                'created_by'            => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ];
            $existsLease1 = $db->table('lease_agreements')->where('agreement_number', $lease1['agreement_number'])->get()->getRowArray();
            if (!$existsLease1) {
                $db->table('lease_agreements')->insert($lease1);
                $l1Id = $db->insertID();

                // If unit was assigned, mark Rented
                if ($unit1Id) {
                    $db->table('property_units')->where('id', $unit1Id)->update(['availability_status' => 'Rented']);
                }

                // Security deposit
                $db->table('security_deposits')->insert([
                    'deposit_number'    => 'DEP-2026-000001',
                    'tenant_id'         => $tenant1['id'],
                    'lease_id'          => $l1Id,
                    'amount'            => 90000.00,
                    'deposit_date'      => '2026-01-01',
                    'refundable_amount' => 90000.00,
                    'adjusted_amount'   => 0.00,
                    'refund_status'     => 'held',
                    'remarks'           => 'Security deposit received in full via Bank Transfer',
                    'created_at'        => date('Y-m-d H:i:s'),
                    'updated_at'        => date('Y-m-d H:i:s'),
                ]);

                // Rental history
                $db->table('rental_histories')->insert([
                    'tenant_id'        => $tenant1['id'],
                    'property_id'      => $prop1Id,
                    'property_unit_id' => $unit1Id,
                    'lease_id'         => $l1Id,
                    'previous_rent'    => null,
                    'current_rent'     => 45000.00,
                    'start_date'       => '2026-01-01',
                    'end_date'         => '2026-12-31',
                    'status'           => 'Active',
                    'notes'            => 'Initial tenancy commenced.',
                    'created_at'       => date('Y-m-d H:i:s'),
                ]);

                // Rent Demands
                $demand1 = [
                    'demand_number'    => 'RNT-2026-000001',
                    'tenant_id'        => $tenant1['id'],
                    'lease_id'         => $l1Id,
                    'property_id'      => $prop1Id,
                    'property_unit_id' => $unit1Id,
                    'billing_period'   => '2026-09',
                    'base_rent'        => 45000.00,
                    'maintenance'      => 5000.00,
                    'other_charges'    => 0.00,
                    'late_fee'         => 0.00,
                    'tax'              => 0.00,
                    'total_amount'     => 50000.00,
                    'paid_amount'      => 50000.00,
                    'balance_amount'   => 0.00,
                    'due_date'         => '2026-09-05',
                    'status'           => 'paid',
                    'generated_by'     => 1,
                    'created_at'       => '2026-09-01 08:00:00',
                    'updated_at'       => '2026-09-04 11:30:00',
                ];
                $db->table('rent_demands')->insert($demand1);
                $d1Id = $db->insertID();

                // Collection
                $db->table('rent_collections')->insert([
                    'collection_number'     => 'RCL-2026-000001',
                    'rent_demand_id'        => $d1Id,
                    'tenant_id'             => $tenant1['id'],
                    'lease_id'              => $l1Id,
                    'amount'                => 50000.00,
                    'payment_date'          => '2026-09-04',
                    'payment_method'        => 'NEFT',
                    'transaction_reference' => 'NEFT-AXIS-99881122',
                    'remarks'               => 'Cleared September rent & maintenance in full.',
                    'received_by'           => 1,
                    'created_at'            => '2026-09-04 11:30:00',
                ]);

                // Current month demand
                $demand2 = [
                    'demand_number'    => 'RNT-2026-000002',
                    'tenant_id'        => $tenant1['id'],
                    'lease_id'         => $l1Id,
                    'property_id'      => $prop1Id,
                    'property_unit_id' => $unit1Id,
                    'billing_period'   => '2026-10',
                    'base_rent'        => 45000.00,
                    'maintenance'      => 5000.00,
                    'other_charges'    => 0.00,
                    'late_fee'         => 0.00,
                    'tax'              => 0.00,
                    'total_amount'     => 50000.00,
                    'paid_amount'      => 0.00,
                    'balance_amount'   => 50000.00,
                    'due_date'         => '2026-10-05',
                    'status'           => 'unpaid',
                    'generated_by'     => 1,
                    'created_at'       => date('Y-m-d H:i:s'),
                    'updated_at'       => date('Y-m-d H:i:s'),
                ];
                $db->table('rent_demands')->insert($demand2);
            }
        }

        // Commercial Lease for Tenant 2
        if ($tenant2) {
            $lease2 = [
                'agreement_number'      => 'LSE-2026-000002',
                'tenant_id'             => $tenant2['id'],
                'property_id'           => $prop2Id,
                'property_unit_id'      => $unit2Id,
                'agreement_type'        => 'commercial',
                'start_date'            => '2026-04-01',
                'end_date'              => '2029-03-31',
                'lock_in_period_months' => 12,
                'notice_period_days'    => 60,
                'monthly_rent'          => 180000.00,
                'security_deposit'      => 540000.00,
                'maintenance_charges'   => 20000.00,
                'rent_escalation_pct'   => 7.50,
                'escalation_frequency'  => 'annual',
                'payment_due_day'       => 1,
                'late_fee_amount'       => 2000.00,
                'terms_conditions'      => 'Commercial lease with 36 months duration, 18% GST applicable.',
                'status'                => 'active',
                'created_by'            => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ];
            $existsLease2 = $db->table('lease_agreements')->where('agreement_number', $lease2['agreement_number'])->get()->getRowArray();
            if (!$existsLease2) {
                $db->table('lease_agreements')->insert($lease2);
                $l2Id = $db->insertID();

                if ($unit2Id) {
                    $db->table('property_units')->where('id', $unit2Id)->update(['availability_status' => 'Rented']);
                }

                $db->table('security_deposits')->insert([
                    'deposit_number'    => 'DEP-2026-000002',
                    'tenant_id'         => $tenant2['id'],
                    'lease_id'          => $l2Id,
                    'amount'            => 540000.00,
                    'deposit_date'      => '2026-04-01',
                    'refundable_amount' => 540000.00,
                    'adjusted_amount'   => 0.00,
                    'refund_status'     => 'held',
                    'remarks'           => 'Commercial deposit (3 months rent equivalent)',
                    'created_at'        => date('Y-m-d H:i:s'),
                    'updated_at'        => date('Y-m-d H:i:s'),
                ]);

                // Demand with 18% GST
                $commercialTax = round((180000 + 20000) * 0.18, 2);
                $commercialTotal = 180000 + 20000 + $commercialTax;

                $db->table('rent_demands')->insert([
                    'demand_number'    => 'RNT-2026-000003',
                    'tenant_id'        => $tenant2['id'],
                    'lease_id'         => $l2Id,
                    'property_id'      => $prop2Id,
                    'property_unit_id' => $unit2Id,
                    'billing_period'   => '2026-10',
                    'base_rent'        => 180000.00,
                    'maintenance'      => 20000.00,
                    'other_charges'    => 0.00,
                    'late_fee'         => 0.00,
                    'tax'              => $commercialTax,
                    'total_amount'     => $commercialTotal,
                    'paid_amount'      => 0.00,
                    'balance_amount'   => $commercialTotal,
                    'due_date'         => '2026-10-01',
                    'status'           => 'unpaid',
                    'generated_by'     => 1,
                    'created_at'       => date('Y-m-d H:i:s'),
                    'updated_at'       => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // 7. Maintenance Tickets
        $ticketExists = $db->table('maintenance_requests')->where('ticket_number', 'MR-2026-000001')->get()->getRowArray();
        if (!$ticketExists) {
            $db->table('maintenance_requests')->insert([
                'ticket_number'          => 'MR-2026-000001',
                'tenant_id'              => $tenant1['id'] ?? null,
                'property_id'            => $prop1Id,
                'property_unit_id'       => $unit1Id,
                'asset_id'               => 1,
                'category'               => 'electrical',
                'subcategory'            => 'Breaker Tripping',
                'priority'               => 'high',
                'description'            => 'Master circuit breaker in Unit 401 is tripping intermittently during peak load.',
                'created_date'           => date('Y-m-d H:i:s', strtotime('-4 hours')),
                'assigned_technician_id' => 1,
                'sla_due_date'           => date('Y-m-d H:i:s', strtotime('+4 hours')),
                'status'                 => 'in_progress',
                'created_by'             => 1,
                'created_at'             => date('Y-m-d H:i:s'),
                'updated_at'             => date('Y-m-d H:i:s'),
            ]);
        }

        // 8. Complaints
        $complaintExists = $db->table('complaints')->where('complaint_code', 'CMP-2026-000001')->get()->getRowArray();
        if (!$complaintExists) {
            $db->table('complaints')->insert([
                'complaint_code'    => 'CMP-2026-000001',
                'complaint_type'    => 'Noise Disturbance',
                'property_id'       => $prop1Id,
                'property_unit_id'  => $unit1Id,
                'tenant_id'         => $tenant1['id'] ?? null,
                'description'       => 'Ongoing late night construction noise from adjacent development site exceeding permitted hours.',
                'priority'          => 'medium',
                'assigned_user_id'  => 1,
                'status'            => 'in_review',
                'feedback_rating'   => null,
                'feedback_comments' => null,
                'created_at'        => date('Y-m-d H:i:s', strtotime('-1 day')),
                'updated_at'        => date('Y-m-d H:i:s'),
            ]);
        }

        // 9. Preventive Maintenance Schedules
        $pmExists = $db->table('preventive_maintenance')->where('schedule_code', 'PM-2026-000001')->get()->getRowArray();
        if (!$pmExists) {
            $db->table('preventive_maintenance')->insert([
                'schedule_code'          => 'PM-2026-000001',
                'asset_id'               => 1,
                'maintenance_type'       => 'Elevator Monthly Rope & Safety Inspection',
                'frequency'              => 'monthly',
                'last_service_date'      => date('Y-m-d', strtotime('-25 days')),
                'next_service_date'      => date('Y-m-d', strtotime('+5 days')),
                'assigned_technician_id' => 4,
                'status'                 => 'scheduled',
                'remarks'                => 'Check governor speed clamp, door sensors, and counterweight balance.',
                'created_at'             => date('Y-m-d H:i:s'),
                'updated_at'             => date('Y-m-d H:i:s'),
            ]);

            $db->table('preventive_maintenance')->insert([
                'schedule_code'          => 'PM-2026-000002',
                'asset_id'               => 2,
                'maintenance_type'       => 'Diesel Generator Oil & Filter Replacement',
                'frequency'              => 'quarterly',
                'last_service_date'      => date('Y-m-d', strtotime('-80 days')),
                'next_service_date'      => date('Y-m-d', strtotime('+10 days')),
                'assigned_technician_id' => 1,
                'status'                 => 'scheduled',
                'remarks'                => 'Replace 15W40 lube oil, fuel filter cartridge, and battery check.',
                'created_at'             => date('Y-m-d H:i:s'),
                'updated_at'             => date('Y-m-d H:i:s'),
            ]);
        }

        // 10. CAM Charges
        $camExists = $db->table('cam_charges')->where('cam_code', 'CAM-2026-000001')->get()->getRowArray();
        if (!$camExists) {
            $db->table('cam_charges')->insert([
                'cam_code'         => 'CAM-2026-000001',
                'property_id'      => $prop1Id,
                'property_unit_id' => $unit1Id,
                'tenant_id'        => $tenant1['id'] ?? null,
                'area_sqft'        => 1450.00,
                'billing_model'    => 'per_sqft',
                'rate'             => 4.50,
                'period'           => '2026-10',
                'amount'           => 6525.00,
                'tax'              => 1174.50,
                'total'            => 7699.50,
                'status'           => 'billed',
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);
        }

        // 11. TDS Entries & Form 16A
        $tdsExists = $db->table('tds_entries')->where('entry_code', 'TDS-2026-000001')->get()->getRowArray();
        if (!$tdsExists) {
            $db->table('tds_entries')->insert([
                'entry_code'            => 'TDS-2026-000001',
                'party_name'            => 'Apex Realty Advisory LLP',
                'pan_number'            => 'AAACA1234D',
                'section'               => '194H',
                'transaction_reference' => 'Booking #BK-2026-000001 Commission Payout',
                'commission_id'         => 1,
                'gross_amount'          => 942900.00,
                'tds_rate'              => 5.00,
                'tds_amount'            => 47145.00,
                'net_payable'           => 895755.00,
                'deduction_date'        => date('Y-m-d'),
                'financial_year'        => '2026-2027',
                'quarter'               => 'Q3',
                'status'                => 'certified',
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ]);

            $db->table('tds_certificates')->insert([
                'certificate_number' => 'CERT-2026-000001',
                'party_name'         => 'Apex Realty Advisory LLP',
                'pan_number'         => 'AAACA1234D',
                'gross_amount'       => 942900.00,
                'tds_amount'         => 47145.00,
                'financial_year'     => '2026-2027',
                'quarter'            => 'Q3',
                'issue_date'         => date('Y-m-d'),
                'created_at'         => date('Y-m-d H:i:s'),
            ]);
        }

        // 12. Portal Requests
        $portalExists = $db->table('portal_requests')->where('request_code', 'REQ-2026-000001')->get()->getRowArray();
        if (!$portalExists) {
            $db->table('portal_requests')->insert([
                'request_code' => 'REQ-2026-000001',
                'portal_type'  => 'customer',
                'user_id'      => 1,
                'request_type' => 'Bank NOC for Home Loan',
                'subject'      => 'Request for Tripartite NOC - HDFC Bank',
                'details'      => 'Kindly issue builder NOC for loan disbursement against Unit 401.',
                'status'       => 'in_progress',
                'admin_notes'  => 'Legal team reviewing draft NOC.',
                'created_at'   => date('Y-m-d H:i:s', strtotime('-2 days')),
                'updated_at'   => date('Y-m-d H:i:s'),
            ]);

            $db->table('portal_requests')->insert([
                'request_code' => 'REQ-2026-000002',
                'portal_type'  => 'tenant',
                'user_id'      => 1,
                'request_type' => 'Parking Permit Pass',
                'subject'      => 'Additional Car Parking RFID Sticker',
                'details'      => 'Requesting additional RFID sticker for secondary resident car.',
                'status'       => 'approved',
                'admin_notes'  => 'RFID sticker #P-401-B issued at security gate.',
                'created_at'   => date('Y-m-d H:i:s', strtotime('-5 days')),
                'updated_at'   => date('Y-m-d H:i:s'),
            ]);
        }

        echo "Phase 5 operational data seeded successfully.\n";
    }
}
