<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth('admin')->user();
        return $user && in_array($user->type, ['admin', 'teacher']);
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('video_url')) {
            $url = $this->input('video_url');
            if (preg_match('/youtube\.com\/watch\?v=([^&]+)/', $url, $m)) {
                $url = 'https://www.youtube-nocookie.com/embed/' . $m[1] . '?rel=0';
            } elseif (preg_match('/youtu\.be\/([^?&]+)/', $url, $m)) {
                $url = 'https://www.youtube-nocookie.com/embed/' . $m[1] . '?rel=0';
            }
            $this->merge(['video_url' => $url]);
        }
    }

    public function rules(): array
    {
        $videoId = $this->route('video')?->id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'grade' => ['required', Rule::in(['1_secondary', '2_secondary', '3_secondary'])],
            'subject' => ['nullable', 'string', 'max:255'],
            'video_url' => ['required', 'url', 'max:500', Rule::unique('videos', 'video_url')->ignore($videoId)],
            'thumbnail_url' => ['nullable', 'url', 'max:500'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
