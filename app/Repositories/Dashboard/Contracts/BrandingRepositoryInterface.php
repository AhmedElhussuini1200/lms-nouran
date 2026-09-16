<?php

namespace App\Repositories\Dashboard\Contracts;

interface BrandingRepositoryInterface
{
    public function all(): array;
    public function update(array $data): array;
}
