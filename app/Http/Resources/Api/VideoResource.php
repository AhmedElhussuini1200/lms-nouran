<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'grade' => $this->grade,
            'subject' => $this->subject,
            'embed_url' => $this->embed_url,
            'duration_seconds' => $this->duration_seconds,
            'views_count' => $this->views_count,
            'teacher' => $this->whenLoaded('teacher', fn () => [
                'id' => $this->teacher->id,
                'name' => $this->teacher->brand_name ?? $this->teacher->name,
            ]),
        ];
    }
}
