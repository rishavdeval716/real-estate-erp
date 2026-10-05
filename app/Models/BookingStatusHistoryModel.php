<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingStatusHistoryModel extends Model
{
    protected $table            = 'booking_status_history';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'booking_id',
        'old_status',
        'new_status',
        'changed_by',
        'remarks',
        'created_at',
    ];

    protected $useTimestamps = false;

    public function recordChange(int $bookingId, ?string $oldStatus, string $newStatus, ?int $userId = null, ?string $remarks = null): int|string|false
    {
        return $this->insert([
            'booking_id' => $bookingId,
            'old_status' => $oldStatus ?: 'None',
            'new_status' => $newStatus,
            'changed_by' => $userId ?: session()->get('user_id'),
            'remarks'    => $remarks,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
