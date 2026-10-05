<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Phase7CompletenessSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        // 1. Expense Categories
        $categories = [
            ['name' => 'Property Maintenance',       'code' => 'EXP-CAT-MAINT',  'description' => 'Routine building and facility maintenance, lift AMCs, landscaping'],
            ['name' => 'Repair & Renovation',        'code' => 'EXP-CAT-REPAIR', 'description' => 'Plumbing, civil repairs, waterproofing, painting and restoration'],
            ['name' => 'Property Tax & Municipal',   'code' => 'EXP-CAT-TAX',    'description' => 'Municipal property tax, land assessment charges, statutory water tax'],
            ['name' => 'Utilities & Energy',         'code' => 'EXP-CAT-UTIL',   'description' => 'Electricity bills, DG diesel fuel, water tanker supply'],
            ['name' => 'Marketing & Advertising',     'code' => 'EXP-CAT-MKT',    'description' => 'Online portal listings, social media ads, brochures, hoarding displays'],
            ['name' => 'Legal, Title & Audit',       'code' => 'EXP-CAT-LEGAL',  'description' => 'Legal opinion, title searches, RERA compliance certification, audit fees'],
            ['name' => 'Insurance Premiums',         'code' => 'EXP-CAT-INSUR',  'description' => 'Building fire insurance, public liability, equipment breakdown cover'],
            ['name' => 'Security & Housekeeping',    'code' => 'EXP-CAT-SEC',    'description' => 'Manned security guard agency, housekeeping materials and janitorial'],
        ];

        foreach ($categories as $cat) {
            $existing = $db->table('expense_categories')->where('code', $cat['code'])->get()->getRowArray();
            if (!$existing) {
                $cat['status'] = 'active';
                $cat['created_at'] = $now;
                $cat['updated_at'] = $now;
                $db->table('expense_categories')->insert($cat);
            }
        }

        // 2. Property Owners
        $owners = [
            [
                'owner_code'          => 'OWN-2026-000001',
                'first_name'          => 'Rajeshwar',
                'last_name'           => 'Rao',
                'company_name'        => 'Rajeshwar Capital Holdings Ltd',
                'email'               => 'rajeshwar.rao@investor-holdings.in',
                'phone'               => '+91 98201 12345',
                'alternate_phone'     => '+91 98201 12346',
                'address'             => 'Penthouse 14, Silver Crest Towers, Worli',
                'city'                => 'Mumbai',
                'state'               => 'Maharashtra',
                'pincode'             => '400018',
                'pan_number'          => 'ABCPR1234F',
                'aadhaar_number'      => '9876-5432-1098',
                'kyc_status'          => 'verified',
                'bank_name'           => 'HDFC Bank Ltd',
                'bank_account_number' => '50100234567890',
                'bank_ifsc'           => 'HDFC0000060',
                'status'              => 'active',
                'notes'               => 'Primary individual landlord and investor holding commercial and residential portfolio',
            ],
            [
                'owner_code'          => 'OWN-2026-000002',
                'first_name'          => 'Anita',
                'last_name'           => 'Singhania',
                'company_name'        => 'Apex Prime Realty LLP',
                'email'               => 'anita.singhania@apexrealty.org',
                'phone'               => '+91 98302 23456',
                'alternate_phone'     => '+91 98302 23457',
                'address'             => 'Bungalow 4, Golf Course Road, DLF Phase 5',
                'city'                => 'Gurugram',
                'state'               => 'Haryana',
                'pincode'             => '122002',
                'pan_number'          => 'BKAPS5678G',
                'aadhaar_number'      => '8765-4321-2109',
                'kyc_status'          => 'verified',
                'bank_name'           => 'ICICI Bank Ltd',
                'bank_account_number' => '000405012345',
                'bank_ifsc'           => 'ICIC0000004',
                'status'              => 'active',
                'notes'               => 'Co-investor in high-rise residential projects and premium suites',
            ],
            [
                'owner_code'          => 'OWN-2026-000003',
                'first_name'          => 'Vikramaditya',
                'last_name'           => 'Mehta',
                'company_name'        => 'Mehta Family Heritage Trust',
                'email'               => 'vikram.mehta@heritage-estate.in',
                'phone'               => '+91 98403 34567',
                'alternate_phone'     => '+91 98403 34568',
                'address'             => 'Heritage Villa, Jubilee Hills Road No. 36',
                'city'                => 'Hyderabad',
                'state'               => 'Telangana',
                'pincode'             => '500033',
                'pan_number'          => 'CLVPM9012H',
                'aadhaar_number'      => '7654-3210-3210',
                'kyc_status'          => 'verified',
                'bank_name'           => 'State Bank of India',
                'bank_account_number' => '30012345678',
                'bank_ifsc'           => 'SBIN0004123',
                'status'              => 'active',
                'notes'               => 'Owner of commercial retail strip and prime IT towers',
            ],
        ];

        foreach ($owners as $owner) {
            $existing = $db->table('property_owners')->where('owner_code', $owner['owner_code'])->get()->getRowArray();
            if (!$existing) {
                $owner['created_at'] = $now;
                $owner['updated_at'] = $now;
                $db->table('property_owners')->insert($owner);
            }
        }

        // Link properties to owners
        $owner1 = $db->table('property_owners')->where('owner_code', 'OWN-2026-000001')->get()->getRowArray();
        $owner2 = $db->table('property_owners')->where('owner_code', 'OWN-2026-000002')->get()->getRowArray();
        $owner3 = $db->table('property_owners')->where('owner_code', 'OWN-2026-000003')->get()->getRowArray();

        if ($owner1 && $db->tableExists('properties')) {
            $db->table('properties')->where('id', 1)->update(['owner_id' => $owner1['id']]);
        }
        if ($owner2 && $db->tableExists('properties')) {
            $db->table('properties')->where('id', 2)->update(['owner_id' => $owner2['id']]);
        }
        if ($owner3 && $db->tableExists('properties')) {
            $db->table('properties')->where('id', 3)->update(['owner_id' => $owner3['id']]);
        }

        // 3. Agents / Brokers
        $agents = [
            [
                'agent_code'      => 'AGT-2026-000001',
                'agent_type'      => 'External Broker',
                'agency_name'     => 'Prime Realtors Advisory LLP',
                'first_name'      => 'Rajesh',
                'last_name'       => 'Sharma',
                'email'           => 'rajesh.sharma@primerealtors.com',
                'phone'           => '+91 98111 22334',
                'license_number'  => 'MAHARERA-A51800012345',
                'pan_number'      => 'AAAPR1234A',
                'commission_rate' => 2.00,
                'status'          => 'active',
                'address'         => 'Suite 401, Nariman Point Business Centre',
                'city'            => 'Mumbai',
                'notes'           => 'Top performing residential channel partner specializing in high-value apartment sales',
            ],
            [
                'agent_code'      => 'AGT-2026-000002',
                'agent_type'      => 'Channel Partner',
                'agency_name'     => 'Metro Realty Network',
                'first_name'      => 'Priya',
                'last_name'       => 'Kapoor',
                'email'           => 'priya.kapoor@metrorealty.in',
                'phone'           => '+91 98222 33445',
                'license_number'  => 'HARERA-PK-2024-889',
                'pan_number'      => 'BBBPK5678B',
                'commission_rate' => 2.50,
                'status'          => 'active',
                'address'         => 'Floor 2, Cyber Hub Tower B',
                'city'            => 'Gurugram',
                'notes'           => 'Key corporate institutional channel partner with high sales conversion rates',
            ],
            [
                'agent_code'      => 'AGT-2026-000003',
                'agent_type'      => 'Internal Agent',
                'agency_name'     => 'Direct In-House Advisory',
                'first_name'      => 'Amit',
                'last_name'       => 'Verma',
                'email'           => 'amit.verma@realestate-erp.local',
                'phone'           => '+91 98333 44556',
                'license_number'  => 'INTERNAL-SALES-003',
                'pan_number'      => 'CCCAM9012C',
                'commission_rate' => 1.50,
                'status'          => 'active',
                'address'         => 'Branch HQ Office, Bandra Kurla Complex',
                'city'            => 'Mumbai',
                'notes'           => 'Senior in-house property advisor handling direct walk-ins and developer inventory',
            ],
            [
                'agent_code'      => 'AGT-2026-000004',
                'agent_type'      => 'External Broker',
                'agency_name'     => 'Skyline Luxury Properties',
                'first_name'      => 'Sunita',
                'last_name'       => 'Desai',
                'email'           => 'sunita.desai@skylineprops.com',
                'phone'           => '+91 98444 55667',
                'license_number'  => 'GUJRERA-SD-2025-442',
                'pan_number'      => 'DDESD3456D',
                'commission_rate' => 2.00,
                'status'          => 'active',
                'address'         => 'Office 12, Sindhu Bhavan Road',
                'city'            => 'Ahmedabad',
                'notes'           => 'Experienced broker for commercial office leases and industrial park spaces',
            ],
        ];

        foreach ($agents as $agent) {
            $existing = $db->table('agents')->where('agent_code', $agent['agent_code'])->get()->getRowArray();
            if (!$existing) {
                $agent['created_at'] = $now;
                $agent['updated_at'] = $now;
                $db->table('agents')->insert($agent);
            }
        }

        // 4. Property Expenses
        $catMaint = $db->table('expense_categories')->where('code', 'EXP-CAT-MAINT')->get()->getRowArray();
        $catTax   = $db->table('expense_categories')->where('code', 'EXP-CAT-TAX')->get()->getRowArray();
        $catUtil  = $db->table('expense_categories')->where('code', 'EXP-CAT-UTIL')->get()->getRowArray();
        $catRepair= $db->table('expense_categories')->where('code', 'EXP-CAT-REPAIR')->get()->getRowArray();
        $catMkt   = $db->table('expense_categories')->where('code', 'EXP-CAT-MKT')->get()->getRowArray();

        $expenses = [
            [
                'expense_code'    => 'EXP-2026-000001',
                'category_id'     => $catMaint['id'] ?? 1,
                'property_id'     => 1,
                'project_id'      => 1,
                'title'           => 'Elevators Comprehensive Annual AMC',
                'expense_date'    => '2026-09-15',
                'payee_vendor'    => 'Otis Elevator Company India Ltd',
                'amount'          => 45000.00,
                'tax_amount'      => 8100.00,
                'total_amount'    => 53100.00,
                'payment_method'  => 'Bank Transfer',
                'status'          => 'Paid',
                'notes'           => 'Q3 and Q4 comprehensive elevator inspection and servicing',
            ],
            [
                'expense_code'    => 'EXP-2026-000002',
                'category_id'     => $catTax['id'] ?? 3,
                'property_id'     => 1,
                'project_id'      => 1,
                'title'           => 'Annual Municipal Property Tax Assessment FY 2026-27',
                'expense_date'    => '2026-09-20',
                'payee_vendor'    => 'Brihanmumbai Municipal Corporation (BMC)',
                'amount'          => 125000.00,
                'tax_amount'      => 0.00,
                'total_amount'    => 125000.00,
                'payment_method'  => 'RTGS',
                'status'          => 'Paid',
                'notes'           => 'Annual statutory property tax clearance receipt challan issued',
            ],
            [
                'expense_code'    => 'EXP-2026-000003',
                'category_id'     => $catUtil['id'] ?? 4,
                'property_id'     => 2,
                'project_id'      => 2,
                'title'           => 'Emergency DG Diesel Fuel Supply & Filtration',
                'expense_date'    => '2026-09-25',
                'payee_vendor'    => 'Bharat Petroleum Bulk Supply',
                'amount'          => 28500.00,
                'tax_amount'      => 5130.00,
                'total_amount'    => 33630.00,
                'payment_method'  => 'Bank Transfer',
                'status'          => 'Paid',
                'notes'           => '2000 Liters DG fuel replenishment for backup generator banks',
            ],
            [
                'expense_code'    => 'EXP-2026-000004',
                'category_id'     => $catRepair['id'] ?? 2,
                'property_id'     => 2,
                'project_id'      => 2,
                'title'           => 'External Podium Waterproofing & Joint Sealant Touch-up',
                'expense_date'    => '2026-10-01',
                'payee_vendor'    => 'Apex Waterproofing Solutions',
                'amount'          => 38000.00,
                'tax_amount'      => 6840.00,
                'total_amount'    => 44840.00,
                'payment_method'  => 'Cheque',
                'status'          => 'Paid',
                'notes'           => 'Monsoon pre-emptive podium joint resealing with 2-year warranty',
            ],
            [
                'expense_code'    => 'EXP-2026-000005',
                'category_id'     => $catMkt['id'] ?? 5,
                'property_id'     => 1,
                'project_id'      => 1,
                'title'           => 'Property Portals Premium Featured Showcase Campaigns',
                'expense_date'    => '2026-10-02',
                'payee_vendor'    => 'MagicBricks & 99acres Marketing',
                'amount'          => 50000.00,
                'tax_amount'      => 9000.00,
                'total_amount'    => 59000.00,
                'payment_method'  => 'Bank Transfer',
                'status'          => 'Approved',
                'notes'           => 'Top of page spotlight placement for unsold 3BHK and 4BHK units',
            ],
        ];

        foreach ($expenses as $exp) {
            $existing = $db->table('property_expenses')->where('expense_code', $exp['expense_code'])->get()->getRowArray();
            if (!$existing) {
                $exp['created_at'] = $now;
                $exp['updated_at'] = $now;
                $db->table('property_expenses')->insert($exp);
            }
        }

        // 5. Marketing Campaigns
        $campaigns = [
            [
                'campaign_code'   => 'CMP-2026-000001',
                'name'            => 'Prestige Skyrise Festive Launch - Meta Ads',
                'campaign_type'   => 'Social Media',
                'project_id'      => 1,
                'property_id'     => 1,
                'start_date'      => '2026-09-01',
                'end_date'        => '2026-10-31',
                'budget'          => 150000.00,
                'actual_spend'    => 112500.00,
                'leads_generated' => 84,
                'qualified_leads' => 28,
                'converted_leads' => 6,
                'status'          => 'Active',
                'description'     => 'Hyper-targeted Instagram and Facebook carousel campaigns targeting high-net-worth buyers in Bandra and Worli.',
                'notes'           => 'Strong conversion on 3BHK Luxury formats; cost per lead at approx ₹1,339',
            ],
            [
                'campaign_code'   => 'CMP-2026-000002',
                'name'            => 'Google High-Intent Search Ads - Corporate IT Park',
                'campaign_type'   => 'Google Ads',
                'project_id'      => 2,
                'property_id'     => 2,
                'start_date'      => '2026-08-15',
                'end_date'        => '2026-10-15',
                'budget'          => 200000.00,
                'actual_spend'    => 178000.00,
                'leads_generated' => 65,
                'qualified_leads' => 24,
                'converted_leads' => 5,
                'status'          => 'Active',
                'description'     => 'High-intent search keyword campaigns covering terms like "Grade A office space for lease" and "commercial IT units".',
                'notes'           => 'Qualified corporate inquiries from multinational tenants; solid CPL',
            ],
            [
                'campaign_code'   => 'CMP-2026-000003',
                'name'            => 'MagicBricks Platinum Showcase & Banner Ads',
                'campaign_type'   => 'Property Portal',
                'project_id'      => 1,
                'property_id'     => 1,
                'start_date'      => '2026-07-01',
                'end_date'        => '2026-08-31',
                'budget'          => 120000.00,
                'actual_spend'    => 120000.00,
                'leads_generated' => 58,
                'qualified_leads' => 19,
                'converted_leads' => 4,
                'status'          => 'Completed',
                'description'     => 'Prime banner real estate on MagicBricks portal with automated lead sync into CRM.',
                'notes'           => 'Completed successfully with 4 bookings finalized.',
            ],
            [
                'campaign_code'   => 'CMP-2026-000004',
                'name'            => 'Western Express Highway Airport Corridor Billboards',
                'campaign_type'   => 'Hoarding / Outdoor',
                'project_id'      => 1,
                'property_id'     => 1,
                'start_date'      => '2026-08-01',
                'end_date'        => '2026-09-30',
                'budget'          => 350000.00,
                'actual_spend'    => 350000.00,
                'leads_generated' => 42,
                'qualified_leads' => 14,
                'converted_leads' => 3,
                'status'          => 'Completed',
                'description'     => 'Dual LED illuminated billboards adjacent to Mumbai Airport Terminal 2 approach road.',
                'notes'           => 'Excellent brand recall and drive-in site visit walk-ins generated.',
            ],
        ];

        foreach ($campaigns as $camp) {
            $existing = $db->table('marketing_campaigns')->where('campaign_code', $camp['campaign_code'])->get()->getRowArray();
            if (!$existing) {
                $camp['created_at'] = $now;
                $camp['updated_at'] = $now;
                $db->table('marketing_campaigns')->insert($camp);
            }
        }

        // 6. Property Documents (Legal & Compliance)
        $documents = [
            [
                'document_code'       => 'DOC-2026-000001',
                'title'               => 'Registered Sale Title Deed & Conveyance Certificate',
                'document_category'   => 'Ownership / Title Deed',
                'property_id'         => 1,
                'project_id'          => 1,
                'owner_id'            => 1,
                'file_path'           => 'uploads/documents/title_deed_prestige.pdf',
                'file_name'           => 'Title_Deed_Prestige_Registered.pdf',
                'file_size'           => 2450000,
                'mime_type'           => 'application/pdf',
                'issue_date'          => '2024-03-15',
                'verification_status' => 'Verified',
                'verified_by'         => 1,
                'verified_at'         => '2026-09-01 10:00:00',
                'notes'               => 'Registered at Sub-Registrar Office, Mumbai. Title clear without lien.',
            ],
            [
                'document_code'       => 'DOC-2026-000002',
                'title'               => 'Municipal Corporation Sanctioned Building Plan',
                'document_category'   => 'Building Approval Plan',
                'property_id'         => 1,
                'project_id'          => 1,
                'file_path'           => 'uploads/documents/sanctioned_plan_prestige.pdf',
                'file_name'           => 'Sanctioned_Plan_Tower_A_B.pdf',
                'file_size'           => 8900000,
                'mime_type'           => 'application/pdf',
                'issue_date'          => '2024-01-20',
                'verification_status' => 'Verified',
                'verified_by'         => 1,
                'verified_at'         => '2026-09-01 10:30:00',
                'notes'               => 'Approved by Chief Town Planner; FAR/FSI utilization verified.',
            ],
            [
                'document_code'       => 'DOC-2026-000003',
                'title'               => 'Fire Safety Statutory NOC Clearance',
                'document_category'   => 'NOC Clearance',
                'property_id'         => 1,
                'project_id'          => 1,
                'file_path'           => 'uploads/documents/fire_noc_2026.pdf',
                'file_name'           => 'Fire_NOC_Annual_Clearance.pdf',
                'file_size'           => 1200000,
                'mime_type'           => 'application/pdf',
                'issue_date'          => '2026-04-01',
                'expiry_date'         => '2027-03-31',
                'verification_status' => 'Verified',
                'verified_by'         => 1,
                'verified_at'         => '2026-09-02 11:00:00',
                'notes'               => 'Annual fire sprinkler and riser hydrant test clearance certificate.',
            ],
            [
                'document_code'       => 'DOC-2026-000004',
                'title'               => 'Statutory Occupancy Certificate (OC)',
                'document_category'   => 'Occupancy Certificate',
                'property_id'         => 1,
                'project_id'          => 1,
                'file_path'           => 'uploads/documents/occupancy_certificate.pdf',
                'file_name'           => 'Final_OC_Prestige_Highline.pdf',
                'file_size'           => 3100000,
                'mime_type'           => 'application/pdf',
                'issue_date'          => '2026-06-10',
                'verification_status' => 'Verified',
                'verified_by'         => 1,
                'verified_at'         => '2026-09-02 11:30:00',
                'notes'               => 'Complete building occupancy granted with zero pending structural snags.',
            ],
            [
                'document_code'       => 'DOC-2026-000005',
                'title'               => 'RERA Project Registration Certificate',
                'document_category'   => 'RERA Certificate',
                'property_id'         => 2,
                'project_id'          => 2,
                'file_path'           => 'uploads/documents/rera_registration.pdf',
                'file_name'           => 'RERA_Registration_Certificate.pdf',
                'file_size'           => 1450000,
                'mime_type'           => 'application/pdf',
                'issue_date'          => '2025-01-01',
                'expiry_date'         => '2028-12-31',
                'verification_status' => 'Verified',
                'verified_by'         => 1,
                'verified_at'         => '2026-09-03 14:00:00',
                'notes'               => 'RERA approved project registration certificate; quarterly audits compliant.',
            ],
            [
                'document_code'       => 'DOC-2026-000006',
                'title'               => '30-Year Non-Encumbrance Search Report',
                'document_category'   => 'Encumbrance Certificate',
                'property_id'         => 2,
                'project_id'          => 2,
                'file_path'           => 'uploads/documents/encumbrance_cert.pdf',
                'file_name'           => 'Encumbrance_30Year_Search.pdf',
                'file_size'           => 4200000,
                'mime_type'           => 'application/pdf',
                'issue_date'          => '2026-08-20',
                'verification_status' => 'Under Review',
                'notes'               => 'Submitted to senior legal panel for final validation.',
            ],
        ];

        foreach ($documents as $doc) {
            $existing = $db->table('property_documents')->where('document_code', $doc['document_code'])->get()->getRowArray();
            if (!$existing) {
                $doc['created_at'] = $now;
                $doc['updated_at'] = $now;
                $db->table('property_documents')->insert($doc);
            }
        }

        // 7. Property Verifications (Compliance Audits)
        $verifications = [
            [
                'verification_code' => 'VER-2026-000001',
                'property_id'       => 1,
                'verification_type' => 'Legal Title Clearance',
                'status'            => 'Verified',
                'assigned_to'       => 1,
                'findings'          => 'Clear 30-year unbroken chain of title verified. Zero encumbrances, registered in favor of the developer.',
                'checklist_data'    => json_encode([
                    'title_deed_verified'        => true,
                    'non_encumbrance_verified'   => true,
                    'tax_receipts_cleared'       => true,
                    'litigation_search_clear'    => true,
                ]),
                'verified_by'       => 1,
                'verified_at'       => '2026-09-10 12:00:00',
            ],
            [
                'verification_code' => 'VER-2026-000002',
                'property_id'       => 1,
                'verification_type' => 'RERA Compliance Check',
                'status'            => 'Verified',
                'assigned_to'       => 1,
                'findings'          => 'RERA registration active. Escrow bank account compliant. Quarterly progress filings updated with authority.',
                'checklist_data'    => json_encode([
                    'rera_number_active'         => true,
                    'escrow_account_reconciled'  => true,
                    'quarterly_filings_current'  => true,
                    'advertisement_rera_labeled' => true,
                ]),
                'verified_by'       => 1,
                'verified_at'       => '2026-09-11 15:30:00',
            ],
            [
                'verification_code' => 'VER-2026-000003',
                'property_id'       => 2,
                'verification_type' => 'Physical Property Audit',
                'status'            => 'Verified',
                'assigned_to'       => 1,
                'findings'          => 'Setback distances, structural fire exits and boundary demarcations verified on site matching approved plans.',
                'checklist_data'    => json_encode([
                    'setback_compliance'         => true,
                    'boundary_markers_verified'  => true,
                    'fire_tender_movement_clear' => true,
                    'parking_slot_allotments'    => true,
                ]),
                'verified_by'       => 1,
                'verified_at'       => '2026-09-15 11:00:00',
            ],
            [
                'verification_code' => 'VER-2026-000004',
                'property_id'       => 2,
                'verification_type' => 'Municipal Approval Check',
                'status'            => 'Under Review',
                'assigned_to'       => 1,
                'findings'          => 'Sanctioned architectural drawing matches foundation footprint. Awaiting final drainage connection certificate.',
                'checklist_data'    => json_encode([
                    'building_plan_sanctioned'   => true,
                    'plinth_certificate_issued'  => true,
                    'drainage_clearance'         => false,
                    'tree_plantation_noc'        => true,
                ]),
            ],
        ];

        foreach ($verifications as $ver) {
            $existing = $db->table('property_verifications')->where('verification_code', $ver['verification_code'])->get()->getRowArray();
            if (!$existing) {
                $ver['created_at'] = $now;
                $ver['updated_at'] = $now;
                $db->table('property_verifications')->insert($ver);
            }
        }

        // 8. Notifications
        $notifications = [
            [
                'title'          => 'New High-Value Lead Ingested',
                'message'        => 'Lead LEAD-2026-000001 (Rohan Mehra) requested callback for 4BHK Penthouse.',
                'type'           => 'lead',
                'priority'       => 'high',
                'related_module' => 'leads',
                'related_id'     => 1,
                'link_url'       => '/leads',
                'is_read'        => 0,
            ],
            [
                'title'          => 'Site Visit Scheduled for Today',
                'message'        => 'Site Visit SV-2026-000001 scheduled with Vikram Malhotra at 03:00 PM.',
                'type'           => 'site_visit',
                'priority'       => 'urgent',
                'related_module' => 'site-visits',
                'related_id'     => 1,
                'link_url'       => '/site-visits',
                'is_read'        => 0,
            ],
            [
                'title'          => 'Upcoming Payment Milestone Due',
                'message'        => 'Construction installment #3 due for Booking BK-2026-000001 (₹4,714,500).',
                'type'           => 'payment_due',
                'priority'       => 'high',
                'related_module' => 'payments',
                'related_id'     => 1,
                'link_url'       => '/payments',
                'is_read'        => 0,
            ],
            [
                'title'          => 'Monthly Rent Demand Generated',
                'message'        => 'Rent demand advice RNT-2026-000001 issued to Tenant TEN-2026-000001 for ₹90,000.',
                'type'           => 'rent_due',
                'priority'       => 'medium',
                'related_module' => 'rent-demands',
                'related_id'     => 1,
                'link_url'       => '/rent-demands',
                'is_read'        => 0,
            ],
            [
                'title'          => 'Fire Safety NOC Expiring Soon',
                'message'        => 'Compliance document DOC-2026-000003 (Fire NOC) expires in 180 days. Renewal inspection required.',
                'type'           => 'document_expiry',
                'priority'       => 'medium',
                'related_module' => 'documents',
                'related_id'     => 3,
                'link_url'       => '/documents',
                'is_read'        => 0,
            ],
            [
                'title'          => 'Maintenance Work Order Assigned',
                'message'        => 'Ticket MR-2026-000001 assigned to senior HVAC engineering team for inspection.',
                'type'           => 'maintenance',
                'priority'       => 'medium',
                'related_module' => 'maintenance',
                'related_id'     => 1,
                'link_url'       => '/maintenance',
                'is_read'        => 1,
                'read_at'        => $now,
            ],
        ];

        foreach ($notifications as $notif) {
            $existing = $db->table('notifications')->where('title', $notif['title'])->get()->getRowArray();
            if (!$existing) {
                $notif['created_at'] = $now;
                $db->table('notifications')->insert($notif);
            }
        }

        // 9. Customer Communications
        $comms = [
            [
                'comm_code'          => 'COM-2026-000001',
                'customer_id'        => 1,
                'lead_id'            => 1,
                'channel'            => 'Phone Call',
                'purpose'            => 'Follow-up',
                'subject'            => 'Discussion on Penthouse consideration and parking allotment',
                'content'            => 'Conducted detailed telephonic consultation explaining payment schedule milestones and special token advance terms. Customer requested updated draft agreement.',
                'status'             => 'Completed',
                'communication_date' => '2026-09-28 11:30:00',
                'user_id'            => 1,
                'response_notes'     => 'Customer satisfied with pricing; agreed to schedule family site visit.',
            ],
            [
                'comm_code'          => 'COM-2026-000002',
                'customer_id'        => 1,
                'channel'            => 'WhatsApp',
                'purpose'            => 'Booking Confirmation',
                'subject'            => 'Booking Confirmation & Official Payment Receipt Shared',
                'content'            => 'Transmitted official booking receipt and welcome packet PDF via official WhatsApp business channel.',
                'status'             => 'Delivered',
                'communication_date' => '2026-10-01 14:15:00',
                'user_id'            => 1,
                'response_notes'     => 'Receipt acknowledged with thumbs up.',
            ],
            [
                'comm_code'          => 'COM-2026-000003',
                'customer_id'        => 1,
                'channel'            => 'Email',
                'purpose'            => 'Payment Reminder',
                'subject'            => 'Upcoming Construction Milestone Notice - Plinth Completion',
                'content'            => 'Formal demand letter sent notifying allottee of slab milestone clearance and invoice INV-2026-000001 due date.',
                'status'             => 'Sent',
                'communication_date' => '2026-10-02 09:45:00',
                'user_id'            => 1,
                'response_notes'     => 'Email opened and delivered.',
            ],
            [
                'comm_code'          => 'COM-2026-000004',
                'customer_id'        => 1,
                'channel'            => 'In-Person Meeting',
                'purpose'            => 'Agreement Execution',
                'subject'            => 'Sales Agreement Signing and Document Franking Session',
                'content'            => 'Met customer at branch office for formal agreement execution with corporate legal officer and witnesses.',
                'status'             => 'Completed',
                'communication_date' => '2026-10-03 16:00:00',
                'user_id'            => 1,
                'response_notes'     => 'All 3 copies signed and sealed.',
            ],
        ];

        foreach ($comms as $c) {
            $existing = $db->table('customer_communications')->where('comm_code', $c['comm_code'])->get()->getRowArray();
            if (!$existing) {
                $c['created_at'] = $now;
                $c['updated_at'] = $now;
                $db->table('customer_communications')->insert($c);
            }
        }

        // 10. System Settings
        $settings = [
            ['setting_group' => 'general',      'setting_key' => 'app_name',                    'setting_value' => 'Enterprise Real Estate ERP & CRM', 'setting_type' => 'string',  'description' => 'System application display title'],
            ['setting_group' => 'general',      'setting_key' => 'default_currency',            'setting_value' => 'INR',                             'setting_type' => 'string',  'description' => 'Default system accounting currency'],
            ['setting_group' => 'general',      'setting_key' => 'currency_symbol',             'setting_value' => '₹',                               'setting_type' => 'string',  'description' => 'Currency symbol for display'],
            ['setting_group' => 'general',      'setting_key' => 'date_format',                 'setting_value' => 'Y-m-d',                           'setting_type' => 'string',  'description' => 'Default system date display format'],
            ['setting_group' => 'general',      'setting_key' => 'timezone',                    'setting_value' => 'Asia/Kolkata',                    'setting_type' => 'string',  'description' => 'Server business operating timezone'],
            ['setting_group' => 'company',      'setting_key' => 'company_tax_id',              'setting_value' => '27AAACP0123P1Z5',                 'setting_type' => 'string',  'description' => 'Corporate GSTIN tax identification'],
            ['setting_group' => 'company',      'setting_key' => 'rera_registration',           'setting_value' => 'MAHARERA-P51800045678',           'setting_type' => 'string',  'description' => 'Developer Master RERA registration'],
            ['setting_group' => 'property',     'setting_key' => 'default_area_unit',           'setting_value' => 'Sq.Ft.',                          'setting_type' => 'string',  'description' => 'Standard architectural area measurement unit'],
            ['setting_group' => 'property',     'setting_key' => 'auto_sync_unit_status',       'setting_value' => '1',                               'setting_type' => 'boolean', 'description' => 'Automatically synchronize unit availability upon booking or lease'],
            ['setting_group' => 'property',     'setting_key' => 'unit_hold_timeout_hours',     'setting_value' => '72',                              'setting_type' => 'integer', 'description' => 'Maximum temporary unit hold token reservation validity in hours'],
            ['setting_group' => 'payment',      'setting_key' => 'standard_gst_rate',           'setting_value' => '18.00',                           'setting_type' => 'decimal', 'description' => 'Standard Goods & Services Tax percentage for commercial properties'],
            ['setting_group' => 'payment',      'setting_key' => 'payment_grace_period_days',   'setting_value' => '7',                               'setting_type' => 'integer', 'description' => 'Grace period in days before late payment penalties are assessed'],
            ['setting_group' => 'payment',      'setting_key' => 'late_fee_daily_percentage',   'setting_value' => '0.05',                            'setting_type' => 'decimal', 'description' => 'Daily overdue penalty assessment rate percentage'],
            ['setting_group' => 'notification', 'setting_key' => 'lead_assignment_alert',       'setting_value' => '1',                               'setting_type' => 'boolean', 'description' => 'Notify executive upon CRM lead assignment'],
            ['setting_group' => 'notification', 'setting_key' => 'overdue_payment_reminder_days','setting_value' => '3',                               'setting_type' => 'integer', 'description' => 'Days prior to payment milestone due date to trigger notifications'],
            ['setting_group' => 'document',     'setting_key' => 'max_upload_size_mb',          'setting_value' => '25',                              'setting_type' => 'integer', 'description' => 'Maximum allowed file upload size in megabytes'],
            ['setting_group' => 'document',     'setting_key' => 'expiry_alert_threshold_days', 'setting_value' => '30',                              'setting_type' => 'integer', 'description' => 'Days before document expiration to flag critical review alerts'],
            ['setting_group' => 'backup',       'setting_key' => 'auto_backup_enabled',         'setting_value' => '1',                               'setting_type' => 'boolean', 'description' => 'Automated nightly database snapshots enabled'],
            ['setting_group' => 'backup',       'setting_key' => 'backup_retention_days',       'setting_value' => '30',                              'setting_type' => 'integer', 'description' => 'Days to retain system database backups before rotation'],
        ];

        foreach ($settings as $set) {
            $existing = $db->table('system_settings')->where('setting_key', $set['setting_key'])->get()->getRowArray();
            if (!$existing) {
                $set['created_at'] = $now;
                $set['updated_at'] = $now;
                $db->table('system_settings')->insert($set);
            }
        }
    }
}
