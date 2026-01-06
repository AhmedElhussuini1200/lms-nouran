<?php

namespace App\Services\Dashboard;

use App\Repositories\Dashboard\Contracts\NotificationRepositoryInterface;

class NotificationService
{
    protected $notificationRepository;

    public function __construct(NotificationRepositoryInterface $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    public function markAsRead($id)
    {
        $notification = $this->notificationRepository->markAsRead($id);
        $notification->markAsRead();
        return redirect($notification->data['url']);
    }
    public function markAllAsRead()
    {
        $this->notificationRepository->markAllAsRead();
        return redirect()->back();
    }
    public function loadMore($type, $next)
    {
        $notifications = $this->notificationRepository->loadMore($type, $next);
        return response()->json([
            'data' => $notifications,
            'isMoreExist' => $notifications->skip($next)->count() > 0,
        ]);
    }
    public function saveToken($request)
    {
        $this->notificationRepository->saveToken($request);
        return response()->json(['token saved successfully.']);
    }
    public function changeSoundStatus($request)
    {
        return response()->json($this->notificationRepository->changeSoundStatus($request));
    }
}
