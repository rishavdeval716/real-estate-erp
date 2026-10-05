<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table            = 'customers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'customer_code',
        'lead_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'alternate_phone',
        'address',
        'city',
        'state',
        'pincode',
        'id_proof_type',
        'id_proof_number',
        'kyc_status',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Generate sequential unique customer code: CUS-2026-000001
     */
    public function generateCustomerCode(): string
    {
        $year = date('Y');
        $prefix = "CUS-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('customer_code');
        $builder->like('customer_code', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['customer_code'])) {
            $parts = explode('-', $last['customer_code']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return sprintf('%s%06d', $prefix, $nextSeq);
    }

    /**
     * Get customers with filters, bookings counts, and outstanding metrics
     */
    public function getCustomersWithDetails(array $filters = [], int $perPage = 15): array
    {
        $builder = $this->select('customers.*, 
            leads.lead_code,
            (SELECT COUNT(*) FROM bookings WHERE bookings.customer_id = customers.id AND bookings.deleted_at IS NULL) as total_bookings,
            (SELECT COALESCE(SUM(final_amount), 0) FROM bookings WHERE bookings.customer_id = customers.id AND bookings.booking_status IN ("Confirmed", "Completed") AND bookings.deleted_at IS NULL) as total_purchase_value,
            (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payments.customer_id = customers.id AND payments.status = "Received") as total_paid_amount')
            ->join('leads', 'leads.id = customers.lead_id', 'left')
            ->where('customers.deleted_at', null);

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $builder->groupStart()
                ->like('customers.customer_code', $s)
                ->orLike('customers.first_name', $s)
                ->orLike('customers.last_name', $s)
                ->orLike('customers.phone', $s)
                ->orLike('customers.email', $s)
                ->orLike('customers.city', $s)
                ->groupEnd();
        }

        if (!empty($filters['kyc_status'])) {
            $builder->where('customers.kyc_status', $filters['kyc_status']);
        }

        if (!empty($filters['status'])) {
            $builder->where('customers.status', $filters['status']);
        }

        $sort = $filters['sort'] ?? 'customers.created_at';
        $order = $filters['order'] ?? 'DESC';
        $builder->orderBy($sort, $order);

        $customers = $builder->paginate($perPage);

        return [
            'customers' => $customers,
            'pager'     => $this->pager,
        ];
    }

    /**
     * Get single customer with full demographic, KYC, and transaction history
     */
    public function getCustomerWithFullProfile(int $id): ?array
    {
        $customer = $this->select('customers.*, leads.lead_code, leads.lead_status, leads.lead_stage')
            ->join('leads', 'leads.id = customers.lead_id', 'left')
            ->where('customers.id', $id)
            ->where('customers.deleted_at', null)
            ->first();

        if (!$customer) {
            return null;
        }

        $db = \Config\Database::connect();

        // KYC Documents
        $customer['documents'] = $db->table('customer_documents')
            ->select('customer_documents.*, users.name as verified_by_name')
            ->join('users', 'users.id = customer_documents.verified_by', 'left')
            ->where('customer_id', $id)
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        // Bookings
        $customer['bookings'] = $db->table('bookings')
            ->select('bookings.*, properties.title as property_title, property_units.unit_number, projects.name as project_name')
            ->join('properties', 'properties.id = bookings.property_id', 'left')
            ->join('property_units', 'property_units.id = bookings.property_unit_id', 'left')
            ->join('projects', 'projects.id = bookings.project_id', 'left')
            ->where('bookings.customer_id', $id)
            ->where('bookings.deleted_at', null)
            ->orderBy('bookings.id', 'DESC')
            ->get()
            ->getResultArray();

        // Payments
        $customer['payments'] = $db->table('payments')
            ->select('payments.*, bookings.booking_number')
            ->join('bookings', 'bookings.id = payments.booking_id', 'left')
            ->where('payments.customer_id', $id)
            ->orderBy('payments.payment_date', 'DESC')
            ->get()
            ->getResultArray();

        // Invoices
        $customer['invoices'] = $db->table('invoices')
            ->select('invoices.*, bookings.booking_number')
            ->join('bookings', 'bookings.id = invoices.booking_id', 'left')
            ->where('invoices.customer_id', $id)
            ->orderBy('invoices.invoice_date', 'DESC')
            ->get()
            ->getResultArray();

        // Receipts
        $customer['receipts'] = $db->table('receipts')
            ->select('receipts.*, bookings.booking_number, payments.payment_number')
            ->join('bookings', 'bookings.id = receipts.booking_id', 'left')
            ->join('payments', 'payments.id = receipts.payment_id', 'left')
            ->where('receipts.customer_id', $id)
            ->orderBy('receipts.receipt_date', 'DESC')
            ->get()
            ->getResultArray();

        // Sales Agreements
        $customer['agreements'] = $db->table('sales_agreements')
            ->select('sales_agreements.*, bookings.booking_number, properties.title as property_title, property_units.unit_number')
            ->join('bookings', 'bookings.id = sales_agreements.booking_id', 'left')
            ->join('properties', 'properties.id = sales_agreements.property_id', 'left')
            ->join('property_units', 'property_units.id = sales_agreements.property_unit_id', 'left')
            ->where('sales_agreements.customer_id', $id)
            ->orderBy('sales_agreements.id', 'DESC')
            ->get()
            ->getResultArray();

        // Financial Totals
        $customer['total_purchase_value'] = array_sum(array_column($customer['bookings'], 'final_amount'));
        $receivedPayments = array_filter($customer['payments'], fn($p) => $p['status'] === 'Received');
        $customer['total_paid'] = array_sum(array_column($receivedPayments, 'amount'));
        $customer['total_outstanding'] = max(0, $customer['total_purchase_value'] - $customer['total_paid']);

        return $customer;
    }
}
