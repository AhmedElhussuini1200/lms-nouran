<?php

namespace App\Repositories\Dashboard\Eloquent;

use App\Models\Setting;
use App\Repositories\Dashboard\Contracts\BrandingRepositoryInterface;

class BrandingRepository implements BrandingRepositoryInterface
{
    protected $keys = ['site_name', 'primary_color', 'secondary_color', 'logo'];

    public function all(): array
    {
        $stored = Setting::whereIn('key', $this->keys)->pluck('value', 'key')->toArray();
        return [
            'site_name' => $stored['site_name'] ?? 'منصة نوران التعليمية',
            'primary_color' => $stored['primary_color'] ?? '#1b84ff',
            'secondary_color' => $stored['secondary_color'] ?? '#17c653',
            'logo' => $stored['logo'] ?? null,
        ];
    }

    public function update(array $data): array
    {
        foreach ($this->keys as $key) {
            if (array_key_exists($key, $data)) {
                Setting::updateOrCreate(['key' => $key], ['value' => $data[$key]]);
            }
        }
        return $this->all();
    }
}
