<?php

namespace App\Models;

use CodeIgniter\Model;

class TdsCertificateModel extends Model
{
    protected $table            = 'tds_certificates';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'certificate_number',
        'party_name',
        'pan_number',
        'gross_amount',
        'tds_amount',
        'financial_year',
        'quarter',
        'certificate_file',
        'issue_date',
        'created_at',
    ];

    protected $useTimestamps = false;

    public function generateCertificateNumber(): string
    {
        $year = date('Y');
        $prefix = "CERT-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('certificate_number');
        $builder->like('certificate_number', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['certificate_number'])) {
            $parts = explode('-', $last['certificate_number']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return sprintf('%s%06d', $prefix, $nextSeq);
    }
}
