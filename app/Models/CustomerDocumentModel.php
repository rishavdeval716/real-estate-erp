<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerDocumentModel extends Model
{
    protected $table            = 'customer_documents';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'customer_id',
        'document_type',
        'document_number',
        'file_name',
        'file_path',
        'verification_status',
        'verified_by',
        'verified_at',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getDocumentsForCustomer(int $customerId): array
    {
        return $this->select('customer_documents.*, users.name as verified_by_name')
            ->join('users', 'users.id = customer_documents.verified_by', 'left')
            ->where('customer_id', $customerId)
            ->orderBy('id', 'DESC')
            ->findAll();
    }
}
