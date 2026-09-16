<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Status;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Dashboard\StatusService;

class StatusController extends Controller
{
    protected $service;

    public function __construct(StatusService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $this->authorize('view_statuses');
        return $this->service->index();
    }

    public function update(Request $request, Status $status)
    {
        $this->authorize('update_statuses');
        return $this->service->update($request, $status);
    }
}
