<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\ProfileService;
use App\Http\Requests\Dashboard\UpdateProfileInfoRequest;
use App\Http\Requests\Dashboard\UpdateProfileEmailRequest;
use App\Http\Requests\Dashboard\UpdateProfilePasswordRequest;

class ProfileController extends Controller
{
    protected $service;

    public function __construct(ProfileService $service)
    {
        $this->service = $service;
    }



    public function profileInfo()
    {
        return $this->service->getProfileInfo();
    }
    public function updateProfileInfo(UpdateProfileInfoRequest $request)
    {
        $this->service->updateProfileInfo($request->validated());
        // return redirect()->back()->with('success', 'Profile info updated successfully');
    }
    public function updateProfileEmail(UpdateProfileEmailRequest $request)
    {
        $this->service->updateProfileEmail($request->validated());
        // return redirect()->back()->with('success', 'Email updated successfully');
    }
    public function updateProfilePassword(UpdateProfilePasswordRequest $request)
    {
        $this->service->updateProfilePassword($request->validated());
    }
}
