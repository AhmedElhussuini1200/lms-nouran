<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\StoreAdminRequest;
use App\Http\Requests\Dashboard\UpdateAdminRequest;
use App\Services\Dashboard\AdminService;

class AdminController extends Controller
{
    protected $service;

    public function __construct(AdminService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $this->authorize('view_admins');
        return $this->service->index($request);
    }

    public function create()
    {
        $this->authorize('create_admins');
        return $this->service->create();
    }

    public function show(Admin $admin)
    {
        $this->authorize('view_admins');
        return $this->service->show($admin);
    }

    public function store(StoreAdminRequest $request)
    {
        $this->authorize('create_admins');
        return $this->service->store($request);
    }

    public function edit(Admin $admin)
    {
        $this->authorize('update_admins');
        return $this->service->edit($admin);
    }

    public function update(UpdateAdminRequest $request, Admin $admin)
    {
        $this->authorize('update_admins');
        return $this->service->update($request->validated(), $admin);
    }

    public function destroy(Request $request, Admin $admin)
    {
        $this->authorize('delete_admins');
        return $this->service->destroy($request, $admin);
    }

    public function deleteSelected(Request $request)
    {
        return $this->service->deleteSelected($request->all());
    }

    public function restoreSelected(Request $request)
    {
        return $this->service->restoreSelected($request);
    }

    public function restore(Request $request, Admin $admin)
    {
        return $this->service->restore($request, $admin);
    }

    public function status(Request $request, Admin $admin)
    {
        return response($this->service->status($admin));
    }
}
