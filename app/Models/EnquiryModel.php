<?php

namespace App\Models;

use CodeIgniter\Model;

class EnquiryModel extends Model
{
    protected $table            = 'enquiries';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'enquiry_code',
        'lead_id',
        'project_id',
        'property_id',
        'property_unit_id',
        'enquiry_type',
        'requirement',
        'budget',
        'preferred_location',
        'preferred_property_type',
        'status',
        'remarks',
        'created_by',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'enquiry_code' => 'required|max_length[50]',
        'lead_id'      => 'required|is_natural_no_zero',
        'enquiry_type' => 'required|in_list[Purchase,Investment,Rent,Commercial,Plot/Land,Other]',
        'status'       => 'required|in_list[Open,In Progress,Qualified,Closed,Cancelled]',
    ];

    /**
     * Generate unique sequential enquiry code: ENQ-YYYY-000001
     */
    public function generateEnquiryCode(): string
    {
        $year = date('Y');
        $prefix = "ENQ-{$year}-";

        $db = \Config\Database::connect();
        $row = $db->table($this->table)
            ->select('enquiry_code')
            ->like('enquiry_code', $prefix, 'after')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        $nextSeq = 1;
        if ($row && !empty($row['enquiry_code'])) {
            $parts = explode('-', $row['enquiry_code']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return sprintf('%s%06d', $prefix, $nextSeq);
    }

    /**
     * Get enquiries with full joined details
     */
    public function getEnquiriesWithDetails(?int $leadId = null): array
    {
        $builder = $this->select('enquiries.*, 
            leads.lead_code, 
            leads.first_name, 
            leads.last_name, 
            leads.phone as lead_phone,
            projects.name as project_name, 
            properties.title as property_title, 
            property_units.unit_number,
            users.name as creator_name')
            ->join('leads', 'leads.id = enquiries.lead_id', 'left')
            ->join('projects', 'projects.id = enquiries.project_id', 'left')
            ->join('properties', 'properties.id = enquiries.property_id', 'left')
            ->join('property_units', 'property_units.id = enquiries.property_unit_id', 'left')
            ->join('users', 'users.id = enquiries.created_by', 'left');

        if ($leadId !== null) {
            $builder->where('enquiries.lead_id', $leadId);
        }

        return $builder->orderBy('enquiries.created_at', 'DESC')->findAll();
    }
}
