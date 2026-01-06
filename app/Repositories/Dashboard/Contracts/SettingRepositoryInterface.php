<?php

namespace App\Repositories\Dashboard\Contracts;

interface SettingRepositoryInterface
{
    public function index();
    public function store($data);
    public function changeThemeMode($mode);
    public function changeLanguage($lang);
}
