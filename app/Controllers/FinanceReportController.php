<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\InvoiceModel;
use App\Models\RentDemandModel;
use App\Models\PaymentScheduleItemModel;
use App\Models\TdsEntryModel;
use App\Models\TdsCertificateModel;
use App\Models\CommissionModel;
use App\Models\AuditLogModel;
use App\Models\CompanyModel;

class FinanceReportController extends BaseController
{
    protected $invoiceModel;
    protected $rentDemandModel;
    protected $milestoneModel;
    protected $tdsModel;
    protected $certificateModel;
    protected $commissionModel;
    protected $auditModel;
    protected $companyModel;

    public function __construct()
    {
        $this->invoiceModel     = new InvoiceModel();
        $this->rentDemandModel  = new RentDemandModel();
        $this->milestoneModel   = new PaymentScheduleItemModel();
        $this->tdsModel         = new TdsEntryModel();
        $this->certificateModel = new TdsCertificateModel();
        $this->commissionModel  = new CommissionModel();
        $this->auditModel       = new AuditLogModel();
        $this->companyModel     = new CompanyModel();
    }

    /**
     * GST Statutory Filing Reports (GSTR-1 & GSTR-3B)
     */
    public function gst()
    {
        $month = $this->request->getGet('month') ?? date('Y-m');

        $db = \Config\Database::connect();

        // 1. Sales Invoices
        $invoices = $db->table('invoices')
            ->select('invoices.*, c.first_name, c.last_name, c.customer_code, b.booking_number')
            ->join('customers c', 'c.id = invoices.customer_id')
            ->join('bookings b', 'b.id = invoices.booking_id')
            ->like('invoices.invoice_date', $month, 'after')
            ->where('invoices.status !=', 'Cancelled')
            ->get()->getResultArray();

        // 2. Rent Demands
        $demands = $db->table('rent_demands')
            ->select('rent_demands.*, t.full_name as tenant_name, t.tenant_code')
            ->join('tenants t', 't.id = rent_demands.tenant_id')
            ->where('rent_demands.billing_period', $month)
            ->get()->getResultArray();

        // Calculate Totals
        $invoiceTaxable = 0;
        $invoiceTax = 0;
        foreach ($invoices as $inv) {
            $invoiceTaxable += (float)$inv['subtotal'];
            $invoiceTax     += (float)$inv['tax_amount'];
        }

        $rentTaxable = 0;
        $rentTax = 0;
        foreach ($demands as $d) {
            $rentTaxable += (float)$d['base_rent'] + (float)$d['maintenance'] + (float)$d['other_charges'];
            $rentTax     += (float)$d['tax'];
        }

        $totalTaxable = $invoiceTaxable + $rentTaxable;
        $totalTax     = $invoiceTax + $rentTax;
        $cgst         = round($totalTax / 2, 2);
        $sgst         = round($totalTax / 2, 2);

        $kpi = [
            'month'          => $month,
            'total_taxable'  => $totalTaxable,
            'total_tax'      => $totalTax,
            'cgst'           => $cgst,
            'sgst'           => $sgst,
            'invoice_count'  => count($invoices),
            'rent_demand_count' => count($demands),
        ];

        return view('reports/gst_report', [
            'title'    => 'GST Filing Reports (GSTR-1 & GSTR-3B) - Real Estate ERP',
            'month'    => $month,
            'invoices' => $invoices,
            'demands'  => $demands,
            'kpi'      => $kpi,
        ]);
    }

    /**
     * TDS Deductions & Form 16A Management
     */
    public function tds()
    {
        $quarter = trim($this->request->getGet('quarter') ?? '');
        $fy      = trim($this->request->getGet('financial_year') ?? '2026-2027');

        $builder = $this->tdsModel;
        if ($quarter !== '') {
            $builder->where('quarter', $quarter);
        }
        if ($fy !== '') {
            $builder->where('financial_year', $fy);
        }

        $entries = $builder->orderBy('id', 'DESC')->findAll();

        $certificates = $this->certificateModel->orderBy('id', 'DESC')->findAll();

        $db = \Config\Database::connect();
        $totalGross = $db->table('tds_entries')->selectSum('gross_amount')->get()->getRow()->gross_amount ?? 0;
        $totalTds   = $db->table('tds_entries')->selectSum('tds_amount')->get()->getRow()->tds_amount ?? 0;
        $totalNet   = $db->table('tds_entries')->selectSum('net_payable')->get()->getRow()->net_payable ?? 0;

        $kpi = [
            'total_gross' => (float)$totalGross,
            'total_tds'   => (float)$totalTds,
            'total_net'   => (float)$totalNet,
            'count'       => count($entries),
        ];

        $commissions = $this->commissionModel->where('status', 'Approved')->findAll();

        return view('reports/tds_report', [
            'title'        => 'TDS Management & Form 16A - Real Estate ERP',
            'entries'      => $entries,
            'certificates' => $certificates,
            'filters'      => ['quarter' => $quarter, 'financial_year' => $fy],
            'kpi'          => $kpi,
            'commissions'  => $commissions,
        ]);
    }

    /**
     * Store manual or commission TDS entry
     */
    public function storeTds()
    {
        $rules = [
            'party_name'     => 'required|min_length[3]|max_length[255]',
            'pan_number'     => 'required|regex_match[/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/]',
            'gross_amount'   => 'required|numeric|greater_than[0]',
            'tds_rate'       => 'required|numeric|greater_than_equal_to[0]',
            'deduction_date' => 'required|valid_date',
            'financial_year' => 'required',
            'quarter'        => 'required|in_list[Q1,Q2,Q3,Q4]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $gross = (float)$this->request->getPost('gross_amount');
        $rate  = (float)$this->request->getPost('tds_rate');
        $tds   = round($gross * ($rate / 100), 2);
        $net   = $gross - $tds;

        $code = $this->tdsModel->generateEntryCode();

        $data = [
            'entry_code'            => $code,
            'party_name'            => trim($this->request->getPost('party_name')),
            'pan_number'            => strtoupper(trim($this->request->getPost('pan_number'))),
            'section'               => $this->request->getPost('section') ?? '194H',
            'transaction_reference' => trim($this->request->getPost('transaction_reference') ?? 'Commission Payout'),
            'commission_id'         => $this->request->getPost('commission_id') ?: null,
            'gross_amount'          => $gross,
            'tds_rate'              => $rate,
            'tds_amount'            => $tds,
            'net_payable'           => $net,
            'deduction_date'        => $this->request->getPost('deduction_date'),
            'financial_year'        => $this->request->getPost('financial_year'),
            'quarter'               => $this->request->getPost('quarter'),
            'status'                => 'deducted',
        ];

        $entryId = $this->tdsModel->insert($data);

        $this->auditModel->record(
            session()->get('user_id'),
            'TDS_RECORDED',
            'Compliance',
            $entryId,
            "Recorded TDS entry {$code} for {$data['party_name']} (TDS: ₹{$tds})"
        );

        return redirect()->back()->with('success', "TDS entry {$code} recorded successfully.");
    }

    /**
     * Generate TDS Certificate (Form 16A)
     */
    public function generateCertificate(int $entryId)
    {
        $entry = $this->tdsModel->find($entryId);
        if (!$entry) {
            return redirect()->back()->with('error', 'TDS entry not found.');
        }

        $certNum = $this->certificateModel->generateCertificateNumber();

        $certData = [
            'certificate_number' => $certNum,
            'party_name'         => $entry['party_name'],
            'pan_number'         => $entry['pan_number'],
            'gross_amount'       => $entry['gross_amount'],
            'tds_amount'         => $entry['tds_amount'],
            'financial_year'     => $entry['financial_year'],
            'quarter'            => $entry['quarter'],
            'certificate_file'   => null,
            'issue_date'         => date('Y-m-d'),
            'created_at'         => date('Y-m-d H:i:s'),
        ];

        $certId = $this->certificateModel->insert($certData);
        $this->tdsModel->update($entryId, ['status' => 'certified']);

        $this->auditModel->record(
            session()->get('user_id'),
            'TDS_CERTIFICATE_ISSUED',
            'Compliance',
            $certId,
            "Issued Form 16A Certificate {$certNum} for {$entry['party_name']}"
        );

        return redirect()->back()->with('success', "Form 16A Certificate {$certNum} generated successfully.");
    }

    /**
     * Accounts Receivable & Ageing Analysis
     */
    public function receivables()
    {
        $today = date('Y-m-d');
        $db = \Config\Database::connect();

        // 1. Unpaid Sales Invoices
        $invoices = $db->table('invoices')
            ->select('invoices.*, c.first_name, c.last_name, c.customer_code, c.phone')
            ->join('customers c', 'c.id = invoices.customer_id')
            ->whereIn('invoices.status', ['Issued', 'Partially Paid', 'Overdue'])
            ->get()->getResultArray();

        // 2. Unpaid Rent Demands
        $demands = $db->table('rent_demands')
            ->select('rent_demands.*, t.full_name as tenant_name, t.tenant_code, t.mobile')
            ->join('tenants t', 't.id = rent_demands.tenant_id')
            ->whereIn('rent_demands.status', ['unpaid', 'partially_paid', 'overdue'])
            ->get()->getResultArray();

        // 3. Construction Milestones Overdue
        $milestones = $db->table('payment_schedule_items psi')
            ->select('psi.*, ps.booking_id, b.booking_number, c.first_name, c.last_name, c.customer_code')
            ->join('payment_schedules ps', 'ps.id = psi.payment_schedule_id')
            ->join('bookings b', 'b.id = ps.booking_id')
            ->join('customers c', 'c.id = b.customer_id')
            ->whereIn('psi.status', ['Pending', 'Partially Paid', 'Overdue'])
            ->get()->getResultArray();

        // Ageing Buckets
        $buckets = [
            'current' => 0.00,
            'days_30' => 0.00,
            'days_60' => 0.00,
            'days_90' => 0.00,
            'over_90' => 0.00,
        ];

        $allReceivables = [];

        // Classify Invoices
        foreach ($invoices as $inv) {
            $due = $inv['due_date'];
            $balance = (float)$inv['total_amount']; // simplified balance
            $diff = (strtotime($today) - strtotime($due)) / 86400;

            if ($diff <= 0) {
                $buckets['current'] += $balance;
                $bucket = 'Current';
            } elseif ($diff <= 30) {
                $buckets['days_30'] += $balance;
                $bucket = '1-30 Days';
            } elseif ($diff <= 60) {
                $buckets['days_60'] += $balance;
                $bucket = '31-60 Days';
            } elseif ($diff <= 90) {
                $buckets['days_90'] += $balance;
                $bucket = '61-90 Days';
            } else {
                $buckets['over_90'] += $balance;
                $bucket = '90+ Days';
            }

            $allReceivables[] = [
                'type'         => 'Sales Invoice',
                'reference'    => $inv['invoice_number'],
                'party_name'   => $inv['first_name'] . ' ' . $inv['last_name'] . ' (' . $inv['customer_code'] . ')',
                'contact'      => $inv['phone'],
                'due_date'     => $due,
                'days_overdue' => max(0, (int)$diff),
                'amount'       => $balance,
                'bucket'       => $bucket,
            ];
        }

        // Classify Rent Demands
        foreach ($demands as $dem) {
            $due = $dem['due_date'];
            $balance = (float)$dem['balance_amount'];
            $diff = (strtotime($today) - strtotime($due)) / 86400;

            if ($diff <= 0) {
                $buckets['current'] += $balance;
                $bucket = 'Current';
            } elseif ($diff <= 30) {
                $buckets['days_30'] += $balance;
                $bucket = '1-30 Days';
            } elseif ($diff <= 60) {
                $buckets['days_60'] += $balance;
                $bucket = '31-60 Days';
            } elseif ($diff <= 90) {
                $buckets['days_90'] += $balance;
                $bucket = '61-90 Days';
            } else {
                $buckets['over_90'] += $balance;
                $bucket = '90+ Days';
            }

            $allReceivables[] = [
                'type'         => 'Rent Demand',
                'reference'    => $dem['demand_number'],
                'party_name'   => $dem['tenant_name'] . ' (' . $dem['tenant_code'] . ')',
                'contact'      => $dem['mobile'],
                'due_date'     => $due,
                'days_overdue' => max(0, (int)$diff),
                'amount'       => $balance,
                'bucket'       => $bucket,
            ];
        }

        // Classify Milestones
        foreach ($milestones as $ms) {
            $due = $ms['due_date'] ?? $today;
            $balance = (float)$ms['remaining_amount'];
            $diff = (strtotime($today) - strtotime($due)) / 86400;

            if ($diff <= 0) {
                $buckets['current'] += $balance;
                $bucket = 'Current';
            } elseif ($diff <= 30) {
                $buckets['days_30'] += $balance;
                $bucket = '1-30 Days';
            } elseif ($diff <= 60) {
                $buckets['days_60'] += $balance;
                $bucket = '31-60 Days';
            } elseif ($diff <= 90) {
                $buckets['days_90'] += $balance;
                $bucket = '61-90 Days';
            } else {
                $buckets['over_90'] += $balance;
                $bucket = '90+ Days';
            }

            $allReceivables[] = [
                'type'         => 'Milestone (' . $ms['milestone_name'] . ')',
                'reference'    => $ms['booking_number'],
                'party_name'   => $ms['first_name'] . ' ' . $ms['last_name'] . ' (' . $ms['customer_code'] . ')',
                'contact'      => '—',
                'due_date'     => $due,
                'days_overdue' => max(0, (int)$diff),
                'amount'       => $balance,
                'bucket'       => $bucket,
            ];
        }

        // Sort by overdue days descending
        usort($allReceivables, fn($a, $b) => $b['days_overdue'] <=> $a['days_overdue']);

        $totalReceivables = array_sum($buckets);

        return view('reports/receivables_ageing', [
            'title'            => 'Accounts Receivable & Ageing Analysis - Real Estate ERP',
            'buckets'          => $buckets,
            'totalReceivables' => $totalReceivables,
            'receivables'      => $allReceivables,
        ]);
    }
}
