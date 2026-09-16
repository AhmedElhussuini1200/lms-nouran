<?php

namespace App\Repositories\Dashboard\Contracts;

interface StatusRepositoryInterface
{
    public function allGrouped(): array;
    public function update($status, array $data);
}
