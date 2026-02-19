<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $year = date('Y');
        $app_name = 'Sistem Antrean Digital';
        $settings = [
            ['key' => 'app_name', 'value' => $app_name],
            ['key' => 'app_org_type', 'value' => 'Instansi XYZ'], // General: Perusahaan, Yayasan, dll
            ['key' => 'app_address', 'value' => 'Jl. Protokol No. 10, Jakarta'],
            ['key' => 'app_contact', 'value' => '(021) 555-0123'],
            ['key' => 'app_email', 'value' => 'info@instansi.go.id'],
            ['key' => 'app_copyright', 'value' => "© {$year} {$app_name}. Seluruh Hak Cipta Dilindungi."],
            ['key' => 'app_logo', 'value' => null],
            ['key' => 'meta_description', 'value' => 'Sistem manajemen antrean efisien.'],
            ['key' => 'meta_keywords', 'value' => 'antrean, sistem, manajemen'],
        ];

        foreach ($settings as $setting) {
            \App\Models\Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
