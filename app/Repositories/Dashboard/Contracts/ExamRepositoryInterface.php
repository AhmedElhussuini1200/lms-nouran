<?php

namespace App\Repositories\Dashboard\Contracts;

use Illuminate\Http\Request;

interface ExamRepositoryInterface
{
    public function index(Request $request);
    public function store(array $data);
    public function update(array $data, $exam);
    public function destroy($exam);
    public function show($exam);
    public function find($id);
    public function submit($exam, array $data);
    public function grade($result, array $data);
    public function myResult($exam, $studentId);
}
