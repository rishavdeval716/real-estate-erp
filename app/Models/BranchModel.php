<?php

namespace App\Models;

use CodeIgniter\Model;

class BranchModel extends Model
{
    protected $table            = 'branches';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'company_id',
        'name',
        'code',
        'manager_name',
        'phone',
        'email',
        'address',
        'city',
        'state',
        'country',
        'pincode',
        'status',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'company_id'   => 'required|is_not_unique[companies.id]',
        'name'         => 'required|min_length[3]|max_length[150]',
        'code'         => 'required|min_length[2]|max_length[50]|is_unique[branches.code,id,{id}]',
        'manager_name' => 'permit_empty|max_length[150]',
        'email'        => 'permit_empty|valid_email|max_length[150]',
        'phone'        => 'permit_empty|max_length[30]',
        'status'       => 'required|in_list[active,inactive]',
    ];

    protected $validationMessages = [
        'company_id' => [
            'required'      => 'A company must be selected.',
            'is_not_unique' => 'The selected company does not exist.',
        ],
        'code' => [
            'is_unique' => 'This branch code already exists. Please choose a unique code.',
        ],
    ];

    /**
     * Get paginated branches with company details and filters
     */
    public function getFilteredBranches(?string $search = null, ?string $status = null, int $perPage = 10)
    {
        $builder = $this->select('branches.*, companies.name as company_name')
                        ->join('companies', 'companies.id = branches.company_id', 'left');

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('branches.name', $search)
                    ->orLike('branches.code', $search)
                    ->orLike('branches.city', $search)
                    ->orLike('branches.manager_name', $search)
                    ->groupEnd();
        }

        if (!empty($status) && in_array($status, ['active', 'inactive'])) {
            $builder->where('branches.status', $status);
        }

        return $builder->orderBy('branches.id', 'DESC')->paginate($perPage);
    }
}
