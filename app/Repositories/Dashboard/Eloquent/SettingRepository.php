<?php

namespace App\Repositories\Dashboard\Eloquent;

use App\Models\Ability;
use App\Models\Role;
use App\Models\Status;
use App\Repositories\Dashboard\Contracts\SettingRepositoryInterface;

class SettingRepository implements SettingRepositoryInterface
{
    public function index()
    {
        $roles = Role::withoutGlobalScopes()->with('abilities:id,category,action', 'admins:id')->whereNot('id', 2)->get();
        $abilities = Ability::select('id', 'name', 'category', 'action')->get();
        return [$roles, $abilities];
    }
    public function changeThemeMode($request)
    {
        session()->put('theme_mode', $request->mode);
    }
    public function changeLanguage($lang)
    {
        session()->put('locale', $lang);
    }
    public function store($data)
    {
        setting($data->validated())->save();
    }

    

    
}
