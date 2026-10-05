<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyModel extends Model
{
    protected $table            = 'companies';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'logo',
        'email',
        'phone',
        'alternate_phone',
        'address',
        'city',
        'state',
        'country',
        'pincode',
        'website',
        'tax_number',
        'description',
        'status',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'name'   => 'required|min_length[3]|max_length[150]',
        'email'  => 'permit_empty|valid_email|max_length[150]',
        'phone'  => 'permit_empty|max_length[30]',
        'status' => 'required|in_list[active,inactive]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'Company name is required.',
            'min_length' => 'Company name must be at least 3 characters.',
        ],
        'email' => [
            'valid_email' => 'Please provide a valid company email address.',
        ],
    ];

    /**
     * Get or create default company profile
     */
    public function getCompanyProfile()
    {
        $company = $this->first();
        if (!$company) {
            $id = $this->insert([
                'name'        => 'Real Estate ERP Global Group',
                'email'       => 'info@realestate-erp.local',
                'phone'       => '+91 98765 43210',
                'address'     => 'Corporate Towers, Tech Park Boulevard',
                'city'        => 'Mumbai',
                'state'       => 'Maharashtra',
                'country'     => 'India',
                'pincode'     => '400001',
                'website'     => 'https://realestate-erp.local',
                'tax_number'  => '27AAAAA0000A1Z5',
                'description' => 'Enterprise Real Estate Management & CRM Infrastructure',
                'status'      => 'active',
            ]);
            return $this->find($id);
        }
        return $company;
    }
}
