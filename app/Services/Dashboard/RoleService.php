<?php

namespace App\Services\Dashboard;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Repositories\Dashboard\Contracts\RoleRepositoryInterface;

class RoleService
{
    protected $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    protected function authorizeAdmin(): void
    {
        abort_unless(auth('admin')->user()->type === 'admin', 403);
    }

    public function index()
    {
        $this->authorizeAdmin();
        $roles = $this->roleRepository->index();

        return view('dashboard.roles.index', compact('roles'));
    }

    public function create()
    {
        $this->authorizeAdmin();
        $grouped = $this->roleRepository->abilitiesGrouped();

        return view('dashboard.roles.create', compact('grouped'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'name_ar' => ['required', 'string', 'max:255', 'unique:roles,name_ar'],
            'name_en' => ['required', 'string', 'max:255', 'unique:roles,name_en'],
            'abilities' => ['nullable', 'array'],
            'abilities.*' => ['exists:abilities,id'],
        ]);

        $role = $this->roleRepository->store($data);

        if ($request->ajax()) {
            return response()->json(['message' => __('تم إنشاء الدور بنجاح'), 'url' => route('admin.roles.index')]);
        }

        return redirect()->route('admin.roles.index')->with('success', __('تم إنشاء الدور بنجاح'));
    }

    public function edit(Role $role)
    {
        $this->authorizeAdmin();
        $grouped = $this->roleRepository->abilitiesGrouped();

        return view('dashboard.roles.edit', compact('role', 'grouped'));
    }

    public function update(Request $request, Role $role)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'name_ar' => ['required', 'string', 'max:255', Rule::unique('roles', 'name_ar')->ignore($role->id)],
            'name_en' => ['required', 'string', 'max:255', Rule::unique('roles', 'name_en')->ignore($role->id)],
            'abilities' => ['nullable', 'array'],
            'abilities.*' => ['exists:abilities,id'],
        ]);

        $this->roleRepository->update($data, $role);

        if ($request->ajax()) {
            return response()->json(['message' => __('تم تحديث الدور بنجاح'), 'url' => route('admin.roles.index')]);
        }

        return redirect()->route('admin.roles.index')->with('success', __('تم تحديث الدور بنجاح'));
    }

    public function destroy(Request $request, Role $role)
    {
        $this->authorizeAdmin();
        $this->roleRepository->destroy($role);

        if ($request->ajax()) {
            return response()->json(['message' => __('تم حذف الدور بنجاح'), 'url' => route('admin.roles.index')]);
        }

        return redirect()->route('admin.roles.index')->with('success', __('تم حذف الدور بنجاح'));
    }
}
