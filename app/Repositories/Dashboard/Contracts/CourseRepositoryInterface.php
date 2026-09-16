<?php

namespace App\Repositories\Dashboard\Contracts;

use Illuminate\Http\Request;

interface CourseRepositoryInterface
{
    public function index(Request $request);
    public function store(array $data);
    public function update(array $data, $course);
    public function destroy($course);
    public function show($course);
    public function find($id);
}
