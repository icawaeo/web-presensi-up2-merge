<?php

namespace App\Http\Controllers;

use App\Enums\StatusPengajuanPresensi;
use App\Models\Departemen;
use App\Models\Karyawan;
use App\Models\LokasiKantor;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PresensiController extends Controller
{
    public function index()
    {
        $title = 'Presensi';
        $presensiKaryawan = DB::table('presensi')
            ->where('user_id', auth()->guard('karyawan')->user()->user_id)
            ->where('tanggal_presensi', date('Y-m-d'))
            ->first();
        $lokasiKantor = LokasiKantor::where('is_used', true)->first();
        return view('dashboard.presensi.index', compact('title', 'presensiKaryawan', 'lokasiKantor'));
    }

    public function create()
    {
        $hariIni = Carbon::now();
        $lokasiKantor = LokasiKantor::where('is_used', true)->first();

        // Validasi jika admin belum mengatur lokasi kantor
        if (!$lokasiKantor) {
            return redirect()->route('karyawan.dashboard')->with('error', 'Lokasi kantor belum diatur. Silakan hubungi admin.');
        }

        $presensiKaryawan = DB::table('presensi')
            ->where('user_id', auth()->guard('karyawan')->user()->user_id)
            ->where('tanggal_presensi', $hariIni->toDateString())
            ->first();

        return view('dashboard.presensi.create', [
            'title' => "Presensi",
            'presensiKaryawan' => $presensiKaryawan,
            'lokasiKantor' => $lokasiKantor,
        ]);
    } 
    public function store(Request $request)
    {
        $jenisPresensi = $request->jenis;
        $user_id = auth()->guard('karyawan')->user()->user_id;
        $tglPresensi = date('Y-m-d');
        $jam = date('H:i:s');
        $lokasi = $request->lokasi;

        // Logika pengecekan radius untuk menandai status
        $lokasiKantor = LokasiKantor::where('is_used', true)->first();
        $lokasiUser = explode(",", $lokasi);
        $langtitudeUser = $lokasiUser[0];
        $longtitudeUser = $lokasiUser[1];

        $jarak = round($this->validation_radius_presensi($lokasiKantor->latitude, $lokasiKantor->longitude, $langtitudeUser, $longtitudeUser), 2);
        
        $statusRadius = ($jarak > $lokasiKantor->radius) ? 'out' : 'in';

        $folderPath = "public/unggah/presensi/";
        $folderName = $user_id . "-" . $tglPresensi . "-" . $jenisPresensi;
        $image = $request->image;
        $imageParts = explode(";base64", $image);
        $imageBase64 = base64_decode($imageParts[1]);
        $fileName = $folderName . ".png";
        $file = $folderPath . $fileName;

        if ($jenisPresensi == "masuk") {
            $data = [
                "user_id" => $user_id,
                "tanggal_presensi" => $tglPresensi,
                "jam_masuk" => $jam,
                "foto_masuk" => $fileName,
                "lokasi_masuk" => $lokasi,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ];
            $store = DB::table('presensi')->insert($data);
        } elseif ($jenisPresensi == "keluar") {
            $data = [
                "jam_keluar" => $jam,
                "foto_keluar" => $fileName,
                "lokasi_keluar" => $lokasi,
                "updated_at" => Carbon::now(),
            ];
            $store = DB::table('presensi')
                ->where('user_id', auth()->guard('karyawan')->user()->user_id)
                ->where('tanggal_presensi', date('Y-m-d'))
                ->update($data);
        }

        if ($store) {
            Storage::put($file, $imageBase64);
        } else {
            return response()->json([
                'status' => 500,
                'success' => false,
                'message' => "Gagal presensi",
            ]);
        }

        return response()->json([
            'status' => 200,
            'data' => $data,
            'success' => true,
            'message' => "Berhasil presensi",
            'jenis_presensi' => $jenisPresensi,
            'status_radius' => $statusRadius, // Kirim status radius ke frontend
        ]);
    }

    public function validation_radius_presensi($langtitudeKantor, $longtitudeKantor, $langtitudeUser, $longtitudeUser)
    {
        $theta = $longtitudeKantor - $longtitudeUser;
        $hitungKoordinat = (sin(deg2rad($langtitudeKantor)) * sin(deg2rad($langtitudeUser))) + (cos(deg2rad($langtitudeKantor)) * cos(deg2rad($langtitudeUser)) * cos(deg2rad($theta)));
        $miles = rad2deg(acos($hitungKoordinat)) * 60 * 1.1515;

        // $feet = $miles * 5280;
        // $yards = $feet / 3;

        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return $meters;
    }

    public function history()
    {
        $title = 'Riwayat Presensi';
        $riwayatPresensi = DB::table("presensi")
            ->where('user_id', auth()->guard('karyawan')->user()->user_id)
            ->orderBy("tanggal_presensi", "asc")
            ->paginate(10);
        $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        return view('dashboard.presensi.history', compact('title', 'riwayatPresensi', 'bulan'));
    }

    public function searchHistory(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $data = DB::table('presensi')
            ->where('user_id', auth()->guard('karyawan')->user()->user_id)
            ->whereMonth('tanggal_presensi', $bulan)
            ->whereYear('tanggal_presensi', $tahun)
            ->orderBy("tanggal_presensi", "asc")
            ->get();
        return view('dashboard.presensi.search-history', compact('data'));
    }

    public function pengajuanPresensi()
    {
        $title = "Izin Karyawan";
        $riwayatPengajuanPresensi = DB::table("pengajuan_presensi")
            ->where('user_id', auth()->guard('karyawan')->user()->user_id)
            ->orderBy("tanggal_pengajuan", "asc")
            ->paginate(10);
        $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        return view('dashboard.presensi.izin.index', compact('title', 'riwayatPengajuanPresensi', 'bulan'));
    }

    public function pengajuanPresensiCreate()
    {
        $title = "Form Pengajuan Presensi";
        $statusPengajuan = StatusPengajuanPresensi::cases();
        return view('dashboard.presensi.izin.create', compact('title', 'statusPengajuan'));
    }

    public function pengajuanPresensiStore(Request $request)
    {
        $user_id = auth()->guard('karyawan')->user()->user_id;
        $tanggal_pengajuan = $request->tanggal_pengajuan;
        $status = $request->status;
        $keterangan = $request->keterangan;

        $cekPengajuan = DB::table('pengajuan_presensi')
            ->where('user_id', auth()->guard('karyawan')->user()->user_id)
            ->whereDate('tanggal_pengajuan', Carbon::make($tanggal_pengajuan)->format('Y-m-d'))
            ->where(function (Builder $query) {
                $query->where('status_approved', 0)
                    ->orWhere('status_approved', 1);
            })
            ->first();

        if ($cekPengajuan) {
            return to_route('karyawan.izin')->with("error", "Anda sudah menambahkan pengajuan pada tanggal " . Carbon::make($tanggal_pengajuan)->format('d-m-Y'));
        } else {
            $store = DB::table('pengajuan_presensi')->insert([
                'user_id' => $user_id,
                'tanggal_pengajuan' => $tanggal_pengajuan,
                'status' => $status,
                'keterangan' => $keterangan,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        if ($store) {
            return to_route('karyawan.izin')->with("success", "Berhasil menambahkan pengajuan");

        } else {
            return to_route('karyawan.izin')->with("error", "Gagal menambahkan pengajuan");
        }
    }

    public function searchPengajuanHistory(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $data = DB::table('pengajuan_presensi')
            ->where('user_id', auth()->guard('karyawan')->user()->user_id)
            ->whereMonth('tanggal_pengajuan', $bulan)
            ->whereYear('tanggal_pengajuan', $tahun)
            ->orderBy("tanggal_pengajuan", "asc")
            ->get();
        return view('dashboard.presensi.izin.search-history', compact('data'));
    }

    public function monitoringPresensi(Request $request)
    {
        $query = DB::table('presensi as p')
            ->join('karyawan as k', 'p.user_id', '=', 'k.user_id')
            ->join('departemen as d', 'k.departemen_id', '=', 'd.id')
            ->orderBy('k.nama_lengkap', 'asc')
            ->orderBy('d.kode', 'asc')
            ->select('p.*', 'k.nama_lengkap as nama_karyawan', 'd.nama as nama_departemen', 'k.email');

        if ($request->tanggal_presensi) {
            $query->whereDate('p.tanggal_presensi', $request->tanggal_presensi);
        } else {
            $query->whereDate('p.tanggal_presensi', Carbon::now());
        }

        $monitoring = $query->paginate(10);

        $lokasiKantor = LokasiKantor::where('is_used', true)->first();

        return view('admin.monitoring-presensi.index', compact('monitoring', 'lokasiKantor'));
    }

    public function viewLokasi(Request $request)
    {
        if ($request->tipe == "lokasi_masuk") {
            $lokasi = DB::table('presensi')
                ->where('user_id', $request->user_id)
                ->where('tanggal_presensi', 'LIKE', '%' . Carbon::now()->format('Y-m') . '%')
                ->pluck('lokasi_masuk');
        } else {
            $lokasi = DB::table('presensi')
                ->where('user_id', $request->user_id)
                ->where('tanggal_presensi', 'LIKE', '%' . Carbon::now()->format('Y-m') . '%')
                ->pluck('lokasi_keluar');
        }

        return response()->json($lokasi);
    }

    public function laporan(Request $request)
    {
        $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $karyawan = Karyawan::orderBy('nama_lengkap', 'asc')->get();
        return view('admin.laporan.presensi', compact('bulan', 'karyawan'));
    }

    public function laporanPresensiKaryawan(Request $request)
    {
        $title = 'Laporan Presensi Karyawan';
        $bulan = $request->bulan;
        $karyawan = Karyawan::query()
            ->with('departemen')
            ->where('user_id', $request->karyawan)
            ->first();
        $riwayatPresensi = DB::table("presensi")
            ->where('user_id', $request->karyawan)
            ->whereMonth('tanggal_presensi', Carbon::make($bulan)->format('m'))
            ->whereYear('tanggal_presensi', Carbon::make($bulan)->format('Y'))
            ->orderBy("tanggal_presensi", "asc")
            ->get();

        // return view('admin.laporan.pdf.presensi-karyawan', compact('title', 'bulan', 'karyawan', 'riwayatPresensi'));
        $pdf = Pdf::loadView('admin.laporan.pdf.presensi-karyawan', compact('title', 'bulan', 'karyawan', 'riwayatPresensi'));
        return $pdf->stream($title . ' ' . $karyawan->nama_lengkap . '.pdf');
    }

    public function laporanPresensiSemuaKaryawan(Request $request)
    {
        $title = 'Laporan Presensi Semua Karyawan';
        $bulan = $request->bulan;
        $riwayatPresensi = DB::table("presensi as p")
            ->join('karyawan as k', 'p.user_id', '=', 'k.user_id')
            ->join('departemen as d', 'k.departemen_id', '=', 'd.id')
            ->whereMonth('tanggal_presensi', Carbon::make($bulan)->format('m'))
            ->whereYear('tanggal_presensi', Carbon::make($bulan)->format('Y'))
            ->select(
                'p.user_id',
                'k.nama_lengkap as nama_karyawan',
                'k.jabatan as jabatan_karyawan',
                'd.nama as nama_departemen'
            )
            ->selectRaw("COUNT(p.user_id) as total_kehadiran, SUM(IF (jam_masuk > '08:00',1,0)) as total_terlambat")
            ->groupBy(
                'p.user_id',
                'k.nama_lengkap',
                'k.jabatan',
                'd.nama'
            )
            ->orderBy("tanggal_presensi", "asc")
            ->get();

        // return view('admin.laporan.pdf.presensi-semua-karyawan', compact('title', 'bulan', 'riwayatPresensi'));
        $pdf = Pdf::loadView('admin.laporan.pdf.presensi-semua-karyawan', compact('title', 'bulan', 'riwayatPresensi'));
        return $pdf->stream($title . '.pdf');
    }

    public function indexAdmin(Request $request)
    {
        $title = 'Administrasi Presensi';

        $departemen = Departemen::get();

        $query = DB::table('pengajuan_presensi as p')
            ->join('karyawan as k', 'k.user_id', '=', 'p.user_id')
            ->join('departemen as d', 'k.departemen_id', '=', 'd.id')
            ->where('p.tanggal_pengajuan', '>=', Carbon::now()->startOfMonth()->format("Y-m-d"))
            ->where('p.tanggal_pengajuan', '<=', Carbon::now()->endOfMonth()->format("Y-m-d"))
            ->select('p.*', 'k.nama_lengkap as nama_karyawan', 'd.nama as nama_departemen', 'd.id as id_departemen')
            ->orderBy('p.tanggal_pengajuan', 'asc');

        if ($request->user_id) {
            $query->where('k.user_id', 'LIKE', '%' . $request->user_id . '%');
        }
        if ($request->karyawan) {
            $query->where('k.nama_lengkap', 'LIKE', '%' . $request->karyawan . '%');
        }
        if ($request->departemen) {
            $query->where('d.id', $request->departemen);
        }
        if ($request->tanggal_awal) {
            $query->WhereDate('p.tanggal_pengajuan', '>=', Carbon::parse($request->tanggal_awal)->format('Y-m-d'));
        }
        if ($request->tanggal_akhir) {
            $query->WhereDate('p.tanggal_pengajuan', '<=', Carbon::parse($request->tanggal_akhir)->format('Y-m-d'));
        }
        if ($request->status) {
            $query->Where('p.status', $request->status);
        }
        if ($request->status_approved) {
            $query->Where('p.status_approved', $request->status_approved);
        }

        $pengajuan = $query->paginate(10);

        return view('admin.monitoring-presensi.administrasi-presensi', compact('title', 'pengajuan', 'departemen'));
    }

    public function persetujuanPresensi(Request $request)
    {
        if ($request->ajuan == "terima") {
            $pengajuan = DB::table('pengajuan_presensi')->where('id', $request->id)->update([
                'status_approved' => 2
            ]);
            if ($pengajuan) {
                return response()->json(['success' => true, 'message' => 'Pengajuan presensi telah diterima']);
            } else {
                return response()->json(['success' => false, 'message' => 'Pengajuan presensi gagal diterima']);
            }

        } elseif ($request->ajuan == "tolak") {
            $pengajuan = DB::table('pengajuan_presensi')->where('id', $request->id)->update([
                'status_approved' => 3
            ]);
            if ($pengajuan) {
                return response()->json(['success' => true, 'message' => 'Pengajuan presensi telah ditolak']);
            } else {
                return response()->json(['success' => false, 'message' => 'Pengajuan presensi gagal ditolak']);
            }

        } elseif ($request->ajuan == "batal") {
            $pengajuan = DB::table('pengajuan_presensi')->where('id', $request->id)->update([
                'status_approved' => 1
            ]);
            if ($pengajuan) {
                return response()->json(['success' => true, 'message' => 'Pengajuan presensi telah dibatalkan']);
            } else {
                return response()->json(['success' => false, 'message' => 'Pengajuan presensi gagal dibatalkan']);
            }
        }
    }
}