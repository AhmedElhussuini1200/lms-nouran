<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NotificationService;
use App\Services\AuthService;

class NotificationController extends Controller
{
    protected $notificationService;
    protected $authService;

    public function __construct(NotificationService $notificationService, AuthService $authService)
    {
        $this->middleware('auth');
        $this->notificationService = $notificationService;
        $this->authService = $authService;
    }

    public function index()
    {
        $user = $this->authService->getUser();
        $notifications = $this->notificationService->getAllNotifications($user->id);
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $user = $this->authService->getUser();
        $notification = $this->notificationService->getNotificationById($id);
        
        if ($notification->user_id !== $user->id) {
            abort(403);
        }

        $this->notificationService->markAsRead($id);
        return back()->with('success', 'تم تحديد الإشعار كمقروء');
    }

    public function markAllAsRead()
    {
        $user = $this->authService->getUser();
        $this->notificationService->markAllAsRead($user->id);
        return back()->with('success', 'تم تحديد جميع الإشعارات كمقروءة');
    }
}
