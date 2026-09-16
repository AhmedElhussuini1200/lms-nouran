<?php

namespace App\Repositories\Dashboard\Contracts;

interface RoleRepositoryInterface
{
    public function index();
    public function store(array $data);
    public function update(array $data, $role);
    public function destroy($role);
    public function abilitiesGrouped();
}
