<?php

namespace App\Services;

use App\Interfaces\NotificationRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    protected $notificationRepository;

    public function __construct(NotificationRepositoryInterface $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    public function getAllNotifications($userId, $paginate = 20)
    {
        $notifications = $this->notificationRepository->getByUser($userId);
        
        // Convert collection to paginated results
        $page = request()->get('page', 1);
        $perPage = $paginate;
        $items = $notifications->forPage($page, $perPage);
        $total = $notifications->count();
        
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function getNotificationById($id)
    {
        return $this->notificationRepository->find($id);
    }

    public function createNotification(array $data)
    {
        return $this->notificationRepository->create($data);
    }

    public function markAsRead($id)
    {
        return $this->notificationRepository->markAsRead($id);
    }

    public function markAllAsRead($userId)
    {
        return $this->notificationRepository->markAllAsRead($userId);
    }

    public function getUnreadCount($userId)
    {
        return $this->notificationRepository->getUnreadCount($userId);
    }
}

