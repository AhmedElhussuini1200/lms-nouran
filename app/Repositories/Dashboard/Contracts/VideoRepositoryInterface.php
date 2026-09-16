<?php

namespace App\Repositories\Dashboard\Contracts;

use Illuminate\Http\Request;

interface VideoRepositoryInterface
{
    public function index(Request $request);
    public function store(array $data);
    public function update(array $data, $video);
    public function destroy($video);
    public function show($video);
    public function find($id);
    public function incrementViews($video);
}
