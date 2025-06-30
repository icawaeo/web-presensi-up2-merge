<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LaporanPresensiKaryawanExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $bulan;
    protected $userId;

    public function __construct(string $bulan, string $userId)
    {
        $this->bulan = $bulan;
        $this->userId = $userId;
    }

    /**
    * @return \Illuminate\Database\Query\Builder
    */
    public function query()
    {
        return DB::table("presensi")
            ->where('user_id', $this->userId)
            ->whereMonth('tanggal_presensi', Carbon::make($this->bulan)->format('m'))
            ->whereYear('tanggal_presensi', Carbon::make($this->bulan)->format('Y'))
            ->orderBy("tanggal_presensi", "asc");
    }

    /**
    * @param mixed $presensi
    * @return array
    */
    public function map($presensi): array
    {
        return [
            Carbon::parse($presensi->tanggal_presensi)->format('d-m-Y'),
            $presensi->jam_masuk,
            $presensi->jam_keluar,
            $presensi->jam_masuk > '08:00:00' ? 'Terlambat' : 'Tepat Waktu'
        ];
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            'Tanggal',
            'Jam Masuk',
            'Jam Pulang',
            'Keterangan',
        ];
    }
}