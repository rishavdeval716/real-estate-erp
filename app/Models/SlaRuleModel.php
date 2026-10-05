<?php

namespace App\Models;

use CodeIgniter\Model;

class SlaRuleModel extends Model
{
    protected $table            = 'sla_rules';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'category',
        'priority',
        'response_time_hours',
        'resolution_time_hours',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getResolutionHours(string $category, string $priority): int
    {
        $rule = $this->where('category', $category)
            ->where('priority', $priority)
            ->where('status', 'active')
            ->first();

        if ($rule) {
            return (int)$rule['resolution_time_hours'];
        }

        // Fallback default based on priority
        return match ($priority) {
            'urgent' => 4,
            'high'   => 12,
            'medium' => 24,
            default  => 48,
        };
    }
}
