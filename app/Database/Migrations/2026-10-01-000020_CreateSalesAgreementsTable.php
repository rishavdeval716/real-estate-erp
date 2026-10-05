<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSalesAgreementsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'agreement_number' => [
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
            ],
            'agreement_date' => [
                'type' => 'DATE',
            ],
            'agreement_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'Sale Agreement',
            ],
            'agreement_status' => [
                'type'       => 'ENUM',
                'constraint' => ['Draft', 'Pending Signature', 'Signed', 'Cancelled'],
                'default'    => 'Draft',
            ],
            'total_value' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'terms_conditions' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'special_conditions' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addKey('booking_id');
        $this->forge->addKey('customer_id');
        $this->forge->addKey('project_id');
        $this->forge->addKey('property_id');
        $this->forge->addKey('property_unit_id');
        $this->forge->addKey('agreement_status');
        $this->forge->addKey('agreement_date');
        $this->forge->addForeignKey('booking_id', 'bookings', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('project_id', 'projects', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('property_id', 'properties', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('property_unit_id', 'property_units', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('sales_agreements');
    }

    public function down()
    {
        $this->forge->dropTable('sales_agreements', true);
    }
}
