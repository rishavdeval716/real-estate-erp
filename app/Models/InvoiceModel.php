<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceModel extends Model
{
    protected $table            = 'invoices';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'invoice_number',
        'booking_id',
        'customer_id',
        'invoice_date',
        'due_date',
        'subtotal',
        'discount',
        'tax',
        'total_amount',
        'paid_amount',
        'balance_amount',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Generate sequential unique invoice number: INV-2026-000001
     */
    public function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $prefix = "INV-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('invoice_number');
        $builder->like('invoice_number', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['invoice_number'])) {
            $parts = explode('-', $last['invoice_number']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return sprintf('%s%06d', $prefix, $nextSeq);
    }

    /**
     * Get invoices with details
     */
    public function getInvoicesWithDetails(array $filters = [], int $perPage = 15): array
    {
        $builder = $this->select('invoices.*, 
            bookings.booking_number,
            customers.customer_code, 
            customers.first_name, 
            customers.last_name, 
            customers.phone as customer_phone')
            ->join('bookings', 'bookings.id = invoices.booking_id', 'left')
            ->join('customers', 'customers.id = invoices.customer_id', 'left');

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $builder->groupStart()
                ->like('invoices.invoice_number', $s)
                ->orLike('bookings.booking_number', $s)
                ->orLike('customers.first_name', $s)
                ->orLike('customers.last_name', $s)
                ->groupEnd();
        }

        if (!empty($filters['status'])) {
            $builder->where('invoices.status', $filters['status']);
        }

        if (!empty($filters['booking_id'])) {
            $builder->where('invoices.booking_id', $filters['booking_id']);
        }

        $sort = $filters['sort'] ?? 'invoices.invoice_date';
        $order = $filters['order'] ?? 'DESC';
        $builder->orderBy($sort, $order);

        $invoices = $builder->paginate($perPage);

        return [
            'invoices' => $invoices,
            'pager'    => $this->pager,
        ];
    }
}
