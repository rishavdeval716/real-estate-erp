<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSiteVisitsAndUnitHoldsTables extends Migration
{
    public function up()
    {
        // 1. Site Visits Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'visit_code' => [
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
            'assigned_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'scheduled_at' => [
                'type' => 'DATETIME',
            ],
            'visit_type' => [
                'type'       => 'ENUM',
                'constraint' => ['Property Visit', 'Project Visit', 'Virtual Visit'],
                'default'    => 'Property Visit',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Scheduled', 'Confirmed', 'Completed', 'Cancelled', 'No Show', 'Rescheduled'],
                'default'    => 'Scheduled',
            ],
            'visitor_count' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'default'    => 1,
            ],
            'rating' => [
                'type'       => 'INT',
                'constraint' => 2,
                'unsigned'   => true,
                'null'       => true,
            ],
            'interest_level' => [
                'type'       => 'ENUM',
                'constraint' => ['High', 'Medium', 'Low', 'Not Interested'],
                'null'       => true,
            ],
            'feedback' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'agent_observation' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'preferred_unit' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'price_feedback' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'next_action' => [
                'type'       => 'ENUM',
                'constraint' => ['Follow-up', 'Negotiation', 'Alternative Property', 'Token Discussion', 'Lost'],
                'null'       => true,
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
        $this->forge->addKey('scheduled_at');
        $this->forge->addKey('status');

        $this->forge->addForeignKey('lead_id', 'leads', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('project_id', 'projects', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('property_id', 'properties', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('property_unit_id', 'property_units', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('assigned_user_id', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('site_visits');

        // 2. Unit Holds Table (Temporary Reservation / Hold)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'hold_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'lead_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
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
            ],
            'held_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'hold_status' => [
                'type'       => 'ENUM',
                'constraint' => ['Active', 'Expired', 'Released', 'Converted'],
                'default'    => 'Active',
            ],
            'hold_reason' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'started_at' => [
                'type' => 'DATETIME',
            ],
            'expires_at' => [
                'type' => 'DATETIME',
            ],
            'released_at' => [
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
        $this->forge->addKey('hold_status');
        $this->forge->addKey('expires_at');

        $this->forge->addForeignKey('lead_id', 'leads', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('property_id', 'properties', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('property_unit_id', 'property_units', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('held_by', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('unit_holds');
    }

    public function down()
    {
        $this->forge->dropTable('unit_holds', true);
        $this->forge->dropTable('site_visits', true);
    }
}
