<?php

namespace App\Models;

use CodeIgniter\Model;

class ContractorModel extends Model
{
    protected $table            = 'contractors';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'contractor_code',
        'company_name',
        'specialization',
        'contact_person',
        'phone',
        'email',
        'license_number',
        'gstin',
        'rating',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function generateContractorCode(): string
    {
        $year = date('Y');
        $prefix = "CON-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('contractor_code');
        $builder->like('contractor_code', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['contractor_code'])) {
            $parts = explode('-', $last['contractor_code']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return $prefix . str_pad((string)$nextSeq, 6, '0', STR_PAD_LEFT);
    }
}
