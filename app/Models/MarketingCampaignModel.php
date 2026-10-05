<?php

namespace App\Models;

use CodeIgniter\Model;

class MarketingCampaignModel extends Model
{
    protected $table            = 'marketing_campaigns';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'campaign_code',
        'name',
        'campaign_type',
        'project_id',
        'property_id',
        'start_date',
        'end_date',
        'budget',
        'actual_spend',
        'leads_generated',
        'qualified_leads',
        'converted_leads',
        'status',
        'description',
        'notes',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function generateCampaignCode(): string
    {
        $year = date('Y');
        $prefix = "CMP-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('campaign_code');
        $builder->like('campaign_code', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['campaign_code'])) {
            $parts = explode('-', $last['campaign_code']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return $prefix . str_pad((string)$nextSeq, 6, '0', STR_PAD_LEFT);
    }

    public function getDashboardMetrics(): array
    {
        $totalCampaigns = $this->countAllResults();
        $activeCampaigns = $this->where('status', 'Active')->countAllResults();

        $totals = $this->selectSum('budget', 'total_budget')
            ->selectSum('actual_spend', 'total_spend')
            ->selectSum('leads_generated', 'total_leads')
            ->selectSum('qualified_leads', 'total_qualified')
            ->selectSum('converted_leads', 'total_conversions')
            ->first();

        $totalBudget      = (float)($totals['total_budget'] ?? 0);
        $totalSpend       = (float)($totals['total_spend'] ?? 0);
        $totalLeads       = (int)($totals['total_leads'] ?? 0);
        $totalQualified   = (int)($totals['total_qualified'] ?? 0);
        $totalConversions = (int)($totals['total_conversions'] ?? 0);

        $costPerLead   = $totalLeads > 0 ? round($totalSpend / $totalLeads, 2) : 0.00;
        $conversionRate= $totalLeads > 0 ? round(($totalConversions / $totalLeads) * 100, 1) : 0.0;

        return [
            'total_campaigns'   => $totalCampaigns,
            'active_campaigns'  => $activeCampaigns,
            'total_budget'      => $totalBudget,
            'total_spend'       => $totalSpend,
            'total_leads'       => $totalLeads,
            'total_qualified'   => $totalQualified,
            'total_conversions' => $totalConversions,
            'cost_per_lead'     => $costPerLead,
            'conversion_rate'   => $conversionRate,
        ];
    }
}
