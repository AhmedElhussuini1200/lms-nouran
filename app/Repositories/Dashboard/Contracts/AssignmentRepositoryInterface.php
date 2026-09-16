<?php

namespace App\Repositories\Dashboard\Contracts;

use Illuminate\Http\Request;

interface AssignmentRepositoryInterface
{
    public function index(Request $request);
    public function store(array $data);
    public function update(array $data, $assignment);
    public function destroy($assignment);
    public function show($assignment);
    public function find($id);
    public function submit($assignment, array $data);
    public function grade($submission, array $data);
    public function mySubmission($assignment, $studentId);
}
