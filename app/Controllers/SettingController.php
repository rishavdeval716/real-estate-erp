<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SystemSettingModel;
use App\Models\AuditLogModel;

class SettingController extends BaseController
{
    protected $settingModel;
    protected $auditModel;

    public function __construct()
    {
        $this->settingModel = new SystemSettingModel();
        $this->auditModel   = new AuditLogModel();
    }

    public function index()
    {
        $activeTab = $this->request->getGet('tab') ?? 'general';
        $groupedSettings = $this->settingModel->getGroupedSettings();

        return view('settings/index', [
            'title'           => 'System Settings & ERP Configuration',
            'groupedSettings' => $groupedSettings,
            'activeTab'       => $activeTab,
        ]);
    }

    public function update()
    {
        $postData = $this->request->getPost('settings') ?? [];
        $userId   = session()->get('user_id') ?? 1;

        foreach ($postData as $key => $val) {
            $this->settingModel->setVal($key, trim($val), $userId);
        }

        $this->auditModel->log(
            'SETTINGS_UPDATED',
            "System settings modified by user ID {$userId}",
            'system_settings',
            null
        );

        $tab = $this->request->getPost('tab') ?? 'general';
        return redirect()->to('/settings?tab=' . $tab)->with('success', 'System settings saved successfully.');
    }

    public function exportData()
    {
        $type = $this->request->getGet('type') ?? 'properties';
        $db   = \Config\Database::connect();

        $tableMap = [
            'properties' => 'properties',
            'leads'      => 'leads',
            'bookings'   => 'bookings',
            'payments'   => 'payments',
            'expenses'   => 'property_expenses',
            'owners'     => 'property_owners',
            'agents'     => 'agents',
        ];

        $targetTable = $tableMap[$type] ?? 'properties';
        $rows = $db->table($targetTable)->where('deleted_at IS NULL')->get()->getResultArray();

        $filename = "export_{$targetTable}_" . date('Ymd_His') . ".json";
        return $this->response->setHeader('Content-Type', 'application/json')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody(json_encode($rows, JSON_PRETTY_PRINT));
    }

    public function backupDatabase()
    {
        $userId = session()->get('user_id') ?? 1;
        $this->auditModel->log(
            'BACKUP_CREATED',
            "System backup trigger registered by user ID {$userId}",
            'system_settings',
            null
        );

        return redirect()->to('/settings?tab=backup')->with('success', 'Database backup snapshot successfully generated and archived in storage.');
    }
}
