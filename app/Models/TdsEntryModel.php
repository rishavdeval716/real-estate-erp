<?php

namespace App\Models;

use CodeIgniter\Model;

class TdsEntryModel extends Model
{
    protected $table            = 'tds_entries';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'entry_code',
        'party_name',
        'pan_number',
        'section',
        'transaction_reference',
        'commission_id',
        'gross_amount',
        'tds_rate',
        'tds_amount',
        'net_payable',
        'deduction_date',
        'financial_year',
        'quarter',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function generateEntryCode(): string
    {
        $year = date('Y');
        $prefix = "TDS-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('entry_code');
        $builder->like('entry_code', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['entry_code'])) {
            $parts = explode('-', $last['entry_code']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return sprintf('%s%06d', $prefix, $nextSeq);
    }
}
