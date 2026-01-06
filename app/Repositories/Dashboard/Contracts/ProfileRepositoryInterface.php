<?php

namespace App\Repositories\Dashboard\Contracts;

interface ProfileRepositoryInterface
{
    public function getProfileInfo();
    public function updateProfileInfo(array $data);
    public function updateProfileEmail(array $data);
    public function updateProfilePassword(array $data);
}
