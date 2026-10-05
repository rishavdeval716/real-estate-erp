<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentScheduleModel extends Model
{
    protected $table            = 'payment_schedules';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'booking_id',
        'schedule_name',
        'total_amount',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Generate standard construction-linked milestones totaling 100%
     */
    public function generateStandardMilestones(int $bookingId, float $totalAmount, string $startDate = ''): int
    {
        $existing = $this->where('booking_id', $bookingId)->first();
        if ($existing) {
            $scheduleId = (int)$existing['id'];
        } else {
            $scheduleId = (int)$this->insert([
                'booking_id'    => $bookingId,
                'schedule_name' => 'Construction Linked Plan (CLP)',
                'total_amount'  => $totalAmount,
            ]);
        }

        $itemModel = new PaymentScheduleItemModel();
        $baseDate = $startDate ? strtotime($startDate) : time();

        $milestones = [
            ['name' => 'Token Advance',                       'pct' => 10.00, 'days' => 0],
            ['name' => 'Booking Confirmation & Agreement',    'pct' => 10.00, 'days' => 30],
            ['name' => 'Commencement of Foundation',          'pct' => 15.00, 'days' => 90],
            ['name' => 'Completion of Plinth Level',          'pct' => 15.00, 'days' => 180],
            ['name' => 'Completion of Structural Slab',        'pct' => 20.00, 'days' => 270],
            ['name' => 'Brickwork & Internal Plaster',        'pct' => 10.00, 'days' => 365],
            ['name' => 'Flooring, Plumbing & Finishing',      'pct' => 10.00, 'days' => 450],
            ['name' => 'Notice of Possession & Handover',     'pct' => 10.00, 'days' => 540],
        ];

        foreach ($milestones as $m) {
            $amt = round($totalAmount * ($m['pct'] / 100.0), 2);
            $dueDate = date('Y-m-d', strtotime("+{$m['days']} days", $baseDate));

            $itemModel->insert([
                'payment_schedule_id' => $scheduleId,
                'milestone_name'      => $m['name'],
                'due_date'            => $dueDate,
                'percentage'          => $m['pct'],
                'amount'              => $amt,
                'status'              => 'Pending',
                'paid_amount'         => 0.00,
                'remaining_amount'    => $amt,
                'remarks'             => "Standard stage milestone ({$m['pct']}%)",
            ]);
        }

        return $scheduleId;
    }
}
