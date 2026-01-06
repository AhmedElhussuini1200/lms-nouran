<?php

namespace App\Services\Dashboard;

use App\Repositories\Dashboard\Contracts\RoleRepositoryInterface;

class RoleService
{
    protected $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }
}
