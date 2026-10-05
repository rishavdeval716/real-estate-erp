<?php

namespace App\Models;

use CodeIgniter\Model;

class UnitHoldModel extends Model
{
    protected $table            = 'unit_holds';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'hold_code',
        'lead_id',
        'property_id',
        'property_unit_id',
        'held_by',
        'hold_status',
        'hold_reason',
        'started_at',
        'expires_at',
        'released_at',
        'remarks',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'hold_code'        => 'required|max_length[50]',
        'lead_id'          => 'required|is_natural_no_zero',
        'property_unit_id' => 'required|is_natural_no_zero',
        'held_by'          => 'required|is_natural_no_zero',
        'started_at'       => 'required',
        'expires_at'       => 'required',
        'hold_status'      => 'required|in_list[Active,Expired,Released,Converted]',
    ];

    /**
     * Generate unique sequential hold code: HOLD-YYYY-000001
     */
    public function generateHoldCode(): string
    {
        $year = date('Y');
        $prefix = "HOLD-{$year}-";

        $db = \Config\Database::connect();
        $row = $db->table($this->table)
            ->select('hold_code')
            ->like('hold_code', $prefix, 'after')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        $nextSeq = 1;
        if ($row && !empty($row['hold_code'])) {
            $parts = explode('-', $row['hold_code']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return sprintf('%s%06d', $prefix, $nextSeq);
    }

    /**
     * Check if an active hold exists on a unit
     */
    public function getActiveHoldForUnit(int $unitId): ?array
    {
        $this->expireOverdueHolds(); // Keep state refreshed on-access

        return $this->where('property_unit_id', $unitId)
            ->where('hold_status', 'Active')
            ->first();
    }

    /**
     * Get active holds with lead, unit, property, and user details
     */
    public function getActiveHolds(int $limit = 25): array
    {
        $this->expireOverdueHolds();

        return $this->select('unit_holds.*, 
            leads.lead_code, 
            leads.first_name, 
            leads.last_name, 
            leads.phone as lead_phone,
            properties.title as property_title,
            properties.property_code,
            property_units.unit_number,
            property_units.flat_type,
            property_units.unit_price,
            users.name as held_by_name')
            ->join('leads', 'leads.id = unit_holds.lead_id', 'left')
            ->join('properties', 'properties.id = unit_holds.property_id', 'left')
            ->join('property_units', 'property_units.id = unit_holds.property_unit_id', 'left')
            ->join('users', 'users.id = unit_holds.held_by', 'left')
            ->where('unit_holds.hold_status', 'Active')
            ->orderBy('unit_holds.expires_at', 'ASC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get all holds for a specific lead
     */
    public function getHoldsForLead(int $leadId): array
    {
        $this->expireOverdueHolds();

        return $this->select('unit_holds.*, 
            properties.title as property_title,
            property_units.unit_number,
            property_units.flat_type,
            property_units.unit_price,
            users.name as held_by_name')
            ->join('properties', 'properties.id = unit_holds.property_id', 'left')
            ->join('property_units', 'property_units.id = unit_holds.property_unit_id', 'left')
            ->join('users', 'users.id = unit_holds.held_by', 'left')
            ->where('unit_holds.lead_id', $leadId)
            ->orderBy('unit_holds.created_at', 'DESC')
            ->findAll();
    }

    /**
     * On-access automatic expiration mechanism
     * Finds active holds where expires_at < NOW(), marks Expired, and restores unit to Available
     */
    public function expireOverdueHolds(): int
    {
        $now = date('Y-m-d H:i:s');
        $overdue = $this->where('hold_status', 'Active')
            ->where('expires_at <', $now)
            ->findAll();

        $expiredCount = 0;
        if (!empty($overdue)) {
            $unitModel = new PropertyUnitModel();
            $statusHistModel = new PropertyStatusHistoryModel();
            $auditModel = new AuditLogModel();

            foreach ($overdue as $h) {
                // Update hold to Expired
                $this->update($h['id'], [
                    'hold_status' => 'Expired',
                ]);

                // Restore unit to Available if currently Reserved
                $unit = $unitModel->find($h['property_unit_id']);
                if ($unit && $unit['availability_status'] === 'Reserved') {
                    $unitModel->update($unit['id'], [
                        'availability_status' => 'Available',
                    ]);

                    // Record status history
                    $statusHistModel->insert([
                        'property_id' => $unit['property_id'] ?: null,
                        'unit_id'     => $unit['id'],
                        'old_status'  => 'Reserved',
                        'new_status'  => 'Available',
                        'changed_by'  => $h['held_by'],
                        'remarks'     => "Unit Hold {$h['hold_code']} expired automatically.",
                        'created_at'  => $now,
                    ]);

                    // Audit log
                    $auditModel->record(
                        $h['held_by'],
                        'UNIT_HOLD_EXPIRED',
                        'UnitHolds',
                        $h['id'],
                        "Unit Hold {$h['hold_code']} expired. Unit {$unit['unit_number']} restored to Available."
                    );
                }

                $expiredCount++;
            }
        }

        return $expiredCount;
    }
}
