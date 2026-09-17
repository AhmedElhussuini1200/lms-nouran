<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'grade' => $this->grade,
            'subject' => $this->subject,
            'exam_date' => $this->exam_date?->toDateTimeString(),
            'duration_minutes' => $this->duration_minutes,
            'total_marks' => $this->total_marks,
            'max_attempts' => $this->max_attempts,
            'questions' => $this->whenLoaded('questions', fn () => $this->questions->map(fn ($q) => [
                'id' => $q->id,
                'type' => $q->type,
                'question' => $q->question,
                'options' => $q->options,
                'marks' => $q->marks,
            ])),
        ];
    }
}
