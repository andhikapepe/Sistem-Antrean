<?php

namespace Database\Seeders;

use App\Models\ServiceUnit;
use Illuminate\Database\Seeder;

class ServiceUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $serviceUnits = [
            [
                'name' => 'Loket 1',
                'type' => 'counter',
                'location' => 'Lantai 1',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Loket 2',
                'type' => 'counter',
                'location' => 'Lantai 1',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Ruang Konsultasi 1',
                'type' => 'room',
                'location' => 'Lantai 2',
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($serviceUnits as $unit) {
            ServiceUnit::updateOrCreate(
                ['name' => $unit['name']], // Unik berdasarkan nama
                $unit
            );
        }
    }
}
