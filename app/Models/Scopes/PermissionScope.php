<?php

namespace App\Models\Scopes;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;

class PermissionScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    // public function apply(Builder $builder, Model $model): void
    // {
    //     $builder->when(Auth::check(), function (Builder $q) {

    //         $user = Auth::user();
    //         $role = $user->roles;
    //         // dd($role);
    //         $isExecutive = $role->contains('name_ar', 'مدير تنفيذي');
    //         $isSuperAdmin = $role->contains('name_en', 'super admin');


    //         if (!($isSuperAdmin)) {
    //             $q->where('district_id', $user->district_id);
    //         }
    //     });
    // }
    public function apply(Builder $builder, Model $model): void
{
    $builder->when(Auth::check(), function (Builder $q) {

        $user = Auth::user();
        $roles = $user->roles;

        // تحديد الدور الأساسي بناءً على نوع المستخدم
        $primaryRole = null;

        if ($user->type === 'admin') {
            // أمانة → المدير التنفيذي
            $primaryRole = $roles->firstWhere('name_ar', 'مدير تنفيذي');
        } else {
            // استشاري أو مقاول → أول role
            $primaryRole = $roles->first();
            // dd($primaryRole);
        }

        // إذا الدور الأساسي موجود وليس super admin
        // $isSuperAdmin = $primaryRole?->name_en === 'super admin';

        if (! $primaryRole) {
            $q->where('district_id', $user->district_id);
        }
    });
}

}
