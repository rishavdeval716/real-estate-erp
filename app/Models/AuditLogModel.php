<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table            = 'audit_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'action',
        'module',
        'record_id',
        'description',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    // Dates
    protected $useTimestamps = false;

    /**
     * Record an audit log entry
     * Supports both:
     * - record(string $action, string $module, ?int $recordId, ?string $description, ?int $userId)
     * - record(?int $userId, string $action, string $module, ?int $recordId, ?string $description)
     */
    public static function record($param1, $param2, $param3 = null, $param4 = null, $param5 = null): bool
    {
        try {
            $request = service('request');
            $session = session();

            if (is_numeric($param1) || (is_null($param1) && is_string($param2) && is_string($param3))) {
                // Signature: ($userId, $action, $module, $recordId, $description)
                $userId      = $param1 ? (int)$param1 : null;
                $action      = (string) $param2;
                $module      = (string) $param3;
                $recordId    = $param4 ? (int)$param4 : null;
                $description = $param5;
            } else {
                // Standard signature: ($action, $module, $recordId, $description, $userId)
                $action      = (string) $param1;
                $module      = (string) $param2;
                $recordId    = $param3 ? (int)$param3 : null;
                $description = $param4;
                $userId      = $param5 ? (int)$param5 : null;
            }

            $actualUserId = $userId ?: $session->get('user_id');
            $ipAddress    = $request ? $request->getIPAddress() : '127.0.0.1';
            $userAgent    = $request && $request->getUserAgent() ? (string) $request->getUserAgent() : 'CLI/Internal';

            $model = new self();
            $model->insert([
                'user_id'     => $actualUserId,
                'action'      => strtoupper($action),
                'module'      => $module,
                'record_id'   => $recordId,
                'description' => $description,
                'ip_address'  => substr($ipAddress, 0, 45),
                'user_agent'  => substr($userAgent, 0, 255),
                'created_at'  => date('Y-m-d H:i:s'),
            ]);

            return true;
        } catch (\Throwable $e) {
            log_message('error', 'AuditLog Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Instance helper to log an audit event
     */
    public function log(string $action, ?string $description = null, string $module = 'System', ?int $recordId = null, ?int $userId = null): bool
    {
        return self::record($action, $module, $recordId, $description, $userId);
    }

    /**
     * Get paginated audit logs with user details and filtering
     */
    public function getFilteredLogs(?string $search = null, ?string $module = null, ?string $action = null, int $perPage = 15)
    {
        $builder = $this->select('audit_logs.*, users.name as user_name, users.email as user_email')
                        ->join('users', 'users.id = audit_logs.user_id', 'left');

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('audit_logs.description', $search)
                    ->orLike('audit_logs.ip_address', $search)
                    ->orLike('users.name', $search)
                    ->orLike('users.email', $search)
                    ->groupEnd();
        }

        if (!empty($module)) {
            $builder->where('audit_logs.module', $module);
        }

        if (!empty($action)) {
            $builder->where('audit_logs.action', strtoupper($action));
        }

        return $builder->orderBy('audit_logs.id', 'DESC')->paginate($perPage);
    }
}
