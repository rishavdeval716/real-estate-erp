<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLeadsAndProspectsTables extends Migration
{
    public function up()
    {
        // 1. Leads Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'lead_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'first_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'last_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                'null'       => true,
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => '30',
            ],
            'alternate_phone' => [
                'type'       => 'VARCHAR',
                'constraint' => '30',
                'null'       => true,
            ],
            'lead_source_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'assigned_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'branch_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'lead_status' => [
                'type'       => 'ENUM',
                'constraint' => ['New', 'Contacted', 'Qualified', 'Unqualified', 'Converted', 'Lost'],
                'default'    => 'New',
            ],
            'lead_stage' => [
                'type'       => 'ENUM',
                'constraint' => ['New', 'Contacted', 'Qualified', 'Site Visit Scheduled', 'Site Visit Completed', 'Negotiation', 'Token Pending', 'Ready for Booking', 'Won', 'Lost'],
                'default'    => 'New',
            ],
            'priority' => [
                'type'       => 'ENUM',
                'constraint' => ['Low', 'Medium', 'High', 'Urgent'],
                'default'    => 'Medium',
            ],
            'budget_min' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
            ],
            'budget_max' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
            ],
            'preferred_location' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'property_type_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'project_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'property_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'property_unit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'purchase_purpose' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'purchase_timeline' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'financing_required' => [
                'type'       => 'ENUM',
                'constraint' => ['Yes', 'No', 'Undecided'],
                'default'    => 'Undecided',
            ],
            'site_visit_required' => [
                'type'       => 'ENUM',
                'constraint' => ['Yes', 'No', 'Scheduled', 'Completed'],
                'default'    => 'No',
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('phone');
        $this->forge->addKey('email');
        $this->forge->addKey('lead_status');
        $this->forge->addKey('lead_stage');
        $this->forge->addKey('priority');
        $this->forge->addKey('created_at');

        $this->forge->addForeignKey('lead_source_id', 'lead_sources', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('assigned_user_id', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('branch_id', 'branches', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('property_type_id', 'property_types', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('project_id', 'projects', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('property_id', 'properties', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('property_unit_id', 'property_units', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('leads');

        // 2. Prospects Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'lead_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                'null'       => true,
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => '30',
            ],
            'alternate_phone' => [
                'type'       => 'VARCHAR',
                'constraint' => '30',
                'null'       => true,
            ],
            'address' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'city' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'state' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'pincode' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ],
            'occupation' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'preferred_contact_method' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'Phone',
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
        $this->forge->addForeignKey('lead_id', 'leads', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('prospects');
    }

    public function down()
    {
        $this->forge->dropTable('prospects', true);
        $this->forge->dropTable('leads', true);
    }
}
