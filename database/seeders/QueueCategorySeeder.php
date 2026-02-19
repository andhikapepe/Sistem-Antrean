<?php

namespace Database\Seeders;

use App\Models\QueueCategory;
use Illuminate\Database\Seeder;

class QueueCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Layanan Umum',
                'prefix' => 'A',
                'sort_order' => 1,
                'color' => 'indigo', // Sesuai class Tailwind yang kita pakai di Kiosk
                'is_active' => true,
            ],
            [
                'name' => 'Layanan Prioritas',
                'prefix' => 'B',
                'sort_order' => 2,
                'color' => 'emerald',
                'is_active' => true,
            ],
            [
                'name' => 'Konsultasi Khusus',
                'prefix' => 'C',
                'sort_order' => 3,
                'color' => 'orange',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            QueueCategory::create($category);
        }
    }
}
