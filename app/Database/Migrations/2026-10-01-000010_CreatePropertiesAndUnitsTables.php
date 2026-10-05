<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePropertiesAndUnitsTables extends Migration
{
    public function up()
    {
        // 1. Properties Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'property_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => '200',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'property_type_id' => [
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
            'location_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'owner_name_or_reference' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                'null'       => true,
            ],
            'ownership_details' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'area' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'price' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Available', 'Reserved', 'Under Negotiation', 'Booked', 'Sold', 'Rented', 'Under Maintenance', 'Unavailable'],
                'default'    => 'Available',
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
        $this->forge->addKey('property_type_id');
        $this->forge->addKey('project_id');
        $this->forge->addKey('location_id');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('property_type_id', 'property_types', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('project_id', 'projects', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('location_id', 'locations', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('properties');

        // 2. Property Units Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'property_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
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
            'unit_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'floor' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'flat_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'carpet_area' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'built_up_area' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'balcony' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'parking' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'facing' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'unit_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'availability_status' => [
                'type'       => 'ENUM',
                'constraint' => ['Available', 'Reserved', 'Under Negotiation', 'Booked', 'Sold', 'Rented', 'Under Maintenance', 'Unavailable'],
                'default'    => 'Available',
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
        $this->forge->addKey('property_id');
        $this->forge->addKey('project_id');
        $this->forge->addKey('tower_id');
        $this->forge->addKey('unit_number');
        $this->forge->addKey('availability_status');
        $this->forge->addForeignKey('property_id', 'properties', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('project_id', 'projects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tower_id', 'project_towers', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('property_units');
    }

    public function down()
    {
        $this->forge->dropTable('property_units', true);
        $this->forge->dropTable('properties', true);
    }
}
