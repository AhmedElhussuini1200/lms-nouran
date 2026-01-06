<?php

namespace App\Repositories\Dashboard\Eloquent;

use App\Repositories\Dashboard\Contracts\ProfileRepositoryInterface;

class ProfileRepository implements ProfileRepositoryInterface
{
    public function getProfileInfo()
    {
        return auth()->user();
    }

    public function updateProfileInfo(array $data)
    {
        return auth()->user()->update($data);
    }

    public function updateProfileEmail(array $data)
    {
        return auth()->user()->update([
            'email' => $data['email']
        ]);
    }

    public function updateProfilePassword(array $data)
    {
        $admin = auth()->user();
        $admin->update($data);
        return $admin;
    }
}
