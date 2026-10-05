<?php

namespace App\Models;

use CodeIgniter\Model;

class TechnicianModel extends Model
{
    protected $table            = 'technicians';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'technician_code',
        'name',
        'mobile',
        'email',
        'skill',
        'department',
        'availability',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function generateTechnicianCode(): string
    {
        $year = date('Y');
        $prefix = "TECH-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('technician_code');
        $builder->like('technician_code', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['technician_code'])) {
            $parts = explode('-', $last['technician_code']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return sprintf('%s%06d', $prefix, $nextSeq);
    }
}
