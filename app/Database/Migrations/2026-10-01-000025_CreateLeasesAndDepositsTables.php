<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLeasesAndDepositsTables extends Migration
{
    public function up()
    {
        // 1. Lease Agreements Table
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
            'tenant_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
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
            'agreement_type' => [
                'type'       => 'ENUM',
                'constraint' => ['residential', 'commercial'],
                'default'    => 'residential',
            ],
            'start_date' => [
                'type' => 'DATE',
            ],
            'end_date' => [
                'type' => 'DATE',
            ],
            'lock_in_period_months' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'notice_period_days' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 30,
            ],
            'monthly_rent' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'security_deposit' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'maintenance_charges' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'rent_escalation_pct' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 5.00,
            ],
            'escalation_frequency' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'annual',
            ],
            'payment_due_day' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 5,
            ],
            'late_fee_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'terms_conditions' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'document_path' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'active', 'expiring_soon', 'expired', 'terminated', 'renewed'],
                'default'    => 'draft',
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('status');
        $this->forge->addKey(['start_date', 'end_date']);
        $this->forge->addForeignKey('tenant_id', 'tenants', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('property_id', 'properties', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('property_unit_id', 'property_units', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('lease_agreements');

        // 2. Security Deposits Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'deposit_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'tenant_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'lease_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'deposit_date' => [
                'type' => 'DATE',
            ],
            'refundable_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'adjusted_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'refund_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'refund_status' => [
                'type'       => 'ENUM',
                'constraint' => ['held', 'partially_refunded', 'refunded', 'forfeited'],
                'default'    => 'held',
            ],
            'adjustment_reason' => [
                'type' => 'TEXT',
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
        $this->forge->addKey('refund_status');
        $this->forge->addForeignKey('tenant_id', 'tenants', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('lease_id', 'lease_agreements', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('security_deposits');

        // 3. Rental History Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tenant_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
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
            'lease_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'previous_rent' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'null'       => true,
            ],
            'current_rent' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'start_date' => [
                'type' => 'DATE',
            ],
            'end_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('tenant_id');
        $this->forge->addForeignKey('tenant_id', 'tenants', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('property_id', 'properties', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('property_unit_id', 'property_units', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('lease_id', 'lease_agreements', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('rental_histories');
    }

    public function down()
    {
        $this->forge->dropTable('rental_histories', true);
        $this->forge->dropTable('security_deposits', true);
        $this->forge->dropTable('lease_agreements', true);
    }
}
