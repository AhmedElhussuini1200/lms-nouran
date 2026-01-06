<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\NotificationService;

class NotificationController extends Controller
{
    protected $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }
    public function markAsRead($id)
    {
        return $this->service->markAsRead($id);
    }
    public function markAllAsRead()
    {
        return $this->service->markAllAsRead();
    }
    public function loadMore($type, $next)
    {
        return $this->service->loadMore($type, $next);
    }
    public function saveToken($request)
    {
        return $this->service->saveToken($request);
    }
    public function changeSoundStatus($request)
    {
        return $this->service->changeSoundStatus($request);
    }
}
