<?php

namespace App\Repositories\Dashboard\Contracts;

interface NotificationRepositoryInterface
{
    public function markAsRead($id);

    public function markAllAsRead();

    public function loadMore($type, $next);
    public function saveToken($data);

    public function changeSoundStatus($data);
}
