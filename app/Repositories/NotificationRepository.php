<?php

namespace App\Repositories;

use App\Interfaces\NotificationRepositoryInterface;
use App\Models\Notification;

class NotificationRepository extends BaseRepository implements NotificationRepositoryInterface
{
    public function __construct(Notification $model)
    {
        parent::__construct($model);
    }

    public function getByUser($userId)
    {
        return $this->model->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getUnreadCount($userId)
    {
        return $this->model->where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    public function markAsRead($id)
    {
        $notification = $this->find($id);
        $notification->update(['is_read' => true]);
        return $notification;
    }

    public function markAllAsRead($userId)
    {
        return $this->model->where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }
}

