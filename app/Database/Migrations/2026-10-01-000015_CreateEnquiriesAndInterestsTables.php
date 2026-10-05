<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEnquiriesAndInterestsTables extends Migration
{
    public function up()
    {
        // 1. Enquiries Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'enquiry_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'lead_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
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
            'enquiry_type' => [
                'type'       => 'ENUM',
                'constraint' => ['Purchase', 'Investment', 'Rent', 'Commercial', 'Plot/Land', 'Other'],
                'default'    => 'Purchase',
            ],
            'requirement' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'budget' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
            ],
            'preferred_location' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'preferred_property_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Open', 'In Progress', 'Qualified', 'Closed', 'Cancelled'],
                'default'    => 'Open',
            ],
            'remarks' => [
                'type' => 'TEXT',
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

        $this->forge->addForeignKey('lead_id', 'leads', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('project_id', 'projects', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('property_id', 'properties', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('property_unit_id', 'property_units', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('enquiries');

        // 2. Lead Property Interests Table
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
            'interest_level' => [
                'type'       => 'ENUM',
                'constraint' => ['Primary', 'Interested', 'Alternative', 'Not Interested'],
                'default'    => 'Interested',
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

        $this->forge->addForeignKey('lead_id', 'leads', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('project_id', 'projects', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('property_id', 'properties', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('property_unit_id', 'property_units', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('lead_property_interests');
    }

    public function down()
    {
        $this->forge->dropTable('lead_property_interests', true);
        $this->forge->dropTable('enquiries', true);
    }
}
