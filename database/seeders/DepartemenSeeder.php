<?php

namespace Database\Seeders;

use App\Models\Departemen;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartemenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Departemen::create([
            'kode' => 'D001',
            'nama' => 'Admin',
        ]);
        Departemen::create([
            'kode' => 'D002',
            'nama' => 'Cleaning Service',
        ]);
        Departemen::create([
            'kode' => 'D003',
            'nama' => 'Security',
        ]);
        Departemen::create([
            'kode' => 'D004',
            'nama' => 'Driver',
        ]);
    }
}
