<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\LokasiKantor;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
        ]);

        LokasiKantor::create([
            'kota' => 'Manado',
            'alamat' => 'Jl. Bethesda No.32, Ranotana, Kec. Sario, Kota Manado, Sulawesi Utara',
            'latitude' => 1.4635002,
            'longitude' => 124.8331226,
            'radius' => 33,
            'is_used' => true,
        ]);

        $this->call([
            DepartemenSeeder::class,
            KaryawanSeeder::class,
            PresensiSeeder::class,
            PengajuanPresensiSeeder::class,
        ]);
    }
}
