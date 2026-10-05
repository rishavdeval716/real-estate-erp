<?php

namespace App\Models;

use CodeIgniter\Model;

class PropertyStatusHistoryModel extends Model
{
    protected $table            = 'property_status_history';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'property_id',
        'unit_id',
        'old_status',
        'new_status',
        'changed_by',
        'remarks',
        'created_at',
    ];

    // Dates
    protected $useTimestamps = false; // Manually handled via created_at
    protected $dateFormat    = 'datetime';

    /**
     * Log a status transition
     */
    public function recordChange(
        ?int $propertyId,
        ?int $unitId,
        string $oldStatus,
        string $newStatus,
        ?int $userId,
        ?string $remarks = null
    ): int|string|false {
        return $this->insert([
            'property_id' => ($propertyId && $propertyId > 0) ? $propertyId : null,
            'unit_id'     => $unitId,
            'old_status'  => $oldStatus,
            'new_status'  => $newStatus,
            'changed_by'  => $userId,
            'remarks'     => $remarks,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get history records with user names
     */
    public function getHistoryForEntity(int $propertyId, ?int $unitId = null): array
    {
        $builder = $this->select('property_status_history.*, users.name as user_name, users.email as user_email')
                        ->join('users', 'users.id = property_status_history.changed_by', 'left')
                        ->where('property_status_history.property_id', $propertyId);

        if ($unitId !== null) {
            $builder->where('property_status_history.unit_id', $unitId);
        } else {
            $builder->where('property_status_history.unit_id', null);
        }

        return $builder->orderBy('property_status_history.id', 'DESC')->findAll();
    }
}
