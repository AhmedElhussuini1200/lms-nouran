<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Role;
use App\Models\Ability;
use App\Models\GiftCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\Dashboard\SettingService;
use anlutro\LaravelSettings\Facades\Setting;
use App\Http\Requests\Dashboard\UpdateSettingsRequest;
use App\Http\Requests\Dashboard\UpdateGeneralSettingsRequest;

class SettingController extends Controller
{
    protected $service;

    public function __construct(SettingService $service)
    {
        $this->service = $service;
    }
    public function index()
    {
        $this->authorize('view_settings');
        return $this->service->index();
    }
    public function changeThemeMode(Request $request)
    {
        return $this->service->changeThemeMode($request);
    }

    public function changeLanguage(Request $request)
    {
        return $this->service->changeLanguage($request);
    }

    // public function store(UpdateGeneralSettingsRequest $request)
    // {
    //     $this->service->store($request);
    // }
}
