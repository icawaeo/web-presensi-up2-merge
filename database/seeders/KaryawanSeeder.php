<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Karyawan::create([
            'user_id' => '12345',
            'departemen_id' => '1',
            'nama_lengkap' => 'Dummy',
            'foto' => '12345.jpg',
            'jabatan' => 'Karyawan',
            'telepon' => '08123456789',
            'email' => 'user-testing@gmail.com',
            'password' => Hash::make('password'),
        ]);

        Karyawan::create([
            'user_id' => '12346',
            'departemen_id' => '2',
            'nama_lengkap' => 'Wati',
            'jabatan' => 'Karyawan',
            'telepon' => '08123456780',
            'email' => 'wati@gmail.com',
            'password' => Hash::make('password'),
        ]);

        Karyawan::create([
            'user_id' => '12347',
            'departemen_id' => '3',
            'nama_lengkap' => 'Mawar',
            'jabatan' => 'Karyawan',
            'telepon' => '08123456781',
            'email' => 'mawar@gmail.com',
            'password' => Hash::make('password'),
        ]);

        Karyawan::create([
            'user_id' => '12348',
            'departemen_id' => '3',
            'nama_lengkap' => 'Melati',
            'jabatan' => 'Karyawan',
            'telepon' => '08123456786',
            'email' => 'melati@gmail.com',
            'password' => Hash::make('password'),
        ]);
    }
}
