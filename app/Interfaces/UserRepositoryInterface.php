<?php

namespace App\Interfaces;

interface UserRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function findByEmail($email);
    public function getStudentsByParent($parentId);
    public function getByRole($role);
}

