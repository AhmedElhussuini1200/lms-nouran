<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['key' => 'site_name', 'value' => 'نظام نوران لإدارة التعلم'],
            ['key' => 'site_description', 'value' => 'نظام نوران لإدارة التعلم'],
            ['key' => 'site_logo', 'value' => 'logo.png'],
            ['key' => 'site_favicon', 'value' => 'favicon.ico'],
            ['key' => 'address', 'value' => 'شارع أحمد بن حنبل، حي اليريان، الرياض، المملكة العربية السعودية'],
            ['key' => 'phone', 'value' => '+966551646971'],
            ['key' => 'email', 'value' => 'asdad@info.com'],
            ['key' => 'privacy_policy_ar', 'value' => 'lorem ipsum dolor sit amet, consectetur adipiscing elit.'],
            ['key' => 'privacy_policy_en', 'value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'],
             ['key' => 'terms_ar', 'value' => 'lorem ipsum dolor sit amet, consectetur adipiscing elit'],
             ['key' => 'terms_en', 'value' => 'lorem ipsum dolor sit amet, consectetur adipiscing elit'],
             ['key' => 'moyasar_commission', 'value' => 2.76],
             ['key' => 'moyasar_value', 'value' => 1],
        ];

        foreach ($data as $setting) {
            Setting::create($setting);
        }
    }
}
