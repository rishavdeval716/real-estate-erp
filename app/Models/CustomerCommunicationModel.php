<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerCommunicationModel extends Model
{
    protected $table            = 'customer_communications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'comm_code',
        'customer_id',
        'lead_id',
        'channel',
        'purpose',
        'subject',
        'content',
        'status',
        'communication_date',
        'user_id',
        'response_notes',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';

    public function generateCommCode(): string
    {
        $year = date('Y');
        $prefix = "COM-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('comm_code');
        $builder->like('comm_code', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['comm_code'])) {
            $parts = explode('-', $last['comm_code']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return $prefix . str_pad((string)$nextSeq, 6, '0', STR_PAD_LEFT);
    }
}
