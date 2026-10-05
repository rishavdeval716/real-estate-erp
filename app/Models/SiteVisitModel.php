<?php

namespace App\Models;

use CodeIgniter\Model;

class SiteVisitModel extends Model
{
    protected $table            = 'site_visits';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'visit_code',
        'lead_id',
        'project_id',
        'property_id',
        'property_unit_id',
        'assigned_user_id',
        'scheduled_at',
        'visit_type',
        'status',
        'visitor_count',
        'rating',
        'interest_level',
        'feedback',
        'agent_observation',
        'preferred_unit',
        'price_feedback',
        'next_action',
        'remarks',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'visit_code'   => 'required|max_length[50]',
        'lead_id'      => 'required|is_natural_no_zero',
        'scheduled_at' => 'required',
        'visit_type'   => 'required|in_list[Property Visit,Project Visit,Virtual Visit]',
        'status'       => 'required|in_list[Scheduled,Confirmed,Completed,Cancelled,No Show,Rescheduled]',
    ];

    /**
     * Generate unique sequential site visit code: SV-YYYY-000001
     */
    public function generateVisitCode(): string
    {
        $year = date('Y');
        $prefix = "SV-{$year}-";

        $db = \Config\Database::connect();
        $row = $db->table($this->table)
            ->select('visit_code')
            ->like('visit_code', $prefix, 'after')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        $nextSeq = 1;
        if ($row && !empty($row['visit_code'])) {
            $parts = explode('-', $row['visit_code']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return sprintf('%s%06d', $prefix, $nextSeq);
    }

    /**
     * Get site visits for a lead with project/property/agent details
     */
    public function getVisitsForLead(int $leadId): array
    {
        return $this->select('site_visits.*, 
            projects.name as project_name, 
            properties.title as property_title, 
            property_units.unit_number,
            users.name as assigned_agent_name')
            ->join('projects', 'projects.id = site_visits.project_id', 'left')
            ->join('properties', 'properties.id = site_visits.property_id', 'left')
            ->join('property_units', 'property_units.id = site_visits.property_unit_id', 'left')
            ->join('users', 'users.id = site_visits.assigned_user_id', 'left')
            ->where('site_visits.lead_id', $leadId)
            ->orderBy('site_visits.scheduled_at', 'DESC')
            ->findAll();
    }

    /**
     * Get upcoming scheduled site visits
     */
    public function getUpcomingVisits(int $limit = 10): array
    {
        return $this->select('site_visits.*, 
            leads.lead_code, 
            leads.first_name, 
            leads.last_name, 
            leads.phone as lead_phone,
            projects.name as project_name,
            properties.title as property_title,
            users.name as assigned_agent_name')
            ->join('leads', 'leads.id = site_visits.lead_id', 'left')
            ->join('projects', 'projects.id = site_visits.project_id', 'left')
            ->join('properties', 'properties.id = site_visits.property_id', 'left')
            ->join('users', 'users.id = site_visits.assigned_user_id', 'left')
            ->whereIn('site_visits.status', ['Scheduled', 'Confirmed'])
            ->orderBy('site_visits.scheduled_at', 'ASC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Today's site visits count
     */
    public function getTodayVisitsCount(): int
    {
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd   = date('Y-m-d 23:59:59');

        return $this->where('scheduled_at >=', $todayStart)
            ->where('scheduled_at <=', $todayEnd)
            ->countAllResults();
    }
}
