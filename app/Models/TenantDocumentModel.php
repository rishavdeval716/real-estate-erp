<?php

namespace App\Models;

use CodeIgniter\Model;

class TenantDocumentModel extends Model
{
    protected $table            = 'tenant_documents';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tenant_id',
        'document_type',
        'document_number',
        'file_name',
        'file_path',
        'verification_status',
        'verified_by',
        'verification_date',
        'expiry_date',
        'remarks',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getByTenant(int $tenantId): array
    {
        return $this->select('tenant_documents.*, u.name as verified_by_name')
            ->join('users u', 'u.id = tenant_documents.verified_by', 'left')
            ->where('tenant_id', $tenantId)
            ->orderBy('id', 'DESC')
            ->findAll();
    }
}
