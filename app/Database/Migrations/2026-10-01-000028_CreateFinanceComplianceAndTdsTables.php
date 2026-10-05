<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFinanceComplianceAndTdsTables extends Migration
{
    public function up()
    {
        // 1. TDS Entries Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'entry_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'party_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'pan_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'section' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'default'    => '194H',
            ],
            'transaction_reference' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'commission_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'gross_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'tds_rate' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 5.00,
            ],
            'tds_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'net_payable' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'deduction_date' => [
                'type' => 'DATE',
            ],
            'financial_year' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'quarter' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['deducted', 'deposited', 'certified'],
                'default'    => 'deducted',
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
        $this->forge->addKey('pan_number');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('commission_id', 'commissions', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('tds_entries');

        // 2. TDS Certificates (Form 16A)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'certificate_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'unique'     => true,
            ],
            'party_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'pan_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'gross_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'tds_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'financial_year' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'quarter' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
            ],
            'certificate_file' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'issue_date' => [
                'type' => 'DATE',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('tds_certificates');

        // 3. Portal Requests (Customer / Tenant / Partner inquiries & NOCs)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'request_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'portal_type' => [
                'type'       => 'ENUM',
                'constraint' => ['customer', 'tenant', 'partner'],
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'request_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'subject' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'details' => [
                'type' => 'TEXT',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['submitted', 'in_progress', 'approved', 'rejected', 'completed'],
                'default'    => 'submitted',
            ],
            'admin_notes' => [
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
        $this->forge->addKey(['portal_type', 'user_id']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('portal_requests');
    }

    public function down()
    {
        $this->forge->dropTable('portal_requests', true);
        $this->forge->dropTable('tds_certificates', true);
        $this->forge->dropTable('tds_entries', true);
    }
}
