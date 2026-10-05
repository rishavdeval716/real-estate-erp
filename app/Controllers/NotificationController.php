<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\NotificationModel;
use App\Models\AuditLogModel;

class NotificationController extends BaseController
{
    protected $notificationModel;
    protected $auditModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
        $this->auditModel        = new AuditLogModel();
    }

    public function index()
    {
        $userId = session()->get('user_id');
        $filter = $this->request->getGet('filter'); // all, unread, high

        $builder = $this->notificationModel->builder();
        if ($userId) {
            $builder->groupStart()
                ->where('user_id', $userId)
                ->orWhere('user_id IS NULL')
                ->groupEnd();
        }

        if ($filter === 'unread') {
            $builder->where('is_read', 0);
        } elseif ($filter === 'urgent') {
            $builder->whereIn('priority', ['urgent', 'high']);
        }

        $notifications = $builder->orderBy('created_at', 'DESC')->get()->getResultArray();
        $unreadCount   = $this->notificationModel->getUnreadCount($userId);

        return view('notifications/index', [
            'title'         => 'Notification & Reminder Center',
            'notifications' => $notifications,
            'unreadCount'   => $unreadCount,
            'filter'        => $filter,
        ]);
    }

    public function markAsRead($id)
    {
        $notif = $this->notificationModel->find($id);
        if ($notif) {
            $this->notificationModel->update($id, [
                'is_read' => 1,
                'read_at' => date('Y-m-d H:i:s'),
            ]);
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success']);
        }

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        $userId = session()->get('user_id');
        $builder = $this->notificationModel->builder();
        if ($userId) {
            $builder->groupStart()
                ->where('user_id', $userId)
                ->orWhere('user_id IS NULL')
                ->groupEnd();
        }
        $builder->where('is_read', 0)->update([
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    public function create()
    {
        $title    = trim($this->request->getPost('title') ?? '');
        $message  = trim($this->request->getPost('message') ?? '');
        $type     = $this->request->getPost('type') ?? 'system';
        $priority = $this->request->getPost('priority') ?? 'medium';

        if (!$title || !$message) {
            return redirect()->back()->with('error', 'Title and message are required.');
        }

        $this->notificationModel->insert([
            'title'      => $title,
            'message'    => $message,
            'type'       => $type,
            'priority'   => $priority,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/notifications')->with('success', 'Reminder broadcasted successfully.');
    }
}
