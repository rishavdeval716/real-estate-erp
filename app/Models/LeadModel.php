<?php

namespace App\Models;

use CodeIgniter\Model;

class LeadModel extends Model
{
    protected $table            = 'leads';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'lead_code',
        'first_name',
        'last_name',
        'email',
        'phone',
        'alternate_phone',
        'lead_source_id',
        'assigned_user_id',
        'branch_id',
        'lead_status',
        'lead_stage',
        'priority',
        'budget_min',
        'budget_max',
        'preferred_location',
        'property_type_id',
        'project_id',
        'property_id',
        'property_unit_id',
        'purchase_purpose',
        'purchase_timeline',
        'financing_required',
        'site_visit_required',
        'remarks',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'lead_code'  => 'required|max_length[50]',
        'first_name' => 'required|min_length[2]|max_length[100]',
        'phone'      => 'required|min_length[8]|max_length[30]',
        'email'      => 'permit_empty|valid_email|max_length[150]',
    ];

    /**
     * Generate sequential unique lead code: LEAD-YYYY-000001
     */
    public function generateLeadCode(): string
    {
        $year = date('Y');
        $prefix = "LEAD-{$year}-";

        $db = \Config\Database::connect();
        $row = $db->table($this->table)
            ->select('lead_code')
            ->like('lead_code', $prefix, 'after')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        $nextSeq = 1;
        if ($row && !empty($row['lead_code'])) {
            $parts = explode('-', $row['lead_code']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return sprintf('%s%06d', $prefix, $nextSeq);
    }

    /**
     * Get paginated leads with joined source, assignee, project, and property details
     */
    public function getLeadsWithDetails(array $filters = [], int $perPage = 15): array
    {
        $builder = $this->builder();
        $builder->select('leads.*, 
            lead_sources.name as source_name, 
            users.name as assigned_to_name, 
            branches.name as branch_name,
            projects.name as project_name,
            properties.title as property_title,
            property_types.name as property_type_name,
            property_units.unit_number as unit_number')
            ->join('lead_sources', 'lead_sources.id = leads.lead_source_id', 'left')
            ->join('users', 'users.id = leads.assigned_user_id', 'left')
            ->join('branches', 'branches.id = leads.branch_id', 'left')
            ->join('projects', 'projects.id = leads.project_id', 'left')
            ->join('properties', 'properties.id = leads.property_id', 'left')
            ->join('property_types', 'property_types.id = leads.property_type_id', 'left')
            ->join('property_units', 'property_units.id = leads.property_unit_id', 'left')
            ->where('leads.deleted_at', null);

        // Apply filters
        if (!empty($filters['search'])) {
            $s = trim($filters['search']);
            $builder->groupStart()
                ->like('leads.lead_code', $s)
                ->orLike('leads.first_name', $s)
                ->orLike('leads.last_name', $s)
                ->orLike('leads.phone', $s)
                ->orLike('leads.email', $s)
                ->groupEnd();
        }

        if (!empty($filters['lead_status'])) {
            $builder->where('leads.lead_status', $filters['lead_status']);
        }

        if (!empty($filters['lead_stage'])) {
            $builder->where('leads.lead_stage', $filters['lead_stage']);
        }

        if (!empty($filters['priority'])) {
            $builder->where('leads.priority', $filters['priority']);
        }

        if (!empty($filters['lead_source_id'])) {
            $builder->where('leads.lead_source_id', $filters['lead_source_id']);
        }

        if (!empty($filters['assigned_user_id'])) {
            $builder->where('leads.assigned_user_id', $filters['assigned_user_id']);
        }

        if (!empty($filters['project_id'])) {
            $builder->where('leads.project_id', $filters['project_id']);
        }

        if (!empty($filters['property_type_id'])) {
            $builder->where('leads.property_type_id', $filters['property_type_id']);
        }

        if (!empty($filters['min_budget'])) {
            $builder->where('leads.budget_min >=', (float)$filters['min_budget']);
        }

        if (!empty($filters['max_budget'])) {
            $builder->where('leads.budget_max <=', (float)$filters['max_budget']);
        }

        $sortField = $filters['sort'] ?? 'leads.created_at';
        $sortOrder = $filters['order'] ?? 'DESC';
        $builder->orderBy($sortField, $sortOrder);

        $leads = $this->paginate($perPage);
        return [
            'leads' => $leads,
            'pager' => $this->pager,
        ];
    }

    /**
     * Get single lead with full joined context
     */
    public function getLeadWithFullDetails(int $id): ?array
    {
        return $this->select('leads.*, 
            lead_sources.name as source_name, 
            users.name as assigned_to_name, 
            users.email as assigned_to_email,
            branches.name as branch_name,
            projects.name as project_name,
            properties.title as property_title,
            properties.property_code as property_code,
            property_types.name as property_type_name,
            property_units.unit_number as unit_number')
            ->join('lead_sources', 'lead_sources.id = leads.lead_source_id', 'left')
            ->join('users', 'users.id = leads.assigned_user_id', 'left')
            ->join('branches', 'branches.id = leads.branch_id', 'left')
            ->join('projects', 'projects.id = leads.project_id', 'left')
            ->join('properties', 'properties.id = leads.property_id', 'left')
            ->join('property_types', 'property_types.id = leads.property_type_id', 'left')
            ->join('property_units', 'property_units.id = leads.property_unit_id', 'left')
            ->where('leads.id', $id)
            ->first();
    }

    /**
     * Get all active leads grouped by pipeline stage for Kanban board
     */
    public function getPipelineLeadsByStage(): array
    {
        $allStages = \App\Libraries\LeadStatus::getStages();
        $pipeline = array_fill_keys($allStages, []);

        $leads = $this->select('leads.*, 
            lead_sources.name as source_name, 
            users.name as assigned_to_name, 
            projects.name as project_name,
            properties.title as property_title,
            property_units.unit_number as unit_number')
            ->join('lead_sources', 'lead_sources.id = leads.lead_source_id', 'left')
            ->join('users', 'users.id = leads.assigned_user_id', 'left')
            ->join('projects', 'projects.id = leads.project_id', 'left')
            ->join('properties', 'properties.id = leads.property_id', 'left')
            ->join('property_units', 'property_units.id = leads.property_unit_id', 'left')
            ->where('leads.deleted_at', null)
            ->orderBy('leads.priority', 'DESC')
            ->orderBy('leads.updated_at', 'DESC')
            ->findAll();

        foreach ($leads as $l) {
            $stage = $l['lead_stage'] ?? 'New';
            if (isset($pipeline[$stage])) {
                $pipeline[$stage][] = $l;
            } else {
                $pipeline['New'][] = $l;
            }
        }

        return $pipeline;
    }
}
