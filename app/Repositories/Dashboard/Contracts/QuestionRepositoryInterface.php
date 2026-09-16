<?php

namespace App\Repositories\Dashboard\Contracts;

interface QuestionRepositoryInterface
{
    public function store($exam, array $data);
    public function update(array $data, $question);
    public function destroy($question);
}
