<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'nik' => '1234567890123456',
                'nama' => 'Admin User',
                'alamat' => 'Jl. Contoh No. 1',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'no_hp' => '081234567890',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nik' => '9876543210987654',
                'nama' => 'Petugas User',
                'alamat' => 'Jl. Contoh No. 2',
                'password' => Hash::make('password123'),
                'role' => 'petugas',
                'no_hp' => '082345678901',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nik' => '5678901234567890',
                'nama' => 'Customer User',
                'alamat' => 'Jl. Contoh No. 3',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'no_hp' => '083456789012',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
