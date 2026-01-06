<?php

namespace App\Services\Dashboard;

use App\Repositories\Dashboard\Contracts\TrashRepositoryInterface;

class TrashService
{
    protected $trashRepository;

    public function __construct(TrashRepositoryInterface $trashRepository)
    {
        $this->trashRepository = $trashRepository;
    }
}
