<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Phase6ConstructionSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        // 1. Seed Contractors
        $contractors = [
            [
                'contractor_code' => 'CON-2026-000001',
                'company_name'    => 'Apex Structural Infrastructure Ltd.',
                'specialization'  => 'Civil & Structural',
                'contact_person'  => 'Rajeshwar Sharma',
                'phone'           => '+91 98210 11223',
                'email'           => 'contact@apexstructural.local',
                'license_number'  => 'LIC-CIV-MH-2021-998',
                'gstin'           => '27AAACA1234A1Z5',
                'rating'          => 4.8,
                'status'          => 'Active',
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                'contractor_code' => 'CON-2026-000002',
                'company_name'    => 'Siemens Power & MEP Solutions',
                'specialization'  => 'Electrical',
                'contact_person'  => 'Arun Gopalan',
                'phone'           => '+91 98210 44556',
                'email'           => 'mep@siemenspower.local',
                'license_number'  => 'LIC-ELE-MH-2020-412',
                'gstin'           => '27BBBCB5678B1Z6',
                'rating'          => 4.9,
                'status'          => 'Active',
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                'contractor_code' => 'CON-2026-000003',
                'company_name'    => 'Supreme Flow Plumbing & Fire Services',
                'specialization'  => 'Plumbing',
                'contact_person'  => 'Dinesh Panchal',
                'phone'           => '+91 98210 77889',
                'email'           => 'services@supremeflow.local',
                'license_number'  => 'LIC-PLB-MH-2022-105',
                'gstin'           => '27CCCC59012C1Z7',
                'rating'          => 4.6,
                'status'          => 'Active',
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                'contractor_code' => 'CON-2026-000004',
                'company_name'    => 'Asian Finishes & Architectural Paints',
                'specialization'  => 'Finishing & Painting',
                'contact_person'  => 'Kavita Deshmukh',
                'phone'           => '+91 98210 88990',
                'email'           => 'projects@asianfinishes.local',
                'license_number'  => 'LIC-FNH-MH-2023-334',
                'gstin'           => '27DDDDD3456D1Z8',
                'rating'          => 4.7,
                'status'          => 'Active',
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
        ];

        foreach ($contractors as $con) {
            $exists = $db->table('contractors')->where('contractor_code', $con['contractor_code'])->get()->getRow();
            if (!$exists) {
                $db->table('contractors')->insert($con);
            }
        }

        // 2. Get first active project and tower
        $project = $db->table('projects')->get()->getFirstRow('array');
        $tower = $db->table('project_towers')->get()->getFirstRow('array');
        $projectId = $project ? $project['id'] : 1;
        $towerId = $tower ? $tower['id'] : 1;

        // 3. Seed Construction Milestones for Project 1
        $milestones = [
            [
                'milestone_code'         => 'MIL-2026-000001',
                'project_id'             => $projectId,
                'tower_id'               => $towerId,
                'milestone_name'         => 'Piling, Deep Excavation & Foundation Works',
                'stage_order'            => 1,
                'weightage_percentage'   => 15.00,
                'target_start_date'      => '2025-01-10',
                'target_completion_date' => '2025-04-30',
                'actual_completion_date' => '2025-04-25',
                'progress_percentage'    => 100.00,
                'status'                 => 'Completed',
                'verified_by'            => 1,
                'verified_at'            => '2025-04-26 14:00:00',
                'remarks'                => 'Piling certified by structural auditor. Raft foundation slab cast.',
                'created_at'             => $now,
                'updated_at'             => $now,
            ],
            [
                'milestone_code'         => 'MIL-2026-000002',
                'project_id'             => $projectId,
                'tower_id'               => $towerId,
                'milestone_name'         => 'Plinth, Podium & Basement Parking Structure',
                'stage_order'            => 2,
                'weightage_percentage'   => 15.00,
                'target_start_date'      => '2025-05-01',
                'target_completion_date' => '2025-08-31',
                'actual_completion_date' => '2025-08-28',
                'progress_percentage'    => 100.00,
                'status'                 => 'Completed',
                'verified_by'            => 1,
                'verified_at'            => '2025-08-29 11:30:00',
                'remarks'                => 'Multi-level basement retention and ramp columns completed.',
                'created_at'             => $now,
                'updated_at'             => $now,
            ],
            [
                'milestone_code'         => 'MIL-2026-000003',
                'project_id'             => $projectId,
                'tower_id'               => $towerId,
                'milestone_name'         => 'Tower RCC Superstructure Frame (Floors 1-14)',
                'stage_order'            => 3,
                'weightage_percentage'   => 20.00,
                'target_start_date'      => '2025-09-01',
                'target_completion_date' => '2026-04-30',
                'actual_completion_date' => null,
                'progress_percentage'    => 80.00,
                'status'                 => 'In Progress',
                'verified_by'            => null,
                'verified_at'            => null,
                'remarks'                => '11th floor slab de-shuttered. 12th floor column rebar in place.',
                'created_at'             => $now,
                'updated_at'             => $now,
            ],
            [
                'milestone_code'         => 'MIL-2026-000004',
                'project_id'             => $projectId,
                'tower_id'               => $towerId,
                'milestone_name'         => 'External/Internal AAC Masonry & Sand-Face Plaster',
                'stage_order'            => 4,
                'weightage_percentage'   => 15.00,
                'target_start_date'      => '2026-01-15',
                'target_completion_date' => '2026-07-31',
                'actual_completion_date' => null,
                'progress_percentage'    => 45.00,
                'status'                 => 'In Progress',
                'verified_by'            => null,
                'verified_at'            => null,
                'remarks'                => 'Floors 1 to 6 brickwork complete. Gypsum plaster initiated on floors 1-3.',
                'created_at'             => $now,
                'updated_at'             => $now,
            ],
            [
                'milestone_code'         => 'MIL-2026-000005',
                'project_id'             => $projectId,
                'tower_id'               => $towerId,
                'milestone_name'         => 'MEP Conduit, Fire Safety Sprinklers & Vertical Wet Risers',
                'stage_order'            => 5,
                'weightage_percentage'   => 15.00,
                'target_start_date'      => '2026-03-01',
                'target_completion_date' => '2026-09-30',
                'actual_completion_date' => null,
                'progress_percentage'    => 35.00,
                'status'                 => 'In Progress',
                'verified_by'            => null,
                'verified_at'            => null,
                'remarks'                => 'Conduit drop verification completed for lower 5 tiers.',
                'created_at'             => $now,
                'updated_at'             => $now,
            ],
            [
                'milestone_code'         => 'MIL-2026-000006',
                'project_id'             => $projectId,
                'tower_id'               => $towerId,
                'milestone_name'         => 'Flooring, Anodized Glazing, Elevators & Finishing',
                'stage_order'            => 6,
                'weightage_percentage'   => 10.00,
                'target_start_date'      => '2026-08-01',
                'target_completion_date' => '2026-11-30',
                'actual_completion_date' => null,
                'progress_percentage'    => 0.00,
                'status'                 => 'Not Started',
                'verified_by'            => null,
                'verified_at'            => null,
                'remarks'                => 'Sample apartment finishes approved by architect.',
                'created_at'             => $now,
                'updated_at'             => $now,
            ],
            [
                'milestone_code'         => 'MIL-2026-000007',
                'project_id'             => $projectId,
                'tower_id'               => $towerId,
                'milestone_name'         => 'Fire NOC, Occupancy Certificate (OC) & Possession Handover',
                'stage_order'            => 7,
                'weightage_percentage'   => 10.00,
                'target_start_date'      => '2026-11-01',
                'target_completion_date' => '2027-01-31',
                'actual_completion_date' => null,
                'progress_percentage'    => 0.00,
                'status'                 => 'Not Started',
                'verified_by'            => null,
                'verified_at'            => null,
                'remarks'                => 'Scheduled for municipal submission upon completion.',
                'created_at'             => $now,
                'updated_at'             => $now,
            ],
        ];

        foreach ($milestones as $ms) {
            $exists = $db->table('construction_milestones')->where('milestone_code', $ms['milestone_code'])->get()->getRow();
            if (!$exists) {
                $db->table('construction_milestones')->insert($ms);
            }
        }

        // 4. Seed Daily Site Log
        $firstMilestone = $db->table('construction_milestones')->where('milestone_code', 'MIL-2026-000003')->get()->getRow();
        $milestoneId = $firstMilestone ? $firstMilestone->id : 1;

        $logExists = $db->table('daily_site_logs')->where('log_code', 'LOG-2026-000001')->get()->getRow();
        if (!$logExists) {
            $db->table('daily_site_logs')->insert([
                'log_code'              => 'LOG-2026-000001',
                'log_date'              => date('Y-m-d'),
                'project_id'            => $projectId,
                'tower_id'              => $towerId,
                'milestone_id'          => $milestoneId,
                'skilled_workers'       => 28,
                'unskilled_workers'     => 45,
                'weather_condition'     => 'Sunny',
                'work_completed'        => 'Completed shuttering and steel binding for 12th floor column grid C1-C14. Poured 45 cubic meters ready-mix concrete for central service core.',
                'materials_used'        => 'RMC M35 Grade: 45 cu.m, Fe500D TMT Steel 16mm: 3.8 Metric Tons, Binding Wire: 60 kg.',
                'equipment_deployed'    => 'Tower Crane #1, Concrete Boom Placer (36m), Vibrator needles (4 units).',
                'delays_or_impediments' => 'None. Work proceeded as scheduled with zero safety incidents.',
                'logged_by'             => 1,
                'approved_by'           => 1,
                'status'                => 'Approved',
                'created_at'            => $now,
                'updated_at'            => $now,
            ]);
        }

        // 5. Seed Construction Work Order
        $contractor = $db->table('contractors')->where('contractor_code', 'CON-2026-000001')->get()->getRow();
        $cwoExists = $db->table('construction_work_orders')->where('work_order_code', 'CWO-2026-000001')->get()->getRow();
        if (!$cwoExists && $contractor) {
            $db->table('construction_work_orders')->insert([
                'work_order_code'      => 'CWO-2026-000001',
                'contractor_id'        => $contractor->id,
                'project_id'           => $projectId,
                'tower_id'             => $towerId,
                'milestone_id'         => $milestoneId,
                'title'                => 'RCC Framing and Superstructure Package - Tower A',
                'scope_of_work'        => 'Complete structural RCC casting, column shuttering, slab reinforcement, de-shuttering and curing from Ground floor through 14th floor terrace level according to approved structural consultant drawings.',
                'contract_amount'      => 45000000.00,
                'retention_percentage' => 5.00,
                'start_date'           => '2025-09-01',
                'completion_date'      => '2026-04-30',
                'payment_terms'        => 'Running Account (RA) bills verified bi-weekly based on actual cube test compressive strength certifications. 5% retention released upon DLP expiry.',
                'status'               => 'In Progress',
                'created_by'           => 1,
                'created_at'           => $now,
                'updated_at'           => $now,
            ]);
        }

        // 6. Seed Material Requisitions
        $req1 = $db->table('material_requisitions')->where('requisition_code', 'REQ-2026-000001')->get()->getRow();
        if (!$req1) {
            $db->table('material_requisitions')->insert([
                'requisition_code'     => 'REQ-2026-000001',
                'project_id'           => $projectId,
                'tower_id'             => $towerId,
                'item_name'            => 'Ultratech 53-Grade OPC Cement (50kg)',
                'category'             => 'Cement',
                'quantity'             => 1200.00,
                'unit_of_measure'      => 'Bags',
                'estimated_unit_cost'  => 385.00,
                'estimated_total_cost' => 462000.00,
                'required_by_date'     => date('Y-m-d', strtotime('+7 days')),
                'priority'             => 'High',
                'requested_by'         => 1,
                'status'               => 'Approved',
                'remarks'              => 'Required for upcoming 13th floor slab casting.',
                'created_at'           => $now,
                'updated_at'           => $now,
            ]);
        }

        $req2 = $db->table('material_requisitions')->where('requisition_code', 'REQ-2026-000002')->get()->getRow();
        if (!$req2) {
            $db->table('material_requisitions')->insert([
                'requisition_code'     => 'REQ-2026-000002',
                'project_id'           => $projectId,
                'tower_id'             => $towerId,
                'item_name'            => 'Tata Tiscon Fe500D TMT Rebar (16mm)',
                'category'             => 'Steel',
                'quantity'             => 25.00,
                'unit_of_measure'      => 'Metric Tons',
                'estimated_unit_cost'  => 62000.00,
                'estimated_total_cost' => 1550000.00,
                'required_by_date'     => date('Y-m-d', strtotime('+12 days')),
                'priority'             => 'Urgent',
                'requested_by'         => 1,
                'status'               => 'Requested',
                'remarks'              => 'Batch test mill test certificate (MTC) required with delivery.',
                'created_at'           => $now,
                'updated_at'           => $now,
            ]);
        }

        // 7. Seed Site Inspection
        $inspExists = $db->table('site_inspections')->where('inspection_code', 'INSP-2026-000001')->get()->getRow();
        if (!$inspExists) {
            $db->table('site_inspections')->insert([
                'inspection_code'        => 'INSP-2026-000001',
                'project_id'             => $projectId,
                'tower_id'               => $towerId,
                'unit_id'                => null,
                'inspection_type'        => 'Structural Integrity',
                'inspection_date'        => date('Y-m-d', strtotime('-2 days')),
                'inspector_id'           => 1,
                'result'                 => 'Passed',
                'snags_found'            => 0,
                'snag_details'           => 'Reinforcement lap lengths, cover blocks (25mm), and beam junctions inspected before concrete pour. All parameters comply with IS 456:2000.',
                'rectification_deadline' => null,
                'remarks'                => 'Structural consultant signed off on quality checklist.',
                'status'                 => 'Completed',
                'created_at'             => $now,
                'updated_at'             => $now,
            ]);
        }

        // 8. Seed Handover Certificate (for an existing booking / customer / unit)
        $booking = $db->table('bookings')->where('booking_status', 'Confirmed')->get()->getFirstRow('array');
        if ($booking) {
            $handoverExists = $db->table('handover_certificates')->where('certificate_number', 'HND-2026-000001')->get()->getRow();
            if (!$handoverExists) {
                $db->table('handover_certificates')->insert([
                    'certificate_number'          => 'HND-2026-000001',
                    'booking_id'                  => $booking['id'],
                    'customer_id'                 => $booking['customer_id'],
                    'property_unit_id'            => $booking['property_unit_id'],
                    'handover_date'               => date('Y-m-d'),
                    'financial_clearance'         => 1,
                    'snagging_clearance'          => 1,
                    'occupancy_certificate_ref'   => 'MCGM/BP/OC-2026/0491',
                    'electricity_meter_number'    => 'MSEDCL-LT-889021',
                    'initial_electricity_reading' => 12.50,
                    'water_meter_number'          => 'MCGM-WM-44120',
                    'initial_water_reading'       => 4.00,
                    'key_sets_provided'           => 3,
                    'customer_acknowledged'       => 1,
                    'authorized_by'               => 1,
                    'status'                      => 'Handed Over',
                    'notes'                       => 'Unit inspected with buyer. All final financial dues settled. 3 complete sets of brass keys handed over along with warranty cards for electrical fittings.',
                    'created_at'                  => $now,
                    'updated_at'                  => $now,
                ]);
            }
        }
    }
}
