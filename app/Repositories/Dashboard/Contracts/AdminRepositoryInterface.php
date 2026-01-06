<?php

namespace App\Repositories\Dashboard\Contracts;

use Illuminate\Http\Request;

interface AdminRepositoryInterface
{
    public function index(Request $request);
    public function store($data);
    public function update($data, $admin);
    public function destroy($data, $admin);
    public function deleteSelected($data);
    public function restoreSelected($data);
    public function show($admin);
    public function restore($data, $admin);
    public function status($admin);
    public function isValid($request, $admin);
    public function find($id);
}
