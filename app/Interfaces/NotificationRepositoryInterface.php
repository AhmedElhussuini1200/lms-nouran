<?php

namespace App\Interfaces;

interface NotificationRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getByUser($userId);
    public function getUnreadCount($userId);
    public function markAsRead($id);
    public function markAllAsRead($userId);
}

