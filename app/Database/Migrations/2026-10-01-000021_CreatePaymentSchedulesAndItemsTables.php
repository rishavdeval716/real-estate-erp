<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaymentSchedulesAndItemsTables extends Migration
{
    public function up()
    {
        // 1. Payment Schedules Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'booking_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'schedule_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                'default'    => 'Standard Construction Linked Plan',
            ],
            'total_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
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
        $this->forge->addForeignKey('booking_id', 'bookings', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('payment_schedules');

        // 2. Payment Schedule Items Table (Milestones)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'payment_schedule_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'milestone_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'due_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'percentage' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Pending', 'Partially Paid', 'Paid', 'Overdue'],
                'default'    => 'Pending',
            ],
            'paid_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'remaining_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
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
        $this->forge->addKey('payment_schedule_id');
        $this->forge->addKey('status');
        $this->forge->addKey('due_date');
        $this->forge->addForeignKey('payment_schedule_id', 'payment_schedules', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('payment_schedule_items');
    }

    public function down()
    {
        $this->forge->dropTable('payment_schedule_items', true);
        $this->forge->dropTable('payment_schedules', true);
    }
}
