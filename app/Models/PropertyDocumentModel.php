<?php

namespace App\Models;

use CodeIgniter\Model;

class PropertyDocumentModel extends Model
{
    protected $table            = 'property_documents';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'document_code',
        'title',
        'document_category',
        'property_id',
        'project_id',
        'owner_id',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'issue_date',
        'expiry_date',
        'verification_status',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'notes',
        'uploaded_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function generateDocumentCode(): string
    {
        $year = date('Y');
        $prefix = "DOC-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('document_code');
        $builder->like('document_code', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['document_code'])) {
            $parts = explode('-', $last['document_code']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return $prefix . str_pad((string)$nextSeq, 6, '0', STR_PAD_LEFT);
    }
}
