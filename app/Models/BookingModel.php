<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingModel extends Model
{
    protected $table            = 'bookings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'booking_number',
        'customer_id',
        'lead_id',
        'project_id',
        'property_id',
        'property_unit_id',
        'sales_executive_id',
        'booking_date',
        'booking_status',
        'base_price',
        'discount',
        'tax_amount',
        'final_amount',
        'token_amount',
        'booking_amount',
        'remarks',
        'created_by',
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
     * Generate sequential unique booking number: BK-2026-000001
     */
    public function generateBookingNumber(): string
    {
        $year = date('Y');
        $prefix = "BK-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('booking_number');
        $builder->like('booking_number', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['booking_number'])) {
            $parts = explode('-', $last['booking_number']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return sprintf('%s%06d', $prefix, $nextSeq);
    }

    /**
     * Check if unit has an active booking (Pending Confirmation, Confirmed, Completed)
     */
    public function hasActiveBookingForUnit(int $unitId, ?int $excludeBookingId = null): bool
    {
        $builder = $this->where('property_unit_id', $unitId)
            ->whereIn('booking_status', ['Draft', 'Pending Confirmation', 'Confirmed'])
            ->where('deleted_at', null);

        if ($excludeBookingId) {
            $builder->where('id !=', $excludeBookingId);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Filtered bookings list with joined metadata and collection totals
     */
    public function getBookingsWithDetails(array $filters = [], int $perPage = 15): array
    {
        $builder = $this->select('bookings.*, 
            customers.customer_code, 
            customers.first_name, 
            customers.last_name, 
            customers.phone as customer_phone,
            properties.title as property_title,
            properties.property_code,
            property_units.unit_number,
            property_units.flat_type,
            projects.name as project_name,
            users.name as executive_name,
            (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payments.booking_id = bookings.id AND payments.status = "Received") as total_paid')
            ->join('customers', 'customers.id = bookings.customer_id', 'left')
            ->join('properties', 'properties.id = bookings.property_id', 'left')
            ->join('property_units', 'property_units.id = bookings.property_unit_id', 'left')
            ->join('projects', 'projects.id = bookings.project_id', 'left')
            ->join('users', 'users.id = bookings.sales_executive_id', 'left')
            ->where('bookings.deleted_at', null);

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $builder->groupStart()
                ->like('bookings.booking_number', $s)
                ->orLike('customers.first_name', $s)
                ->orLike('customers.last_name', $s)
                ->orLike('customers.phone', $s)
                ->orLike('property_units.unit_number', $s)
                ->orLike('properties.title', $s)
                ->groupEnd();
        }

        if (!empty($filters['booking_status'])) {
            $builder->where('bookings.booking_status', $filters['booking_status']);
        }

        if (!empty($filters['project_id'])) {
            $builder->where('bookings.project_id', $filters['project_id']);
        }

        if (!empty($filters['property_id'])) {
            $builder->where('bookings.property_id', $filters['property_id']);
        }

        if (!empty($filters['sales_executive_id'])) {
            $builder->where('bookings.sales_executive_id', $filters['sales_executive_id']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('bookings.booking_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('bookings.booking_date <=', $filters['date_to']);
        }

        $sort = $filters['sort'] ?? 'bookings.created_at';
        $order = $filters['order'] ?? 'DESC';
        $builder->orderBy($sort, $order);

        $bookings = $builder->paginate($perPage);

        return [
            'bookings' => $bookings,
            'pager'    => $this->pager,
        ];
    }

    /**
     * Get single booking with all submodules, items, payments, and audit history
     */
    public function getBookingWithFullDetails(int $id): ?array
    {
        $booking = $this->select('bookings.*, 
            customers.customer_code, 
            customers.first_name, 
            customers.last_name, 
            customers.email as customer_email, 
            customers.phone as customer_phone,
            customers.address as customer_address, 
            customers.city as customer_city, 
            customers.state as customer_state, 
            customers.pincode as customer_pincode,
            customers.id_proof_type,
            customers.id_proof_number,
            customers.kyc_status,
            leads.lead_code,
            properties.title as property_title,
            properties.property_code,
            property_units.unit_number,
            property_units.flat_type,
            property_units.floor as floor_number,
            property_units.floor,
            property_units.carpet_area,
            property_units.built_up_area,
            property_units.unit_price,
            property_units.availability_status as current_unit_status,
            projects.name as project_name,
            users.name as executive_name,
            users.email as executive_email,
            creators.name as created_by_name')
            ->join('customers', 'customers.id = bookings.customer_id', 'left')
            ->join('leads', 'leads.id = bookings.lead_id', 'left')
            ->join('properties', 'properties.id = bookings.property_id', 'left')
            ->join('property_units', 'property_units.id = bookings.property_unit_id', 'left')
            ->join('projects', 'projects.id = bookings.project_id', 'left')
            ->join('users', 'users.id = bookings.sales_executive_id', 'left')
            ->join('users as creators', 'creators.id = bookings.created_by', 'left')
            ->where('bookings.id', $id)
            ->where('bookings.deleted_at', null)
            ->first();

        if (!$booking) {
            return null;
        }

        $db = \Config\Database::connect();

        // 1. Payment Schedule & Items
        $schedule = $db->table('payment_schedules')
            ->where('booking_id', $id)
            ->get()
            ->getRowArray();

        $booking['schedule'] = $schedule;
        $booking['milestones'] = [];
        if ($schedule) {
            $booking['milestones'] = $db->table('payment_schedule_items')
                ->where('payment_schedule_id', $schedule['id'])
                ->orderBy('due_date', 'ASC')
                ->orderBy('id', 'ASC')
                ->get()
                ->getResultArray();
        }

        // 2. Payments Recorded
        $booking['payments'] = $db->table('payments')
            ->select('payments.*, payment_schedule_items.milestone_name, users.name as received_by_name')
            ->join('payment_schedule_items', 'payment_schedule_items.id = payments.payment_schedule_item_id', 'left')
            ->join('users', 'users.id = payments.received_by', 'left')
            ->where('payments.booking_id', $id)
            ->orderBy('payments.payment_date', 'DESC')
            ->get()
            ->getResultArray();

        // 3. Invoices
        $booking['invoices'] = $db->table('invoices')
            ->where('booking_id', $id)
            ->orderBy('invoice_date', 'DESC')
            ->get()
            ->getResultArray();

        // 4. Receipts
        $booking['receipts'] = $db->table('receipts')
            ->select('receipts.*, payments.payment_number')
            ->join('payments', 'payments.id = receipts.payment_id', 'left')
            ->where('receipts.booking_id', $id)
            ->orderBy('receipts.receipt_date', 'DESC')
            ->get()
            ->getResultArray();

        // 5. Sales Agreement
        $booking['agreement'] = $db->table('sales_agreements')
            ->where('booking_id', $id)
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();

        // 6. Booking Status History
        $booking['status_history'] = $db->table('booking_status_history')
            ->select('booking_status_history.*, users.name as changed_by_name')
            ->join('users', 'users.id = booking_status_history.changed_by', 'left')
            ->where('booking_id', $id)
            ->orderBy('booking_status_history.id', 'DESC')
            ->get()
            ->getResultArray();

        // 7. Commissions
        $booking['commissions'] = $db->table('commissions')
            ->select('commissions.*, users.name as agent_name, commission_rules.name as rule_name')
            ->join('users', 'users.id = commissions.agent_user_id', 'left')
            ->join('commission_rules', 'commission_rules.id = commissions.commission_rule_id', 'left')
            ->where('commissions.booking_id', $id)
            ->orderBy('commissions.id', 'DESC')
            ->get()
            ->getResultArray();

        // Dynamic Calculations
        $receivedPayments = array_filter($booking['payments'], fn($p) => $p['status'] === 'Received');
        $booking['total_paid'] = array_sum(array_column($receivedPayments, 'amount'));
        $booking['total_outstanding'] = max(0, (float)$booking['final_amount'] - $booking['total_paid']);

        // Overdue calculation based on schedule items
        $today = date('Y-m-d');
        $overdueAmt = 0.0;
        $nextDueDate = null;
        foreach ($booking['milestones'] as $m) {
            if ($m['status'] !== 'Paid') {
                if ($m['due_date'] && $m['due_date'] < $today) {
                    $overdueAmt += (float)$m['remaining_amount'];
                } elseif ($m['due_date'] && $m['due_date'] >= $today && !$nextDueDate) {
                    $nextDueDate = $m['due_date'];
                }
            }
        }
        $booking['overdue_amount'] = $overdueAmt;
        $booking['next_due_date']  = $nextDueDate;

        return $booking;
    }

    /**
     * Dynamic sales summary for Sales Dashboard and Executive Dashboard
     */
    public function getSalesSummary(): array
    {
        $db = \Config\Database::connect();

        $totalBookings = $this->where('deleted_at', null)->countAllResults();
        $confirmedBookings = $this->where('deleted_at', null)->where('booking_status', 'Confirmed')->countAllResults();
        $pendingBookings = $this->where('deleted_at', null)->whereIn('booking_status', ['Draft', 'Pending Confirmation'])->countAllResults();
        $cancelledBookings = $this->where('deleted_at', null)->where('booking_status', 'Cancelled')->countAllResults();

        // Total Booking Value (Confirmed + Completed)
        $valueRow = $this->select('COALESCE(SUM(final_amount), 0) as total_val')
            ->where('deleted_at', null)
            ->whereIn('booking_status', ['Confirmed', 'Completed'])
            ->first();
        $totalBookingValue = (float)($valueRow['total_val'] ?? 0);

        // Total Collected from payments
        $pmtRow = $db->table('payments')
            ->select('COALESCE(SUM(amount), 0) as total_col')
            ->where('status', 'Received')
            ->get()
            ->getRowArray();
        $totalCollected = (float)($pmtRow['total_col'] ?? 0);

        // Total Outstanding
        $totalOutstanding = max(0, $totalBookingValue - $totalCollected);

        // Overdue milestone amounts
        $today = date('Y-m-d');
        $overdueRow = $db->table('payment_schedule_items')
            ->select('COALESCE(SUM(remaining_amount), 0) as overdue_amt')
            ->where('due_date <', $today)
            ->where('status !=', 'Paid')
            ->get()
            ->getRowArray();
        $overdueAmount = (float)($overdueRow['overdue_amt'] ?? 0);

        // Upcoming payments (next 30 days)
        $next30Days = date('Y-m-d', strtotime('+30 days'));
        $upcomingRow = $db->table('payment_schedule_items')
            ->select('COALESCE(SUM(remaining_amount), 0) as upcoming_amt, COUNT(id) as upcoming_count')
            ->where('due_date >=', $today)
            ->where('due_date <=', $next30Days)
            ->where('status !=', 'Paid')
            ->get()
            ->getRowArray();
        $upcomingAmount = (float)($upcomingRow['upcoming_amt'] ?? 0);
        $upcomingCount  = (int)($upcomingRow['upcoming_count'] ?? 0);

        // Active Agreements (Signed)
        $activeAgreements = $db->table('sales_agreements')
            ->where('agreement_status', 'Signed')
            ->countAllResults();

        return [
            'total_bookings'      => $totalBookings,
            'confirmed_bookings'  => $confirmedBookings,
            'pending_bookings'    => $pendingBookings,
            'cancelled_bookings'  => $cancelledBookings,
            'total_booking_value' => $totalBookingValue,
            'total_collected'     => $totalCollected,
            'total_outstanding'   => $totalOutstanding,
            'overdue_amount'      => $overdueAmount,
            'upcoming_amount'     => $upcomingAmount,
            'upcoming_count'      => $upcomingCount,
            'active_agreements'   => $activeAgreements,
        ];
    }
}

