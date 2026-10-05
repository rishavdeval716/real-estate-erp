<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'role_id',
        'title',
        'message',
        'type',
        'priority',
        'related_module',
        'related_id',
        'link_url',
        'is_read',
        'read_at',
        'created_at',
    ];

    protected $useTimestamps = false;

    public function getUserNotifications($userId = null, $roleId = null, $limit = 20)
    {
        $builder = $this->orderBy('created_at', 'DESC')->limit($limit);

        if ($userId) {
            $builder->groupStart()
                ->where('user_id', $userId)
                ->orWhere('user_id IS NULL')
                ->groupEnd();
        }

        return $builder->find();
    }

    public function getUnreadCount($userId = null)
    {
        $builder = $this->where('is_read', 0);
        if ($userId) {
            $builder->groupStart()
                ->where('user_id', $userId)
                ->orWhere('user_id IS NULL')
                ->groupEnd();
        }
        return $builder->countAllResults();
    }
}
