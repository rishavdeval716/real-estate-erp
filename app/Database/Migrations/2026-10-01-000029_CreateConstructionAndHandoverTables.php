<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateConstructionAndHandoverTables extends Migration
{
    public function up()
    {
        // 1. Construction Milestones Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'milestone_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'project_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tower_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'milestone_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'stage_order' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
            ],
            'weightage_percentage' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
            ],
            'target_start_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'target_completion_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'actual_completion_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'progress_percentage' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Not Started', 'In Progress', 'Under Review', 'Completed', 'Delayed'],
                'default'    => 'Not Started',
            ],
            'verified_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'verified_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'remarks' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('project_id');
        $this->forge->addKey('tower_id');
        $this->forge->createTable('construction_milestones', true);

        // 2. Daily Site Logs Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'log_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'log_date' => [
                'type' => 'DATE',
            ],
            'project_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tower_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'milestone_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'skilled_workers' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'unskilled_workers' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'weather_condition' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'Sunny',
            ],
            'work_completed' => [
                'type' => 'TEXT',
            ],
            'materials_used' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'equipment_deployed' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'delays_or_impediments' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'logged_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'approved_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Draft', 'Submitted', 'Approved'],
                'default'    => 'Submitted',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('project_id');
        $this->forge->addKey('log_date');
        $this->forge->createTable('daily_site_logs', true);

        // 3. Contractors Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'contractor_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'company_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'specialization' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'contact_person' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
            ],
            'license_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'gstin' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'rating' => [
                'type'       => 'DECIMAL',
                'constraint' => '3,1',
                'default'    => 5.0,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Active', 'Inactive', 'Blacklisted'],
                'default'    => 'Active',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('status');
        $this->forge->createTable('contractors', true);

        // 4. Construction Work Orders Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'work_order_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'contractor_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'project_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tower_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'milestone_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'scope_of_work' => [
                'type' => 'TEXT',
            ],
            'contract_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'retention_percentage' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 5.00,
            ],
            'start_date' => [
                'type' => 'DATE',
            ],
            'completion_date' => [
                'type' => 'DATE',
            ],
            'payment_terms' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Draft', 'Awarded', 'In Progress', 'Under Inspection', 'Completed', 'Terminated'],
                'default'    => 'Awarded',
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('contractor_id');
        $this->forge->addKey('project_id');
        $this->forge->createTable('construction_work_orders', true);

        // 5. Material Requisitions Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'requisition_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'project_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tower_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'item_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'category' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'quantity' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'unit_of_measure' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'estimated_unit_cost' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'estimated_total_cost' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'required_by_date' => [
                'type' => 'DATE',
            ],
            'priority' => [
                'type'       => 'ENUM',
                'constraint' => ['Low', 'Medium', 'High', 'Urgent'],
                'default'    => 'Medium',
            ],
            'requested_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Requested', 'Approved', 'Procured', 'Rejected'],
                'default'    => 'Requested',
            ],
            'remarks' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('project_id');
        $this->forge->addKey('status');
        $this->forge->createTable('material_requisitions', true);

        // 6. Site Inspections Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'inspection_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'project_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tower_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'unit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'inspection_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'inspection_date' => [
                'type' => 'DATE',
            ],
            'inspector_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'result' => [
                'type'       => 'ENUM',
                'constraint' => ['Passed', 'Failed', 'Conditional Pass'],
                'default'    => 'Passed',
            ],
            'snags_found' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'snag_details' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'rectification_deadline' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'remarks' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Scheduled', 'Completed', 'Action Required', 'Rectified & Closed'],
                'default'    => 'Completed',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('project_id');
        $this->forge->createTable('site_inspections', true);

        // 7. Handover Certificates Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'certificate_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'booking_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'customer_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'property_unit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'handover_date' => [
                'type' => 'DATE',
            ],
            'financial_clearance' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'snagging_clearance' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'occupancy_certificate_ref' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'electricity_meter_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'initial_electricity_reading' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'water_meter_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'initial_water_reading' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'key_sets_provided' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 2,
            ],
            'customer_acknowledged' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'authorized_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Draft', 'Ready for Handover', 'Handed Over', 'Archived'],
                'default'    => 'Handed Over',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('booking_id');
        $this->forge->addKey('customer_id');
        $this->forge->addKey('property_unit_id');
        $this->forge->createTable('handover_certificates', true);
    }

    public function down()
    {
        $this->forge->dropTable('handover_certificates', true);
        $this->forge->dropTable('site_inspections', true);
        $this->forge->dropTable('material_requisitions', true);
        $this->forge->dropTable('construction_work_orders', true);
        $this->forge->dropTable('contractors', true);
        $this->forge->dropTable('daily_site_logs', true);
        $this->forge->dropTable('construction_milestones', true);
    }
}
