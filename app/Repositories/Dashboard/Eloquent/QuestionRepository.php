<?php

namespace App\Repositories\Dashboard\Eloquent;

use App\Models\Question;
use App\Repositories\Dashboard\Contracts\QuestionRepositoryInterface;

class QuestionRepository implements QuestionRepositoryInterface
{
    public function store($exam, array $data)
    {
        $data['exam_id'] = $exam->id;
        return Question::create($data);
    }

    public function update(array $data, $question)
    {
        $question->update($data);
        return $question;
    }

    public function destroy($question)
    {
        return $question->delete();
    }
}
