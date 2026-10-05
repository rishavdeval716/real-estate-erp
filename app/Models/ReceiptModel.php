<?php

namespace App\Models;

use CodeIgniter\Model;

class ReceiptModel extends Model
{
    protected $table            = 'receipts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'receipt_number',
        'payment_id',
        'booking_id',
        'customer_id',
        'receipt_date',
        'amount',
        'payment_method',
        'transaction_reference',
        'remarks',
        'created_at',
    ];

    protected $useTimestamps = false;

    /**
     * Generate sequential unique receipt number: RCT-2026-000001
     */
    public function generateReceiptNumber(): string
    {
        $year = date('Y');
        $prefix = "RCT-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('receipt_number');
        $builder->like('receipt_number', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['receipt_number'])) {
            $parts = explode('-', $last['receipt_number']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return sprintf('%s%06d', $prefix, $nextSeq);
    }

    /**
     * Get receipts with details
     */
    public function getReceiptsWithDetails(array $filters = [], int $perPage = 15): array
    {
        $builder = $this->select('receipts.*, 
            payments.payment_number,
            bookings.booking_number,
            customers.customer_code, 
            customers.first_name, 
            customers.last_name,
            customers.phone as customer_phone')
            ->join('payments', 'payments.id = receipts.payment_id', 'left')
            ->join('bookings', 'bookings.id = receipts.booking_id', 'left')
            ->join('customers', 'customers.id = receipts.customer_id', 'left');

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $builder->groupStart()
                ->like('receipts.receipt_number', $s)
                ->orLike('payments.payment_number', $s)
                ->orLike('bookings.booking_number', $s)
                ->orLike('customers.first_name', $s)
                ->orLike('customers.last_name', $s)
                ->groupEnd();
        }

        if (!empty($filters['booking_id'])) {
            $builder->where('receipts.booking_id', $filters['booking_id']);
        }

        $sort = $filters['sort'] ?? 'receipts.receipt_date';
        $order = $filters['order'] ?? 'DESC';
        $builder->orderBy($sort, $order);

        $receipts = $builder->paginate($perPage);

        return [
            'receipts' => $receipts,
            'pager'    => $this->pager,
        ];
    }
}
