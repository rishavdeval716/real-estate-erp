<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStatusHistoryAndPricingTables extends Migration
{
    public function up()
    {
        // 1. Property Status History Table
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
            ],
            'unit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'old_status' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'new_status' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'changed_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
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
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('property_id');
        $this->forge->addKey('unit_id');
        $this->forge->addKey('new_status');
        $this->forge->addForeignKey('property_id', 'properties', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('unit_id', 'property_units', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('changed_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('property_status_history');

        // 2. Property Pricing Table
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
            ],
            'unit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'base_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => '0.00',
            ],
            'price_per_sqft' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
            ],
            'market_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
            ],
            'negotiated_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
            ],
            'discount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => '0.00',
            ],
            'effective_from' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'effective_to' => [
                'type' => 'DATE',
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
        $this->forge->addKey('property_id');
        $this->forge->addKey('unit_id');
        $this->forge->addForeignKey('property_id', 'properties', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('unit_id', 'property_units', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('property_pricing');
    }

    public function down()
    {
        $this->forge->dropTable('property_pricing', true);
        $this->forge->dropTable('property_status_history', true);
    }
}
