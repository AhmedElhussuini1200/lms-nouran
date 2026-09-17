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
        if ($this->filled('video_url') && youtubeEmbed($this->input('video_url'))) {
            $this->merge(['video_url' => youtubeEmbed($this->input('video_url'))]);
        }
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'grade' => ['required', Rule::in(['1_secondary', '2_secondary', '3_secondary'])],
            'subject' => ['nullable', 'string', 'max:255'],
            'video_url' => ['required', 'url', 'max:500', Rule::unique('videos', 'video_url'), function ($attr, $val, $fail) {
                if (! youtubeId($val)) {
                    $fail(__('رابط اليوتيوب غير صالح — الصق رابط مشاهدة حقيقي'));
                }
            }],
            'thumbnail_url' => ['nullable', 'url', 'max:500'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
