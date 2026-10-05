<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePhase7ERPCompletenessTables extends Migration
{
    public function up()
    {
        // 1. Property Owners Table
        if (!$this->db->tableExists('property_owners')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'owner_code' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '50',
                    'unique'     => true,
                ],
                'user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'first_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '100',
                ],
                'last_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '100',
                ],
                'company_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '150',
                    'null'       => true,
                ],
                'email' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '150',
                ],
                'phone' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '30',
                ],
                'alternate_phone' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '30',
                    'null'       => true,
                ],
                'address' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'city' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '100',
                    'null'       => true,
                ],
                'state' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '100',
                    'null'       => true,
                ],
                'pincode' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '20',
                    'null'       => true,
                ],
                'pan_number' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '50',
                    'null'       => true,
                ],
                'aadhaar_number' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '50',
                    'null'       => true,
                ],
                'kyc_status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['pending', 'verified', 'rejected'],
                    'default'    => 'pending',
                ],
                'bank_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '100',
                    'null'       => true,
                ],
                'bank_account_number' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '50',
                    'null'       => true,
                ],
                'bank_ifsc' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '30',
                    'null'       => true,
                ],
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['active', 'inactive'],
                    'default'    => 'active',
                ],
                'notes' => [
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
                'deleted_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('user_id');
            $this->forge->addKey('email');
            $this->forge->addKey('phone');
            $this->forge->createTable('property_owners');
        }

        // Add owner_id to properties table if not present
        if ($this->db->tableExists('properties')) {
            $fields = $this->db->getFieldNames('properties');
            if (!in_array('owner_id', $fields)) {
                $this->forge->addColumn('properties', [
                    'owner_id' => [
                        'type'       => 'INT',
                        'constraint' => 11,
                        'unsigned'   => true,
                        'null'       => true,
                        'after'      => 'location_id',
                    ],
                ]);
            }
        }

        // 2. Agents / Brokers Table
        if (!$this->db->tableExists('agents')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'agent_code' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '50',
                    'unique'     => true,
                ],
                'user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'agent_type' => [
                    'type'       => 'ENUM',
                    'constraint' => ['Internal Agent', 'External Broker', 'Channel Partner', 'Agency'],
                    'default'    => 'External Broker',
                ],
                'agency_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '150',
                    'null'       => true,
                ],
                'first_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '100',
                ],
                'last_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '100',
                ],
                'email' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '150',
                ],
                'phone' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '30',
                ],
                'license_number' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '100',
                    'null'       => true,
                ],
                'pan_number' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '50',
                    'null'       => true,
                ],
                'commission_rate' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,2',
                    'default'    => 2.00,
                ],
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['active', 'inactive', 'suspended'],
                    'default'    => 'active',
                ],
                'address' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'city' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '100',
                    'null'       => true,
                ],
                'notes' => [
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
                'deleted_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('user_id');
            $this->forge->addKey('status');
            $this->forge->createTable('agents');
        }

        // 3. Expense Categories Table
        if (!$this->db->tableExists('expense_categories')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '100',
                ],
                'code' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '50',
                    'unique'     => true,
                ],
                'description' => [
                    'type' => 'TEXT',
                    'null' => true,
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
            $this->forge->createTable('expense_categories');
        }

        // 4. Property Expenses Table
        if (!$this->db->tableExists('property_expenses')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'expense_code' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '50',
                    'unique'     => true,
                ],
                'category_id' => [
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
                    'null'       => true,
                ],
                'project_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'title' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '200',
                ],
                'expense_date' => [
                    'type' => 'DATE',
                ],
                'payee_vendor' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '150',
                ],
                'amount' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '15,2',
                ],
                'tax_amount' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '15,2',
                    'default'    => 0.00,
                ],
                'total_amount' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '15,2',
                ],
                'payment_method' => [
                    'type'       => 'ENUM',
                    'constraint' => ['Cash', 'Bank Transfer', 'Cheque', 'UPI', 'Credit Card', 'NEFT', 'RTGS', 'Other'],
                    'default'    => 'Bank Transfer',
                ],
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['Draft', 'Pending Approval', 'Approved', 'Paid', 'Rejected'],
                    'default'    => 'Approved',
                ],
                'receipt_file' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                    'null'       => true,
                ],
                'notes' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'created_by' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'approved_by' => [
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
            $this->forge->addKey('category_id');
            $this->forge->addKey('property_id');
            $this->forge->addKey('project_id');
            $this->forge->addKey('expense_date');
            $this->forge->createTable('property_expenses');
        }

        // 5. Marketing Campaigns Table
        if (!$this->db->tableExists('marketing_campaigns')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'campaign_code' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '50',
                    'unique'     => true,
                ],
                'name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '200',
                ],
                'campaign_type' => [
                    'type'       => 'ENUM',
                    'constraint' => ['Social Media', 'Google Ads', 'Property Portal', 'Print Media', 'Hoarding / Outdoor', 'Email Marketing', 'SMS Campaign', 'Event / Expo', 'Referral Program', 'Other'],
                    'default'    => 'Social Media',
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
                'start_date' => [
                    'type' => 'DATE',
                ],
                'end_date' => [
                    'type' => 'DATE',
                    'null' => true,
                ],
                'budget' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '15,2',
                    'default'    => 0.00,
                ],
                'actual_spend' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '15,2',
                    'default'    => 0.00,
                ],
                'leads_generated' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'default'    => 0,
                ],
                'qualified_leads' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'default'    => 0,
                ],
                'converted_leads' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'default'    => 0,
                ],
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['Planning', 'Active', 'Paused', 'Completed', 'Cancelled'],
                    'default'    => 'Active',
                ],
                'description' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'notes' => [
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
                'deleted_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('project_id');
            $this->forge->addKey('status');
            $this->forge->createTable('marketing_campaigns');
        }

        // 6. Property Documents Table (Legal & Compliance)
        if (!$this->db->tableExists('property_documents')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'document_code' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '50',
                    'unique'     => true,
                ],
                'title' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '200',
                ],
                'document_category' => [
                    'type'       => 'ENUM',
                    'constraint' => ['Ownership / Title Deed', 'Registry Document', 'Property Tax Receipt', 'NOC Clearance', 'Building Approval Plan', 'Occupancy Certificate', 'RERA Certificate', 'Encumbrance Certificate', 'Legal Opinion', 'KYC Document', 'Other'],
                    'default'    => 'Ownership / Title Deed',
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
                    'null'       => true,
                ],
                'owner_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'file_path' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                ],
                'file_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                ],
                'file_size' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'default'    => 0,
                ],
                'mime_type' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '100',
                    'default'    => 'application/pdf',
                ],
                'issue_date' => [
                    'type' => 'DATE',
                    'null' => true,
                ],
                'expiry_date' => [
                    'type' => 'DATE',
                    'null' => true,
                ],
                'verification_status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['Pending', 'Under Review', 'Verified', 'Rejected', 'Expired'],
                    'default'    => 'Pending',
                ],
                'verified_by' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'verified_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'rejection_reason' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'notes' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'uploaded_by' => [
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
            $this->forge->addKey('property_id');
            $this->forge->addKey('owner_id');
            $this->forge->addKey('verification_status');
            $this->forge->createTable('property_documents');
        }

        // 7. Property Verifications Table (Verification & Compliance)
        if (!$this->db->tableExists('property_verifications')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'verification_code' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '50',
                    'unique'     => true,
                ],
                'property_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'verification_type' => [
                    'type'       => 'ENUM',
                    'constraint' => ['Legal Title Clearance', 'Physical Property Audit', 'RERA Compliance Check', 'Municipal Approval Check', 'Structural & Fire Safety', 'Tax Compliance'],
                    'default'    => 'Legal Title Clearance',
                ],
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['Pending', 'Under Review', 'Verified', 'Rejected', 'Expired'],
                    'default'    => 'Pending',
                ],
                'assigned_to' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'checklist_data' => [
                    'type' => 'JSON',
                    'null' => true,
                ],
                'findings' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'verified_by' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'verified_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'rejection_reason' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'expiry_date' => [
                    'type' => 'DATE',
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
            $this->forge->addKey('status');
            $this->forge->createTable('property_verifications');
        }

        // 8. Notifications Table (Centralized Notification & Reminder System)
        if (!$this->db->tableExists('notifications')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'role_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'title' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '200',
                ],
                'message' => [
                    'type' => 'TEXT',
                ],
                'type' => [
                    'type'       => 'ENUM',
                    'constraint' => ['lead', 'site_visit', 'followup', 'payment_due', 'overdue_payment', 'rent_due', 'agreement_expiry', 'booking', 'document_expiry', 'maintenance', 'system'],
                    'default'    => 'system',
                ],
                'priority' => [
                    'type'       => 'ENUM',
                    'constraint' => ['low', 'medium', 'high', 'urgent'],
                    'default'    => 'medium',
                ],
                'related_module' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '100',
                    'null'       => true,
                ],
                'related_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'link_url' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                    'null'       => true,
                ],
                'is_read' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                ],
                'read_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('user_id');
            $this->forge->addKey('is_read');
            $this->forge->createTable('notifications');
        }

        // 9. Customer Communications Table (History & Logs)
        if (!$this->db->tableExists('customer_communications')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'comm_code' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '50',
                    'unique'     => true,
                ],
                'customer_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'lead_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'channel' => [
                    'type'       => 'ENUM',
                    'constraint' => ['Phone Call', 'WhatsApp', 'Email', 'SMS', 'In-Person Meeting', 'Video Call', 'Letter / Notice'],
                    'default'    => 'Phone Call',
                ],
                'purpose' => [
                    'type'       => 'ENUM',
                    'constraint' => ['Follow-up', 'Property Enquiry', 'Site Visit Reminder', 'Booking Confirmation', 'Payment Reminder', 'Payment Receipt', 'Rent Reminder', 'Agreement Execution', 'Document Request', 'Grievance Resolution', 'General Update'],
                    'default'    => 'Follow-up',
                ],
                'subject' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '200',
                ],
                'content' => [
                    'type' => 'TEXT',
                ],
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['Planned', 'Sent', 'Delivered', 'Completed', 'Failed'],
                    'default'    => 'Completed',
                ],
                'communication_date' => [
                    'type' => 'DATETIME',
                ],
                'user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'response_notes' => [
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
            $this->forge->addKey('customer_id');
            $this->forge->addKey('lead_id');
            $this->forge->createTable('customer_communications');
        }

        // 10. System Settings Table
        if (!$this->db->tableExists('system_settings')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'setting_group' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '50',
                    'default'    => 'general',
                ],
                'setting_key' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '100',
                    'unique'     => true,
                ],
                'setting_value' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'setting_type' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '30',
                    'default'    => 'string',
                ],
                'description' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                    'null'       => true,
                ],
                'updated_by' => [
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
            $this->forge->addKey('setting_group');
            $this->forge->createTable('system_settings');
        }
    }

    public function down()
    {
        $this->forge->dropTable('system_settings', true);
        $this->forge->dropTable('customer_communications', true);
        $this->forge->dropTable('notifications', true);
        $this->forge->dropTable('property_verifications', true);
        $this->forge->dropTable('property_documents', true);
        $this->forge->dropTable('marketing_campaigns', true);
        $this->forge->dropTable('property_expenses', true);
        $this->forge->dropTable('expense_categories', true);
        $this->forge->dropTable('agents', true);

        if ($this->db->tableExists('properties')) {
            $fields = $this->db->getFieldNames('properties');
            if (in_array('owner_id', $fields)) {
                $this->forge->dropColumn('properties', 'owner_id');
            }
        }

        $this->forge->dropTable('property_owners', true);
    }
}
