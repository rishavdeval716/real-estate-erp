<?php

namespace App\Models;

use CodeIgniter\Model;

class PropertyVerificationModel extends Model
{
    protected $table            = 'property_verifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'verification_code',
        'property_id',
        'verification_type',
        'status',
        'assigned_to',
        'checklist_data',
        'findings',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'expiry_date',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';

    public function generateVerificationCode(): string
    {
        $year = date('Y');
        $prefix = "VER-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('verification_code');
        $builder->like('verification_code', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['verification_code'])) {
            $parts = explode('-', $last['verification_code']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return $prefix . str_pad((string)$nextSeq, 6, '0', STR_PAD_LEFT);
    }
}
