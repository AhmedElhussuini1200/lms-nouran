<?php

namespace App\Repositories\Dashboard\Eloquent;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Repositories\Dashboard\Contracts\AdminRepositoryInterface;

class AdminRepository implements AdminRepositoryInterface
{
    public function index(Request $request)
    {
        $user = auth('admin')->user();

        $query = Admin::with('roles:id,name_ar,name_en')
            ->where('id', '!=', $user->id)
            ->orderBy('created_at', 'desc');

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if ($request->filled('grade') && $request->grade !== 'all') {
            $query->where('grade', $request->grade);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn ($w) => $w->where('name', 'like', "%$q%")->orWhere('email', 'like', "%$q%")->orWhere('phone', 'like', "%$q%"));
        }

        if ($request->ajax() && $request->has('draw')) {
            return getModelData(new Admin(), relations: ['roles:id,name_ar,name_en']);
        }

        return $query->paginate(15);
    }

    public function show($admin)
    {
        return $admin->load(['roles', 'courses', 'videos', 'students:id,name,grade', 'parents:id,name']);
    }

    public function store($data)
    {
        $admin = Admin::create($data);

        if (!empty($data['roles'])) {
            $admin->roles()->attach($data['roles']);
        }

        return $admin;
    }

    public function update($data, $admin)
    {
        $admin->update($data);

        if (array_key_exists('roles', $data)) {
            $admin->roles()->sync($data['roles'] ?? []);
        }

        return $admin;
    }

    public function destroy($data, $admin)
    {
        $admin->delete();
    }

    public function deleteSelected($ids)
    {
        return Admin::whereIn('id', $ids)->delete();
    }

    public function restoreSelected($data)
    {
        return Admin::withTrashed()->whereIn('id', $data['selected_items_ids'])->restore();
    }

    public function restore($data, $admin)
    {
        $admin->restore();
    }

    public function status($admin)
    {
        $admin->update(['is_blocked' => ($admin->is_blocked ?? 0) == 1 ? 0 : 1]);
        return $admin;
    }

    public function isValid($request, $admin)
    {
        $admin->update(['is_valid' => $request->is_valid]);
        return $admin;
    }

    public function find($id)
    {
        return Admin::find($id);
    }
}
