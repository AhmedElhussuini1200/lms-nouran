<?php

namespace App\Repositories\Dashboard\Eloquent;

use App\Models\City;
use App\Models\Role;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Repositories\Dashboard\Contracts\AdminRepositoryInterface;

class AdminRepository implements AdminRepositoryInterface
{
    public function index(Request $request)
    {
        // $userType=auth()->user()->type;
        $user = auth()->user();
        // dd($user);
        $filters = [
            ['email', '!=', 'support@webstdy.com'],
            // ['email', '!=', 'Admin@webstdy.com'],
            ['id', '!=', $user->id],
            // ['distract_id','=',$user->city->distracts],

        ];
        // ✅ تحديد العرض حسب نوع المستخدم الحالي
        if ($user->type === 'consultant') {
            $filters[] = ['type', '=', 'consultant'];
        } elseif ($user->type === 'contractor') {
            $filters[] = ['type', '=', 'contractor'];
        }


  

        // ✅ فلاتر إضافية (اختيارية من الـ request)
        if ($request->filled('type') && $request->type !== 'all') {
            $filters[] = ['type', '=', $request->type];
        }

        return getModelData(
            new Admin(),
            andsFilters: $filters,
            relations: [
                'roles:id,name_ar,name_en',
                'company:id,name_ar,name_en',
                'sector:id,name_ar,name_en',
            ]
        );
    }



    public function show($admin)
    {
        return $admin;
    }



    public function store($data)
    {
        $admin = Admin::create($data);

        $admin->roles()->attach($data['roles']);
    }

    public function update($data, $admin)
    {
        $admin->update($data);
        $admin->roles()->sync($data['roles']);
    }

    public function destroy($data, $admin)
    {
        if ($data->ajax()) {
            $admin->delete();
        }
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
        $admin->update([
            'is_blocked' => $admin->is_blocked === 1 ? 0 : 1
        ]);
        return $admin;
    }
    public function isValid($request, $admin)
    {
        $admin->update([
            'is_valid' => $request->is_valid
        ]);
        return $admin;
    }

    public function find($id)
    {
        return Admin::find($id);
    }
}
