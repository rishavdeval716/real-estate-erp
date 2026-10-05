<?php

namespace App\Models;

use CodeIgniter\Model;

class LeadFollowupModel extends Model
{
    protected $table            = 'lead_followups';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'lead_id',
        'assigned_to',
        'followup_type',
        'scheduled_at',
        'completed_at',
        'status',
        'outcome',
        'notes',
        'next_followup_at',
        'created_by',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'lead_id'       => 'required|is_natural_no_zero',
        'assigned_to'   => 'required|is_natural_no_zero',
        'followup_type' => 'required|in_list[Phone Call,WhatsApp,Email,Meeting,Site Visit,Other]',
        'scheduled_at'  => 'required',
        'status'        => 'required|in_list[Pending,Completed,Cancelled,Missed]',
    ];

    /**
     * Get followups for a lead with assignee name
     */
    public function getFollowupsForLead(int $leadId): array
    {
        return $this->select('lead_followups.*, 
            users.name as assigned_to_name,
            creator.name as creator_name')
            ->join('users', 'users.id = lead_followups.assigned_to', 'left')
            ->join('users creator', 'creator.id = lead_followups.created_by', 'left')
            ->where('lead_followups.lead_id', $leadId)
            ->orderBy('lead_followups.scheduled_at', 'DESC')
            ->findAll();
    }

    /**
     * Get system-wide upcoming pending follow-ups
     */
    public function getUpcomingFollowups(int $limit = 10): array
    {
        return $this->select('lead_followups.*, 
            leads.lead_code, 
            leads.first_name, 
            leads.last_name, 
            leads.phone as lead_phone,
            users.name as assigned_to_name')
            ->join('leads', 'leads.id = lead_followups.lead_id', 'left')
            ->join('users', 'users.id = lead_followups.assigned_to', 'left')
            ->where('lead_followups.status', 'Pending')
            ->orderBy('lead_followups.scheduled_at', 'ASC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Today's pending followups count
     */
    public function getTodayPendingCount(): int
    {
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd   = date('Y-m-d 23:59:59');

        return $this->where('status', 'Pending')
            ->where('scheduled_at >=', $todayStart)
            ->where('scheduled_at <=', $todayEnd)
            ->countAllResults();
    }

    /**
     * Overdue followups count (scheduled before today and still Pending)
     */
    public function getOverdueCount(): int
    {
        $todayStart = date('Y-m-d 00:00:00');

        return $this->where('status', 'Pending')
            ->where('scheduled_at <', $todayStart)
            ->countAllResults();
    }
}
