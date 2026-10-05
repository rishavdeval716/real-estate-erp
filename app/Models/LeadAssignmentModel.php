<?php

namespace App\Models;

use CodeIgniter\Model;

class LeadAssignmentModel extends Model
{
    protected $table            = 'lead_assignments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'lead_id',
        'assigned_to',
        'assigned_by',
        'assignment_type',
        'remarks',
        'created_at',
    ];

    protected $useTimestamps = false;

    /**
     * Get assignment history for a lead
     */
    public function getHistoryForLead(int $leadId): array
    {
        return $this->select('lead_assignments.*, 
            u1.name as assigned_to_name, 
            u1.email as assigned_to_email,
            u2.name as assigned_by_name')
            ->join('users u1', 'u1.id = lead_assignments.assigned_to', 'left')
            ->join('users u2', 'u2.id = lead_assignments.assigned_by', 'left')
            ->where('lead_assignments.lead_id', $leadId)
            ->orderBy('lead_assignments.created_at', 'DESC')
            ->findAll();
    }
}
