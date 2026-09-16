<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth('admin')->user();
        return $user && in_array($user->type, ['admin', 'teacher']);
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('video_url')) {
            $this->merge(['video_url' => $this->toEmbed($this->input('video_url'))]);
        }
    }

    protected function toEmbed(string $url): string
    {
        if (preg_match('/youtube\.com\/watch\?v=([^&]+)/', $url, $m)) {
            return 'https://www.youtube-nocookie.com/embed/' . $m[1] . '?rel=0';
        }
        if (preg_match('/youtu\.be\/([^?&]+)/', $url, $m)) {
            return 'https://www.youtube-nocookie.com/embed/' . $m[1] . '?rel=0';
        }
        return $url;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'grade' => ['required', Rule::in(['1_secondary', '2_secondary', '3_secondary'])],
            'subject' => ['nullable', 'string', 'max:255'],
            'video_url' => ['required', 'url', 'max:500', Rule::unique('videos', 'video_url')],
            'thumbnail_url' => ['nullable', 'url', 'max:500'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
