<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentScheduleItemModel extends Model
{
    protected $table            = 'payment_schedule_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'payment_schedule_id',
        'milestone_name',
        'due_date',
        'percentage',
        'amount',
        'status',
        'paid_amount',
        'remaining_amount',
        'remarks',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Allocate payment amount to milestone item
     */
    public function allocatePayment(int $itemId, float $paymentAmount): bool
    {
        $item = $this->find($itemId);
        if (!$item) {
            return false;
        }

        $newPaid = (float)$item['paid_amount'] + $paymentAmount;
        $totalAmount = (float)$item['amount'];
        $newRemaining = max(0, $totalAmount - $newPaid);

        $newStatus = 'Pending';
        if ($newRemaining <= 0.001) {
            $newStatus = 'Paid';
        } elseif ($newPaid > 0) {
            $newStatus = 'Partially Paid';
        } elseif ($item['due_date'] && $item['due_date'] < date('Y-m-d')) {
            $newStatus = 'Overdue';
        }

        return $this->update($itemId, [
            'paid_amount'      => $newPaid,
            'remaining_amount' => $newRemaining,
            'status'           => $newStatus,
        ]);
    }
}
