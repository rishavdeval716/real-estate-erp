<?php

namespace App\Models;

use CodeIgniter\Model;

class AgentModel extends Model
{
    protected $table            = 'agents';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'agent_code',
        'user_id',
        'agent_type',
        'agency_name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'license_number',
        'pan_number',
        'commission_rate',
        'status',
        'address',
        'city',
        'notes',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function generateAgentCode(): string
    {
        $year = date('Y');
        $prefix = "AGT-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('agent_code');
        $builder->like('agent_code', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['agent_code'])) {
            $parts = explode('-', $last['agent_code']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return $prefix . str_pad((string)$nextSeq, 6, '0', STR_PAD_LEFT);
    }

    public function getAgentPerformance($agentId)
    {
        $agent = $this->find($agentId);
        if (!$agent) return null;

        $db = \Config\Database::connect();
        $userId = $agent['user_id'];

        // Commission history
        $commissions = [];
        $totalCommissionEarned = 0.00;
        $totalCommissionPaid   = 0.00;

        if ($userId) {
            $commissions = $db->table('commissions')
                ->select('commissions.*, bookings.booking_number, customers.first_name as cust_fname, customers.last_name as cust_lname')
                ->join('bookings', 'bookings.id = commissions.booking_id', 'left')
                ->join('customers', 'customers.id = bookings.customer_id', 'left')
                ->where('commissions.agent_user_id', $userId)
                ->orderBy('commissions.id', 'DESC')
                ->get()->getResultArray();

            foreach ($commissions as $c) {
                if ($c['status'] === 'Paid') {
                    $totalCommissionPaid += (float)$c['commission_amount'];
                }
                if (in_array($c['status'], ['Approved', 'Paid'])) {
                    $totalCommissionEarned += (float)$c['commission_amount'];
                }
            }
        }

        // Assigned leads count
        $leadCount = 0;
        $convertedLeads = 0;
        if ($userId) {
            $leadCount = $db->table('leads')->where('assigned_user_id', $userId)->where('deleted_at IS NULL')->countAllResults();
            $convertedLeads = $db->table('leads')->where('assigned_user_id', $userId)->where('lead_status', 'Converted')->where('deleted_at IS NULL')->countAllResults();
        }

        $agent['commissions'] = $commissions;
        $agent['total_commission_earned'] = $totalCommissionEarned;
        $agent['total_commission_paid']   = $totalCommissionPaid;
        $agent['leads_count']             = $leadCount;
        $agent['converted_leads']         = $convertedLeads;
        $agent['conversion_rate']         = $leadCount > 0 ? round(($convertedLeads / $leadCount) * 100, 1) : 0;

        return $agent;
    }
}
