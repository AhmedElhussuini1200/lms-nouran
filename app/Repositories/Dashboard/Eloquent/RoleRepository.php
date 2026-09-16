<?php

namespace App\Repositories\Dashboard\Eloquent;

use App\Models\Role;
use App\Models\Ability;
use App\Repositories\Dashboard\Contracts\RoleRepositoryInterface;

class RoleRepository implements RoleRepositoryInterface
{
    public function index()
    {
        return Role::withCount(['admins', 'abilities'])->orderBy('id')->paginate(15);
    }

    public function store(array $data)
    {
        $role = Role::create($data);
        $role->abilities()->sync($data['abilities'] ?? []);
        return $role;
    }

    public function update(array $data, $role)
    {
        $role->update($data);
        $role->abilities()->sync($data['abilities'] ?? []);
        return $role;
    }

    public function destroy($role)
    {
        $role->abilities()->detach();
        return $role->delete();
    }

    public function abilitiesGrouped()
    {
        return Ability::orderBy('category')->orderBy('action')->get()->groupBy('category');
    }
}
