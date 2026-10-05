<?php

namespace App\Models;

use CodeIgniter\Model;

class LeadSourceModel extends Model
{
    protected $table            = 'lead_sources';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'name',
        'slug',
        'description',
        'status',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'name'   => 'required|min_length[2]|max_length[100]',
        'slug'   => 'required|min_length[2]|max_length[100]|regex_match[/^[a-z0-9-]+$/]',
        'status' => 'required|in_list[active,inactive]',
    ];

    /**
     * Get active sources for dropdown
     */
    public function getActiveSources(): array
    {
        return $this->where('status', 'active')->orderBy('name', 'ASC')->findAll();
    }
}
