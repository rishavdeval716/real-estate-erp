<?php

namespace App\Models;

use CodeIgniter\Model;

class ProspectModel extends Model
{
    protected $table            = 'prospects';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'lead_id',
        'name',
        'email',
        'phone',
        'alternate_phone',
        'address',
        'city',
        'state',
        'pincode',
        'occupation',
        'preferred_contact_method',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'lead_id' => 'required|is_natural_no_zero',
        'name'    => 'required|min_length[2]|max_length[150]',
        'phone'   => 'required|min_length[8]|max_length[30]',
    ];

    /**
     * Get prospect profile by lead ID
     */
    public function getByLeadId(int $leadId): ?array
    {
        return $this->where('lead_id', $leadId)->first();
    }
}
