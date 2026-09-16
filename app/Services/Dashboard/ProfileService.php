<?php

namespace App\Services\Dashboard;

use App\Repositories\Dashboard\Contracts\ProfileRepositoryInterface;

class ProfileService
{
    protected $profileRepository;

    public function __construct(ProfileRepositoryInterface $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    public function getProfileInfo()
    {
        $admin = $this->profileRepository->getProfileInfo();
        return view('dashboard.profile-info', compact('admin'));
    }

    public function updateProfileInfo(array $data)
    {
        return $this->profileRepository->updateProfileInfo($data);
    }
    public function updateProfileEmail(array $data)
    {
        return $this->profileRepository->updateProfileEmail($data);
    }
    public function updateProfilePassword(array $data)
    {
        return $this->profileRepository->updateProfilePassword($data);
    }
}
