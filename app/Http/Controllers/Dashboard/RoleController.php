<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Dashboard\RoleService;

class RoleController extends Controller
{
    protected $service;

    public function __construct(RoleService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $this->authorize('view_roles');
        return $this->service->index();
    }

    public function create()
    {
        $this->authorize('create_roles');
        return $this->service->create();
    }

    public function store(Request $request)
    {
        $this->authorize('create_roles');
        return $this->service->store($request);
    }

    public function edit(Role $role)
    {
        $this->authorize('update_roles');
        return $this->service->edit($role);
    }

    public function update(Request $request, Role $role)
    {
        $this->authorize('update_roles');
        return $this->service->update($request, $role);
    }

    public function destroy(Request $request, Role $role)
    {
        $this->authorize('delete_roles');
        return $this->service->destroy($request, $role);
    }
}
