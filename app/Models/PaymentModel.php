<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table            = 'payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'payment_number',
        'booking_id',
        'customer_id',
        'payment_schedule_item_id',
        'payment_date',
        'amount',
        'payment_method',
        'transaction_reference',
        'bank_name',
        'cheque_number',
        'remarks',
        'status',
        'received_by',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Generate sequential unique payment number: PMT-2026-000001
     */
    public function generatePaymentNumber(): string
    {
        $year = date('Y');
        $prefix = "PMT-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('payment_number');
        $builder->like('payment_number', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['payment_number'])) {
            $parts = explode('-', $last['payment_number']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return sprintf('%s%06d', $prefix, $nextSeq);
    }

    /**
     * Get payments listing with joined details
     */
    public function getPaymentsWithDetails(array $filters = [], int $perPage = 15): array
    {
        $builder = $this->select('payments.*, 
            bookings.booking_number,
            customers.customer_code, 
            customers.first_name, 
            customers.last_name, 
            customers.phone as customer_phone,
            payment_schedule_items.milestone_name,
            receipts.receipt_number,
            users.name as received_by_name')
            ->join('bookings', 'bookings.id = payments.booking_id', 'left')
            ->join('customers', 'customers.id = payments.customer_id', 'left')
            ->join('payment_schedule_items', 'payment_schedule_items.id = payments.payment_schedule_item_id', 'left')
            ->join('receipts', 'receipts.payment_id = payments.id', 'left')
            ->join('users', 'users.id = payments.received_by', 'left');

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $builder->groupStart()
                ->like('payments.payment_number', $s)
                ->orLike('bookings.booking_number', $s)
                ->orLike('customers.first_name', $s)
                ->orLike('customers.last_name', $s)
                ->orLike('payments.transaction_reference', $s)
                ->orLike('receipts.receipt_number', $s)
                ->groupEnd();
        }

        if (!empty($filters['status'])) {
            $builder->where('payments.status', $filters['status']);
        }

        if (!empty($filters['payment_method'])) {
            $builder->where('payments.payment_method', $filters['payment_method']);
        }

        if (!empty($filters['booking_id'])) {
            $builder->where('payments.booking_id', $filters['booking_id']);
        }

        $sort = $filters['sort'] ?? 'payments.payment_date';
        $order = $filters['order'] ?? 'DESC';
        $builder->orderBy($sort, $order);

        $payments = $builder->paginate($perPage);

        return [
            'payments' => $payments,
            'pager'    => $this->pager,
        ];
    }
}
