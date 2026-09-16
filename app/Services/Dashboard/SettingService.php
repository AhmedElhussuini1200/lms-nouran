<?php

namespace App\Services\Dashboard;

use App\Models\Role;
use App\Repositories\Dashboard\Contracts\SettingRepositoryInterface;

class SettingService
{
    protected $settingRepository;

    public function __construct(SettingRepositoryInterface $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }
    public function index()
    {
        [$roles, $abilities] = $this->settingRepository->index();
        return view('dashboard.settings.index', ['roles' => $roles, 'abilities' => $abilities, 'modules' => Role::$modules]);
    }
    public function changeThemeMode($request)
    {
        $this->settingRepository->changeThemeMode($request);
        return redirect()->back();
    }
    public function changeLanguage($request)
    {
        $this->settingRepository->changeLanguage($request->lang);
        return redirect()->back();
    }
    public function store($data)
    {
        if (request()->isMethod('post')) {
            $this->settingRepository->store($data);
        }
    }

}
