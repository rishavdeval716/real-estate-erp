<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\CustomerModel;
use App\Models\CustomerDocumentModel;
use App\Models\BookingModel;
use App\Models\BookingStatusHistoryModel;
use App\Models\SalesAgreementModel;
use App\Models\PaymentScheduleModel;
use App\Models\PaymentScheduleItemModel;
use App\Models\PaymentModel;
use App\Models\InvoiceModel;
use App\Models\ReceiptModel;
use App\Models\CommissionRuleModel;
use App\Models\CommissionModel;
use App\Models\PropertyUnitModel;
use App\Models\PropertyStatusHistoryModel;
use App\Models\AuditLogModel;

class Phase4SalesSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Fetch sales executive and admin users
        $admin = $db->table('users')->where('email', 'admin@realestate-erp.local')->get()->getRowArray();
        $adminId = $admin['id'] ?? 1;

        $salesUser = $db->table('users')->where('email', 'sales@realestate-erp.local')->get()->getRowArray();
        $salesId = $salesUser['id'] ?? $adminId;

        // 2. Fetch existing leads
        $leads = $db->table('leads')->where('deleted_at', null)->orderBy('id', 'ASC')->limit(5)->get()->getResultArray();

        // 3. Fetch existing property units
        $units = $db->table('property_units')->where('deleted_at', null)->orderBy('id', 'ASC')->limit(6)->get()->getResultArray();

        if (empty($units)) {
            echo "No property units found to seed Phase 4 bookings.\n";
            return;
        }

        // 4. Commission Rules
        $ruleModel = new CommissionRuleModel();
        $existingRules = $ruleModel->countAllResults();
        if ($existingRules === 0) {
            $ruleModel->insert([
                'name'             => 'Standard Broker Commission (2.0%)',
                'commission_type'  => 'Percentage',
                'commission_value' => 2.00,
                'applicable_to'    => 'Broker',
                'status'           => 'Active',
            ]);
            $ruleModel->insert([
                'name'             => 'Channel Partner Premium (2.5%)',
                'commission_type'  => 'Percentage',
                'commission_value' => 2.50,
                'applicable_to'    => 'Channel Partner',
                'status'           => 'Active',
            ]);
            $ruleModel->insert([
                'name'             => 'In-House Executive Direct Sales Bonus',
                'commission_type'  => 'Fixed',
                'commission_value' => 25000.00,
                'applicable_to'    => 'Direct',
                'status'           => 'Active',
            ]);
        }
        $brokerRule = $ruleModel->first();

        // 5. Seed Customers
        $customerModel = new CustomerModel();
        $docModel      = new CustomerDocumentModel();

        // Check if customers already exist
        if ($customerModel->countAllResults() === 0) {
            $customersData = [
                [
                    'customer_code'   => 'CUS-2026-000001',
                    'lead_id'         => $leads[0]['id'] ?? null,
                    'first_name'      => 'Rajesh',
                    'last_name'       => 'Sharma',
                    'email'           => 'rajesh.sharma@example.com',
                    'phone'           => '+91 98201 12345',
                    'alternate_phone' => '+91 98201 54321',
                    'address'         => 'Flat 402, Sea Green Apartments, Worli',
                    'city'            => 'Mumbai',
                    'state'           => 'Maharashtra',
                    'pincode'         => '400018',
                    'id_proof_type'   => 'PAN Card',
                    'id_proof_number' => 'ABCPS1234F',
                    'kyc_status'      => 'Verified',
                    'status'          => 'active',
                ],
                [
                    'customer_code'   => 'CUS-2026-000002',
                    'lead_id'         => $leads[1]['id'] ?? null,
                    'first_name'      => 'Priya',
                    'last_name'       => 'Patel',
                    'email'           => 'priya.patel@example.com',
                    'phone'           => '+91 98202 23456',
                    'alternate_phone' => null,
                    'address'         => 'B-14, Shivalik Hills, Satellite',
                    'city'            => 'Ahmedabad',
                    'state'           => 'Gujarat',
                    'pincode'         => '380015',
                    'id_proof_type'   => 'Aadhaar Card',
                    'id_proof_number' => '8492 1092 3847',
                    'kyc_status'      => 'Verified',
                    'status'          => 'active',
                ],
                [
                    'customer_code'   => 'CUS-2026-000003',
                    'lead_id'         => $leads[2]['id'] ?? null,
                    'first_name'      => 'Amit',
                    'last_name'       => 'Verma',
                    'email'           => 'amit.verma@example.com',
                    'phone'           => '+91 98203 34567',
                    'alternate_phone' => null,
                    'address'         => 'Tower 3, Penthouse 18B, Cyber City',
                    'city'            => 'Gurugram',
                    'state'           => 'Haryana',
                    'pincode'         => '122002',
                    'id_proof_type'   => 'PAN Card',
                    'id_proof_number' => 'AVPPR4567G',
                    'kyc_status'      => 'Pending',
                    'status'          => 'active',
                ],
                [
                    'customer_code'   => 'CUS-2026-000004',
                    'lead_id'         => null,
                    'first_name'      => 'Sneha',
                    'last_name'       => 'Kulkarni',
                    'email'           => 'sneha.kulkarni@example.com',
                    'phone'           => '+91 98204 45678',
                    'alternate_phone' => '+91 98204 99999',
                    'address'         => 'Villa 7, Baner Hills Estate',
                    'city'            => 'Pune',
                    'state'           => 'Maharashtra',
                    'pincode'         => '411045',
                    'id_proof_type'   => 'Passport',
                    'id_proof_number' => 'Z1234567',
                    'kyc_status'      => 'Verified',
                    'status'          => 'active',
                ],
            ];

            foreach ($customersData as $cd) {
                $custId = $customerModel->insert($cd);
                // Insert mock KYC document
                $docModel->insert([
                    'customer_id'         => $custId,
                    'document_type'       => $cd['id_proof_type'],
                    'document_number'     => $cd['id_proof_number'],
                    'file_name'           => strtolower(str_replace(' ', '_', $cd['id_proof_type'])) . '_doc.pdf',
                    'file_path'           => 'kyc/sample_' . $custId . '.pdf',
                    'verification_status' => $cd['kyc_status'],
                    'verified_by'         => $cd['kyc_status'] === 'Verified' ? $adminId : null,
                    'verified_at'         => $cd['kyc_status'] === 'Verified' ? date('Y-m-d H:i:s') : null,
                ]);

                AuditLogModel::record(
                    'Customer Created',
                    'customers',
                    $custId,
                    "Customer {$cd['customer_code']} created ({$cd['first_name']} {$cd['last_name']})",
                    $adminId
                );
            }
        }

        $allCustomers = $customerModel->findAll();
        $c1 = $allCustomers[0];
        $c2 = $allCustomers[1];
        $c3 = $allCustomers[2];
        $c4 = $allCustomers[3];

        // 6. Seed Bookings
        $bookingModel   = new BookingModel();
        $historyModel   = new BookingStatusHistoryModel();
        $scheduleModel  = new PaymentScheduleModel();
        $itemModel      = new PaymentScheduleItemModel();
        $paymentModel   = new PaymentModel();
        $invoiceModel   = new InvoiceModel();
        $receiptModel   = new ReceiptModel();
        $agreementModel = new SalesAgreementModel();
        $unitModel      = new PropertyUnitModel();
        $commModel      = new CommissionModel();
        $propHistModel  = new PropertyStatusHistoryModel();

        if ($bookingModel->countAllResults() < 3) {
            $db->query('SET FOREIGN_KEY_CHECKS=0');
            $db->table('commissions')->truncate();
            $db->table('receipts')->truncate();
            $db->table('invoices')->truncate();
            $db->table('payments')->truncate();
            $db->table('payment_schedule_items')->truncate();
            $db->table('payment_schedules')->truncate();
            $db->table('sales_agreements')->truncate();
            $db->table('booking_status_history')->truncate();
            $db->table('bookings')->truncate();
            $db->query('SET FOREIGN_KEY_CHECKS=1');
            // ==========================================
            // Booking 1: Confirmed with 2 Payments, Agreement, Invoice, Receipt, Commission
            // ==========================================
            $u1 = $units[0];
            $basePrice1 = (float)$u1['unit_price'];
            $discount1  = 100000.00;
            $tax1       = round(($basePrice1 - $discount1) * 0.05, 2);
            $final1     = $basePrice1 - $discount1 + $tax1;

            $b1Id = $bookingModel->insert([
                'booking_number'     => 'BK-2026-000001',
                'customer_id'        => $c1['id'],
                'lead_id'            => $c1['lead_id'],
                'project_id'         => $u1['project_id'],
                'property_id'        => $u1['property_id'] ?: null,
                'property_unit_id'   => $u1['id'],
                'sales_executive_id' => $salesId,
                'booking_date'       => date('Y-m-d', strtotime('-30 days')),
                'booking_status'     => 'Confirmed',
                'base_price'         => $basePrice1,
                'discount'           => $discount1,
                'tax_amount'         => $tax1,
                'final_amount'       => $final1,
                'token_amount'       => 100000.00,
                'booking_amount'     => 500000.00,
                'remarks'            => 'Standard residential allottee booking with approved concession.',
                'created_by'         => $adminId,
            ]);

            // Transition Unit 1 to Booked
            $unitModel->update($u1['id'], ['availability_status' => 'Booked']);
            $propHistModel->recordChange((int)($u1['property_id'] ?: 0), (int)$u1['id'], $u1['availability_status'], 'Booked', $adminId, 'Unit booked via Booking #BK-2026-000001');

            // Log status history
            $historyModel->recordChange($b1Id, null, 'Draft', $adminId, 'Created in Draft state');
            $historyModel->recordChange($b1Id, 'Draft', 'Confirmed', $adminId, 'Formally confirmed with token receipt');

            // Generate Payment Schedule
            $s1Id = $scheduleModel->generateStandardMilestones($b1Id, $final1, date('Y-m-d', strtotime('-30 days')));

            // Milestones for Schedule 1
            $s1Items = $itemModel->where('payment_schedule_id', $s1Id)->orderBy('id', 'ASC')->findAll();

            // Record Payment 1 (Token Amount)
            $p1Amount = 100000.00;
            $p1Id = $paymentModel->insert([
                'payment_number'           => 'PMT-2026-000001',
                'booking_id'               => $b1Id,
                'customer_id'              => $c1['id'],
                'payment_schedule_item_id' => $s1Items[0]['id'],
                'payment_date'             => date('Y-m-d', strtotime('-30 days')),
                'amount'                   => $p1Amount,
                'payment_method'           => 'Bank Transfer',
                'transaction_reference'    => 'HDFCNEFT92019238',
                'bank_name'                => 'HDFC Bank',
                'cheque_number'            => null,
                'remarks'                  => 'Token advance against booking',
                'status'                   => 'Received',
                'received_by'              => $salesId,
            ]);
            $itemModel->allocatePayment($s1Items[0]['id'], $p1Amount);

            // Receipt 1
            $receiptModel->insert([
                'receipt_number'        => 'RCT-2026-000001',
                'payment_id'            => $p1Id,
                'booking_id'            => $b1Id,
                'customer_id'           => $c1['id'],
                'receipt_date'          => date('Y-m-d', strtotime('-30 days')),
                'amount'                => $p1Amount,
                'payment_method'        => 'Bank Transfer',
                'transaction_reference' => 'HDFCNEFT92019238',
                'remarks'               => 'Official receipt for booking token payment',
            ]);

            // Record Payment 2 (Booking Advance)
            $p2Amount = 500000.00;
            $p2Id = $paymentModel->insert([
                'payment_number'           => 'PMT-2026-000002',
                'booking_id'               => $b1Id,
                'customer_id'              => $c1['id'],
                'payment_schedule_item_id' => $s1Items[1]['id'],
                'payment_date'             => date('Y-m-d', strtotime('-15 days')),
                'amount'                   => $p2Amount,
                'payment_method'           => 'NEFT',
                'transaction_reference'    => 'ICICINEFT5502910',
                'bank_name'                => 'ICICI Bank',
                'cheque_number'            => null,
                'remarks'                  => 'Booking confirmation milestone advance',
                'status'                   => 'Received',
                'received_by'              => $salesId,
            ]);
            $itemModel->allocatePayment($s1Items[1]['id'], $p2Amount);

            // Receipt 2
            $receiptModel->insert([
                'receipt_number'        => 'RCT-2026-000002',
                'payment_id'            => $p2Id,
                'booking_id'            => $b1Id,
                'customer_id'           => $c1['id'],
                'receipt_date'          => date('Y-m-d', strtotime('-15 days')),
                'amount'                => $p2Amount,
                'payment_method'        => 'NEFT',
                'transaction_reference' => 'ICICINEFT5502910',
                'remarks'               => 'Official receipt for booking advance milestone',
            ]);

            // Sales Agreement 1 (Signed)
            $agr1Id = $agreementModel->insert([
                'agreement_number'   => 'AGR-2026-000001',
                'booking_id'         => $b1Id,
                'customer_id'        => $c1['id'],
                'project_id'         => $u1['project_id'],
                'property_id'        => $u1['property_id'] ?: null,
                'property_unit_id'   => $u1['id'],
                'agreement_date'     => date('Y-m-d', strtotime('-10 days')),
                'agreement_type'     => 'Agreement to Sale',
                'agreement_status'   => 'Signed',
                'total_value'        => $final1,
                'terms_conditions'   => SalesAgreementModel::getDefaultTerms(),
                'special_conditions' => 'Exclusive covered stilt parking bay P-12 allotted with no additional charge.',
                'remarks'            => 'Executed and registered with statutory stamp duty paid.',
                'created_by'         => $adminId,
            ]);

            // Invoice 1 (Issued)
            $inv1Id = $invoiceModel->insert([
                'invoice_number' => 'INV-2026-000001',
                'booking_id'     => $b1Id,
                'customer_id'    => $c1['id'],
                'invoice_date'   => date('Y-m-d', strtotime('-25 days')),
                'due_date'       => date('Y-m-d', strtotime('-5 days')),
                'subtotal'       => $basePrice1 - $discount1,
                'discount'       => 0.00,
                'tax'            => $tax1,
                'total_amount'   => $final1,
                'paid_amount'    => $p1Amount + $p2Amount,
                'balance_amount' => $final1 - ($p1Amount + $p2Amount),
                'status'         => 'Issued',
            ]);

            // Commission 1 (Approved)
            if ($brokerRule) {
                $commModel->insert([
                    'booking_id'         => $b1Id,
                    'agent_user_id'      => $salesId,
                    'commission_rule_id' => $brokerRule['id'],
                    'booking_amount'     => $final1,
                    'commission_amount'  => round(($final1 * (float)$brokerRule['commission_value']) / 100, 2),
                    'status'             => 'Approved',
                    'payable_date'       => date('Y-m-d', strtotime('+30 days')),
                    'remarks'            => 'Approved for disbursement upon slab completion',
                ]);
            }

            // ==========================================
            // Booking 2: Confirmed with 1 Payment, Pending Signature Agreement
            // ==========================================
            if (isset($units[1])) {
                $u2 = $units[1];
                $basePrice2 = (float)$u2['unit_price'];
                $final2     = $basePrice2;

                $b2Id = $bookingModel->insert([
                    'booking_number'     => 'BK-2026-000002',
                    'customer_id'        => $c2['id'],
                    'lead_id'            => $c2['lead_id'],
                    'project_id'         => $u2['project_id'],
                    'property_id'        => $u2['property_id'] ?: null,
                    'property_unit_id'   => $u2['id'],
                    'sales_executive_id' => $salesId,
                    'booking_date'       => date('Y-m-d', strtotime('-12 days')),
                    'booking_status'     => 'Confirmed',
                    'base_price'         => $basePrice2,
                    'discount'           => 0.00,
                    'tax_amount'         => 0.00,
                    'final_amount'       => $final2,
                    'token_amount'       => 200000.00,
                    'booking_amount'     => 0.00,
                    'remarks'            => 'Fast-track NRI buyer reservation.',
                    'created_by'         => $salesId,
                ]);

                // Transition Unit 2 to Booked
                $unitModel->update($u2['id'], ['availability_status' => 'Booked']);
                $propHistModel->recordChange((int)($u2['property_id'] ?: 0), (int)$u2['id'], $u2['availability_status'], 'Booked', $salesId, 'Unit booked via Booking #BK-2026-000002');

                // Status history
                $historyModel->recordChange($b2Id, null, 'Draft', $salesId, 'Created');
                $historyModel->recordChange($b2Id, 'Draft', 'Confirmed', $salesId, 'Confirmed on receipt of UPI token');

                // Schedule
                $s2Id = $scheduleModel->generateStandardMilestones($b2Id, $final2, date('Y-m-d', strtotime('-12 days')));
                $s2Items = $itemModel->where('payment_schedule_id', $s2Id)->orderBy('id', 'ASC')->findAll();

                // Payment (Token UPI)
                $p3Id = $paymentModel->insert([
                    'payment_number'           => 'PMT-2026-000003',
                    'booking_id'               => $b2Id,
                    'customer_id'              => $c2['id'],
                    'payment_schedule_item_id' => $s2Items[0]['id'],
                    'payment_date'             => date('Y-m-d', strtotime('-12 days')),
                    'amount'                   => 200000.00,
                    'payment_method'           => 'UPI',
                    'transaction_reference'    => 'UPI/920182749219',
                    'bank_name'                => 'State Bank of India',
                    'remarks'                  => 'Initial token advance via UPI',
                    'status'                   => 'Received',
                    'received_by'              => $salesId,
                ]);
                $itemModel->allocatePayment($s2Items[0]['id'], 200000.00);

                // Receipt 3
                $receiptModel->insert([
                    'receipt_number'        => 'RCT-2026-000003',
                    'payment_id'            => $p3Id,
                    'booking_id'            => $b2Id,
                    'customer_id'           => $c2['id'],
                    'receipt_date'          => date('Y-m-d', strtotime('-12 days')),
                    'amount'                => 200000.00,
                    'payment_method'        => 'UPI',
                    'transaction_reference' => 'UPI/920182749219',
                    'remarks'               => 'Official electronic token receipt',
                ]);

                // Agreement 2 (Pending Signature)
                $agreementModel->insert([
                    'agreement_number'   => 'AGR-2026-000002',
                    'booking_id'         => $b2Id,
                    'customer_id'        => $c2['id'],
                    'project_id'         => $u2['project_id'],
                    'property_id'        => $u2['property_id'] ?: null,
                    'property_unit_id'   => $u2['id'],
                    'agreement_date'     => date('Y-m-d', strtotime('-5 days')),
                    'agreement_type'     => 'Allotment Letter',
                    'agreement_status'   => 'Pending Signature',
                    'total_value'        => $final2,
                    'terms_conditions'   => SalesAgreementModel::getDefaultTerms(),
                    'special_conditions' => 'Buyer to execute bi-lateral agreement upon physical arrival in Mumbai.',
                    'remarks'            => 'Sent to customer email for e-signature review.',
                    'created_by'         => $salesId,
                ]);

                // Commission 2 (Paid)
                $commModel->insert([
                    'booking_id'         => $b2Id,
                    'agent_user_id'      => $salesId,
                    'commission_rule_id' => null,
                    'booking_amount'     => $final2,
                    'commission_amount'  => 25000.00,
                    'status'             => 'Paid',
                    'payable_date'       => date('Y-m-d', strtotime('-5 days')),
                    'paid_date'          => date('Y-m-d', strtotime('-2 days')),
                    'remarks'            => 'In-house quarterly incentive disbursement',
                ]);
            }

            // ==========================================
            // Booking 3: Draft (Pending Confirmation)
            // ==========================================
            if (isset($units[2])) {
                $u3 = $units[2];
                $b3Id = $bookingModel->insert([
                    'booking_number'     => 'BK-2026-000003',
                    'customer_id'        => $c3['id'],
                    'lead_id'            => $c3['lead_id'],
                    'project_id'         => $u3['project_id'],
                    'property_id'        => $u3['property_id'] ?: null,
                    'property_unit_id'   => $u3['id'],
                    'sales_executive_id' => $salesId,
                    'booking_date'       => date('Y-m-d', strtotime('-3 days')),
                    'booking_status'     => 'Pending Confirmation',
                    'base_price'         => (float)$u3['unit_price'],
                    'discount'           => 50000.00,
                    'tax_amount'         => 0.00,
                    'final_amount'       => (float)$u3['unit_price'] - 50000.00,
                    'token_amount'       => 50000.00,
                    'booking_amount'     => 0.00,
                    'remarks'            => 'Awaiting cheque clearance before formal unit confirmation.',
                    'created_by'         => $salesId,
                ]);

                $historyModel->recordChange($b3Id, null, 'Draft', $salesId, 'Created in Draft');
                $historyModel->recordChange($b3Id, 'Draft', 'Pending Confirmation', $salesId, 'Pending cheque deposit');
                $scheduleModel->generateStandardMilestones($b3Id, (float)$u3['unit_price'] - 50000.00, date('Y-m-d', strtotime('-3 days')));
            }

            // ==========================================
            // Booking 4: Cancelled Demo (Unit released back to Available)
            // ==========================================
            if (isset($units[3])) {
                $u4 = $units[3];
                $b4Id = $bookingModel->insert([
                    'booking_number'     => 'BK-2026-000004',
                    'customer_id'        => $c4['id'],
                    'lead_id'            => null,
                    'project_id'         => $u4['project_id'],
                    'property_id'        => $u4['property_id'] ?: null,
                    'property_unit_id'   => $u4['id'],
                    'sales_executive_id' => $salesId,
                    'booking_date'       => date('Y-m-d', strtotime('-40 days')),
                    'booking_status'     => 'Cancelled',
                    'base_price'         => (float)$u4['unit_price'],
                    'discount'           => 0.00,
                    'tax_amount'         => 0.00,
                    'final_amount'       => (float)$u4['unit_price'],
                    'token_amount'       => 50000.00,
                    'booking_amount'     => 0.00,
                    'remarks'            => '[Cancelled: ' . date('Y-m-d') . '] Reason: Buyer home loan rejected by HDFC and SBI.',
                    'created_by'         => $adminId,
                ]);

                $historyModel->recordChange($b4Id, null, 'Draft', $adminId, 'Draft created');
                $historyModel->recordChange($b4Id, 'Draft', 'Cancelled', $adminId, 'Loan rejection led to voluntary cancellation');
                $propHistModel->recordChange((int)($u4['property_id'] ?: 0), (int)$u4['id'], 'Booked', 'Available', $adminId, 'Unit released back to Available after cancellation of Booking #BK-2026-000004');
            }
        }

        echo "Phase 4 Real Estate Sales & Transactions demo data seeded successfully.\n";
    }
}
