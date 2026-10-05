<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePropertyRelationsAndMediaTables extends Migration
{
    public function up()
    {
        // 1. Property Amenities Pivot Table
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
            'amenity_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['property_id', 'amenity_id'], false, true); // Unique composite
        $this->forge->addForeignKey('property_id', 'properties', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('amenity_id', 'amenities', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('property_amenities');

        // 2. Property Media Table
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
            'media_type' => [
                'type'       => 'ENUM',
                'constraint' => ['photo', 'gallery', 'video', 'floor_plan', 'brochure', 'virtual_tour', 'document'],
                'default'    => 'photo',
            ],
            'file_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'file_path' => [
                'type'       => 'VARCHAR',
                'constraint' => '500',
            ],
            'file_size' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'mime_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => '200',
                'null'       => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_primary' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
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
        $this->forge->addKey('media_type');
        $this->forge->addKey('is_primary');
        $this->forge->addForeignKey('property_id', 'properties', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('property_media');
    }

    public function down()
    {
        $this->forge->dropTable('property_media', true);
        $this->forge->dropTable('property_amenities', true);
    }
}
