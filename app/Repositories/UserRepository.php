<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function getStudentsByParent($parentId)
    {
        return $this->model->whereHas('parents', function($query) use ($parentId) {
            $query->where('parent_id', $parentId);
        })->get();
    }

    public function getByRole($role)
    {
        return $this->model->where('role', $role)->get();
    }
}

