<?php

namespace Database\Seeders;

use App\Models\ServiceUnit;
use App\Models\QueueCategory;
use Illuminate\Database\Seeder;

class UnitCategorySeeder extends Seeder
{
    public function run(): void
    {
        $units = ServiceUnit::all();
        $categories = QueueCategory::all();

        if ($units->isEmpty() || $categories->isEmpty()) {
            return;
        }

        // Contoh skenario:
        // Loket 1 melayani semua kategori
        $units->first()->categories()->attach($categories->pluck('id'));

        // Loket lainnya hanya melayani Umum (A) dan Prioritas (B)
        foreach ($units->skip(1) as $unit) {
            $unit->categories()->attach([
                $categories->where('prefix', 'A')->first()->id,
                $categories->where('prefix', 'B')->first()->id,
            ]);
        }
    }
}
