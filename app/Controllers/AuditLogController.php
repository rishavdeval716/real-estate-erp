<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;

class AuditLogController extends BaseController
{
    protected $auditLogModel;

    public function __construct()
    {
        $this->auditLogModel = new AuditLogModel();
    }

    /**
     * List audit logs with search, module filter, action filter, and pagination
     */
    public function index()
    {
        $search = trim($this->request->getGet('search') ?? '');
        $module = trim($this->request->getGet('module') ?? '');
        $action = trim($this->request->getGet('action') ?? '');

        $perPage = 20;
        $logs    = $this->auditLogModel->getFilteredLogs($search, $module, $action, $perPage);
        $pager   = $this->auditLogModel->pager;

        // Distinct modules & actions for filter dropdowns
        $db = \Config\Database::connect();
        $moduleRows = $db->table('audit_logs')->select('DISTINCT(module) as mname')->orderBy('module', 'ASC')->get()->getResultArray();
        $modules    = array_column($moduleRows, 'mname');

        $actionRows = $db->table('audit_logs')->select('DISTINCT(action) as aname')->orderBy('action', 'ASC')->get()->getResultArray();
        $actions    = array_column($actionRows, 'aname');

        $data = [
            'title'   => 'System Audit Logs - Real Estate ERP',
            'logs'    => $logs,
            'pager'   => $pager,
            'modules' => $modules,
            'actions' => $actions,
            'search'  => $search,
            'selectedModule' => $module,
            'selectedAction' => $action,
        ];

        return view('audit_logs/index', $data);
    }
}
