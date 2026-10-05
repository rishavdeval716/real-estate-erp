<?php

namespace App\Models;

use CodeIgniter\Model;

class PropertyExpenseModel extends Model
{
    protected $table            = 'property_expenses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'expense_code',
        'category_id',
        'property_id',
        'property_unit_id',
        'project_id',
        'title',
        'expense_date',
        'payee_vendor',
        'amount',
        'tax_amount',
        'total_amount',
        'payment_method',
        'status',
        'receipt_file',
        'notes',
        'created_by',
        'approved_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function generateExpenseCode(): string
    {
        $year = date('Y');
        $prefix = "EXP-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('expense_code');
        $builder->like('expense_code', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['expense_code'])) {
            $parts = explode('-', $last['expense_code']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return $prefix . str_pad((string)$nextSeq, 6, '0', STR_PAD_LEFT);
    }

    public function getFilteredExpenses($filters = [], $limit = 20, $offset = 0)
    {
        $builder = $this->select('property_expenses.*, expense_categories.name as category_name, properties.title as property_title, projects.name as project_name')
            ->join('expense_categories', 'expense_categories.id = property_expenses.category_id', 'left')
            ->join('properties', 'properties.id = property_expenses.property_id', 'left')
            ->join('projects', 'projects.id = property_expenses.project_id', 'left');

        if (!empty($filters['category_id'])) {
            $builder->where('property_expenses.category_id', $filters['category_id']);
        }
        if (!empty($filters['property_id'])) {
            $builder->where('property_expenses.property_id', $filters['property_id']);
        }
        if (!empty($filters['status'])) {
            $builder->where('property_expenses.status', $filters['status']);
        }
        if (!empty($filters['date_from'])) {
            $builder->where('property_expenses.expense_date >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $builder->where('property_expenses.expense_date <=', $filters['date_to']);
        }
        if (!empty($filters['keyword'])) {
            $builder->groupStart()
                ->like('property_expenses.title', $filters['keyword'])
                ->orLike('property_expenses.expense_code', $filters['keyword'])
                ->orLike('property_expenses.payee_vendor', $filters['keyword'])
                ->groupEnd();
        }

        return $builder->orderBy('property_expenses.expense_date', 'DESC')
            ->limit($limit, $offset)
            ->find();
    }

    public function getExpenseSummary($filters = [])
    {
        $builder = $this->selectSum('total_amount', 'total_spend')
            ->selectSum('amount', 'subtotal')
            ->selectSum('tax_amount', 'total_tax')
            ->selectCount('id', 'total_count');

        if (!empty($filters['category_id'])) {
            $builder->where('category_id', $filters['category_id']);
        }
        if (!empty($filters['property_id'])) {
            $builder->where('property_id', $filters['property_id']);
        }
        if (!empty($filters['date_from'])) {
            $builder->where('expense_date >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $builder->where('expense_date <=', $filters['date_to']);
        }

        return $builder->first() ?? [
            'total_spend' => 0.00,
            'subtotal'    => 0.00,
            'total_tax'   => 0.00,
            'total_count' => 0,
        ];
    }
}
