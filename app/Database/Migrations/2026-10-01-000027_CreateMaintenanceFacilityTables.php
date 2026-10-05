<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMaintenanceFacilityTables extends Migration
{
    public function up()
    {
        // 1. Facility Assets
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'asset_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'category' => [
                'type'       => 'ENUM',
                'constraint' => ['elevators', 'generators', 'fire_safety', 'water_treatment', 'electrical', 'hvac', 'other'],
                'default'    => 'other',
            ],
            'property_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'location_details' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'installation_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'warranty_expiry' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['operational', 'under_maintenance', 'decommissioned'],
                'default'    => 'operational',
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
        $this->forge->addForeignKey('property_id', 'properties', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('facility_assets');

        // 2. Technicians
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'technician_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'mobile' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'skill' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'department' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'availability' => [
                'type'       => 'ENUM',
                'constraint' => ['available', 'busy', 'on_leave'],
                'default'    => 'available',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
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
        $this->forge->createTable('technicians');

        // 3. SLA Rules
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'category' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'priority' => [
                'type'       => 'ENUM',
                'constraint' => ['low', 'medium', 'high', 'urgent'],
            ],
            'response_time_hours' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 4,
            ],
            'resolution_time_hours' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 24,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
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
        $this->forge->addKey(['category', 'priority']);
        $this->forge->createTable('sla_rules');

        // 4. Maintenance Requests / Work Orders
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'ticket_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'tenant_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'property_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'property_unit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'asset_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'category' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'subcategory' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'priority' => [
                'type'       => 'ENUM',
                'constraint' => ['low', 'medium', 'high', 'urgent'],
                'default'    => 'medium',
            ],
            'description' => [
                'type' => 'TEXT',
            ],
            'attachment' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'created_date' => [
                'type' => 'DATETIME',
            ],
            'assigned_technician_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'sla_due_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['open', 'assigned', 'in_progress', 'on_hold', 'resolved', 'closed', 'rejected'],
                'default'    => 'open',
            ],
            'resolution' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'closed_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
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
        $this->forge->addKey('priority');
        $this->forge->addForeignKey('tenant_id', 'tenants', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('property_id', 'properties', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('property_unit_id', 'property_units', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('asset_id', 'facility_assets', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('assigned_technician_id', 'technicians', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('maintenance_requests');

        // 5. Resident / Tenant Complaints
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'complaint_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'complaint_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'property_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'property_unit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'tenant_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'description' => [
                'type' => 'TEXT',
            ],
            'priority' => [
                'type'       => 'ENUM',
                'constraint' => ['low', 'medium', 'high', 'urgent'],
                'default'    => 'medium',
            ],
            'assigned_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['submitted', 'in_review', 'in_progress', 'resolved', 'rejected'],
                'default'    => 'submitted',
            ],
            'resolution' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'feedback_rating' => [
                'type'       => 'INT',
                'constraint' => 1,
                'null'       => true,
            ],
            'feedback_comments' => [
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
        $this->forge->addKey('status');
        $this->forge->addForeignKey('property_id', 'properties', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('property_unit_id', 'property_units', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('tenant_id', 'tenants', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('assigned_user_id', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('complaints');

        // 6. Preventive Maintenance Schedules
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'schedule_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'asset_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'maintenance_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'frequency' => [
                'type'       => 'ENUM',
                'constraint' => ['daily', 'weekly', 'monthly', 'quarterly', 'semi_annual', 'annual'],
                'default'    => 'monthly',
            ],
            'last_service_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'next_service_date' => [
                'type' => 'DATE',
            ],
            'assigned_technician_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['scheduled', 'completed', 'overdue'],
                'default'    => 'scheduled',
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
        $this->forge->addKey('status');
        $this->forge->addForeignKey('asset_id', 'facility_assets', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('assigned_technician_id', 'technicians', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('preventive_maintenance');

        // 7. CAM Charges
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'cam_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'property_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'property_unit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'tenant_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'area_sqft' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'billing_model' => [
                'type'       => 'ENUM',
                'constraint' => ['per_sqft', 'flat_rate'],
                'default'    => 'per_sqft',
            ],
            'rate' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'period' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'tax' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'total' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['unbilled', 'billed', 'paid'],
                'default'    => 'unbilled',
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
        $this->forge->addForeignKey('property_id', 'properties', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('property_unit_id', 'property_units', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('tenant_id', 'tenants', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('cam_charges');
    }

    public function down()
    {
        $this->forge->dropTable('cam_charges', true);
        $this->forge->dropTable('preventive_maintenance', true);
        $this->forge->dropTable('complaints', true);
        $this->forge->dropTable('maintenance_requests', true);
        $this->forge->dropTable('sla_rules', true);
        $this->forge->dropTable('technicians', true);
        $this->forge->dropTable('facility_assets', true);
    }
}
