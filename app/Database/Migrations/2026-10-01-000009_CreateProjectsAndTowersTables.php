<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProjectsAndTowersTables extends Migration
{
    public function up()
    {
        // 1. Projects Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'project_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'builder_developer' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
            ],
            'location_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'construction_status' => [
                'type'       => 'ENUM',
                'constraint' => ['Pre-Launch', 'Under Construction', 'Ready to Move', 'Completed', 'On Hold'],
                'default'    => 'Under Construction',
            ],
            'possession_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'total_units' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'available_units' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive', 'completed', 'archived'],
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('location_id');
        $this->forge->addKey('construction_status');
        $this->forge->addForeignKey('location_id', 'locations', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('projects');

        // 2. Project Towers / Blocks Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'project_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tower_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'tower_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'number_of_floors' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 1,
            ],
            'total_units' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
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
        $this->forge->addUniqueKey(['project_id', 'tower_code']);
        $this->forge->addForeignKey('project_id', 'projects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('project_towers');
    }

    public function down()
    {
        $this->forge->dropTable('project_towers', true);
        $this->forge->dropTable('projects', true);
    }
}
