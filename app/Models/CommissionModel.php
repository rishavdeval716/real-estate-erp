<?php

namespace App\Models;

use CodeIgniter\Model;

class CommissionModel extends Model
{
    protected $table            = 'commissions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'booking_id',
        'agent_user_id',
        'commission_rule_id',
        'booking_amount',
        'commission_amount',
        'status',
        'payable_date',
        'paid_date',
        'remarks',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get commissions with joined booking, agent, and rule details
     */
    public function getCommissionsWithDetails(array $filters = [], int $perPage = 15): array
    {
        $builder = $this->select('commissions.*, 
            bookings.booking_number,
            users.name as agent_name,
            users.email as agent_email,
            commission_rules.name as rule_name,
            commission_rules.commission_type,
            commission_rules.commission_value')
            ->join('bookings', 'bookings.id = commissions.booking_id', 'left')
            ->join('users', 'users.id = commissions.agent_user_id', 'left')
            ->join('commission_rules', 'commission_rules.id = commissions.commission_rule_id', 'left');

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $builder->groupStart()
                ->like('bookings.booking_number', $s)
                ->orLike('users.name', $s)
                ->orLike('commission_rules.name', $s)
                ->groupEnd();
        }

        if (!empty($filters['status'])) {
            $builder->where('commissions.status', $filters['status']);
        }

        if (!empty($filters['agent_user_id'])) {
            $builder->where('commissions.agent_user_id', $filters['agent_user_id']);
        }

        $sort = $filters['sort'] ?? 'commissions.created_at';
        $order = $filters['order'] ?? 'DESC';
        $builder->orderBy($sort, $order);

        $commissions = $builder->paginate($perPage);

        return [
            'commissions' => $commissions,
            'pager'       => $this->pager,
        ];
    }
}
