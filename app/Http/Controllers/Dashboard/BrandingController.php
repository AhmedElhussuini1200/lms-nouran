<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Dashboard\BrandingService;

class BrandingController extends Controller
{
    protected $service;

    public function __construct(BrandingService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $this->authorize('view_branding');
        return $this->service->index();
    }

    public function update(Request $request)
    {
        $this->authorize('update_branding');
        return $this->service->update($request);
    }
}
