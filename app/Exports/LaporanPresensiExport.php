<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LaporanPresensiExport implements FromQuery, WithHeadings, ShouldAutoSize
{
    protected $bulan;

    public function __construct(string $bulan)
    {
        $this->bulan = $bulan;
    }

    /**
    * @return \Illuminate\Database\Query\Builder
    */
    public function query()
    {
        return DB::table("presensi as p")
            ->join('karyawan as k', 'p.user_id', '=', 'k.user_id')
            ->join('departemen as d', 'k.departemen_id', '=', 'd.id')
            ->whereMonth('tanggal_presensi', Carbon::make($this->bulan)->format('m'))
            ->whereYear('tanggal_presensi', Carbon::make($this->bulan)->format('Y'))
            ->select(
                'k.nama_lengkap as nama_karyawan',
                'd.nama as nama_departemen',
                DB::raw("COUNT(p.user_id) as total_kehadiran"),
                DB::raw("SUM(IF (p.jam_masuk > '08:00', 1, 0)) as total_terlambat")
            )
            ->groupBy(
                'p.user_id',
                'k.nama_lengkap',
                'd.nama'
            )
            ->orderBy("k.nama_lengkap", "asc");
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            'Nama Lengkap',
            'Pekerjaan',
            'Jumlah Kehadiran',
            'Jumlah Terlambat',
        ];
    }
}