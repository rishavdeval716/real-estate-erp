<?php

namespace App\Models;

use CodeIgniter\Model;

class SystemSettingModel extends Model
{
    protected $table            = 'system_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'setting_group',
        'setting_key',
        'setting_value',
        'setting_type',
        'description',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';

    public function getGroupedSettings(): array
    {
        $all = $this->orderBy('setting_group', 'ASC')->orderBy('id', 'ASC')->findAll();
        $grouped = [];
        foreach ($all as $item) {
            $grouped[$item['setting_group']][] = $item;
        }
        return $grouped;
    }

    public function getVal(string $key, $default = null)
    {
        $setting = $this->where('setting_key', $key)->first();
        return $setting ? $setting['setting_value'] : $default;
    }

    public function setVal(string $key, $val, $userId = null)
    {
        $existing = $this->where('setting_key', $key)->first();
        if ($existing) {
            return $this->update($existing['id'], [
                'setting_value' => $val,
                'updated_by'    => $userId,
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
        }
        return false;
    }
}
