<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'grade' => $this->grade,
            'subject' => $this->subject,
            'scheduled_at' => $this->scheduled_at?->toDateTimeString(),
            'price' => $this->price,
            'is_live' => (bool) $this->is_live,
            'teacher' => $this->whenLoaded('teacher', fn () => [
                'id' => $this->teacher->id,
                'name' => $this->teacher->brand_name ?? $this->teacher->name,
            ]),
        ];
    }
}
