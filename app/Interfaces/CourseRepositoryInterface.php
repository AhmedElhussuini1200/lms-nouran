<?php

namespace App\Interfaces;

interface CourseRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getByTeacher($teacherId);
    public function getByGrade($grade);
    public function getRecent($limit = 5);
}

