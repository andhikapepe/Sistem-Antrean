<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. ADMIN
        $this->command->warn('Membuat Akun Administrator...');
        $admin = User::firstOrCreate(
            ['email' => 'admin@mail.com'],
            ['name' => 'Administrator Utama', 'password' => Hash::make('password123')]
        );
        $admin->assignRole('admin');
        $this->command->info('Admin berhasil dibuat.');

        // 1. customer_service
        $this->command->warn('Membuat Akun customer service...');
        $admin = User::firstOrCreate(
            ['email' => 'customer_service@mail.com'],
            ['name' => 'customer service', 'password' => Hash::make('password123')]
        );
        $admin->assignRole('customer_service');
        $this->command->info('Customer service berhasil dibuat.');
    }
}
