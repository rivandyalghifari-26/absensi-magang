<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Helpers\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'company_name', 'value' => 'PT BANK INDEX SELINDO'],
            ['key' => 'office_latitiude', 'value' => ''],
            ['key' => 'office_longitude', 'value' => ''],
            ['key' => 'geofence_radius', 'value' => '100'],
            ['key' => 'late_threshold', 'value' => '08:00:00'],
            ['key' => 'wa_token', 'value' => ''],
            ['key' => 'wa_group_id', 'value' => ''],
        ];
        foreach($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }
}
