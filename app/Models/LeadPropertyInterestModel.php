<?php

namespace App\Models;

use CodeIgniter\Model;

class LeadPropertyInterestModel extends Model
{
    protected $table            = 'lead_property_interests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'lead_id',
        'project_id',
        'property_id',
        'property_unit_id',
        'interest_level',
        'remarks',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'lead_id'        => 'required|is_natural_no_zero',
        'interest_level' => 'required|in_list[Primary,Interested,Alternative,Not Interested]',
    ];

    /**
     * Get interests for a specific lead with joined titles/numbers
     */
    public function getInterestsForLead(int $leadId): array
    {
        return $this->select('lead_property_interests.*, 
            projects.name as project_name, 
            properties.title as property_title, 
            properties.property_code, 
            properties.price as property_price,
            property_units.unit_number, 
            property_units.unit_price')
            ->join('projects', 'projects.id = lead_property_interests.project_id', 'left')
            ->join('properties', 'properties.id = lead_property_interests.property_id', 'left')
            ->join('property_units', 'property_units.id = lead_property_interests.property_unit_id', 'left')
            ->where('lead_property_interests.lead_id', $leadId)
            ->orderBy('lead_property_interests.created_at', 'DESC')
            ->findAll();
    }
}
