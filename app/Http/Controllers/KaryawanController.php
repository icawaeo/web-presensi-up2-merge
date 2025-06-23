<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class KaryawanController extends Controller
{
    public function index()
    {
        $title = "Profile";
        $karyawan = Karyawan::where('user_id', auth()->guard('karyawan')->user()->user_id)->first();
        return view('dashboard.profile.index', compact('title', 'karyawan'));
    }

    public function update(Request $request)
    {
        $karyawan = Karyawan::where('user_id', auth()->guard('karyawan')->user()->user_id)->first();

        if ($request->hasFile('foto')) {
            $foto = $karyawan->user_id . "." . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = $karyawan->foto;
        }

        if ($request->password != null) {
            $update = Karyawan::where('user_id', auth()->guard('karyawan')->user()->user_id)->update([
                'nama_lengkap' => $request->nama_lengkap,
                'telepon' => $request->telepon,
                'password' => Hash::make($request->password),
                'foto' => $foto,
                'updated_at' => Carbon::now(),
            ]);

        } elseif ($request->password == null) {
            $update = Karyawan::where('user_id', auth()->guard('karyawan')->user()->user_id)->update([
                'nama_lengkap' => $request->nama_lengkap,
                'telepon' => $request->telepon,
                'foto' => $foto,
                'updated_at' => Carbon::now(),
            ]);
        }

        if ($update) {
            if ($request->hasFile('foto')) {
                $folderPath = "public/unggah/karyawan/";
                $request->file('foto')->storeAs($folderPath, $foto);
            }
            return redirect()->back()->with('success', 'Profile updated successfully');
        } else {
            return redirect()->back()->with('error', 'Profile updated failed');
        }
    }

    public function indexAdmin(Request $request)
    {
        $title = "Data Tenaga Ahli Daya";

        $departemen = Departemen::get();

        $query = Karyawan::join('departemen as d', 'karyawan.departemen_id', '=', 'd.id')->select('karyawan.*', 'd.kode')->orderBy('d.kode', 'asc')->orderBy('karyawan.nama_lengkap', 'asc');
        if ($request->nama_karyawan) {
            $query->where('karyawan.nama_lengkap', 'like', '%'.$request->nama_karyawan.'%');
        }
        if ($request->kode_departemen) {
            $query->where('d.kode', 'like', '%'.$request->kode_departemen.'%');
        }
        $karyawan = $query->paginate(10);

        return view('admin.karyawan.index', compact('title', 'karyawan', 'departemen'));
    }

    public function store(Request $request)
    {
        do {
            $user_id = date('ymd') . mt_rand(100, 999);
        } while (Karyawan::where('user_id', $user_id)->exists());

        $request->merge([
            'user_id' => $user_id,
        ]);

        $data = $request->validate([
            'user_id' => 'required|unique:karyawan,user_id',
            'departemen_id' => 'required',
            'nama_lengkap' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            // 'jabatan' => 'required|string|max:255',
            'telepon' => 'required|string|max:15',
            'email' => 'required|string|email|max:255|unique:karyawan,email',
            'password' => 'required',
        ]);
        
        $data['password'] = Hash::make($data['password']);
        if ($request->hasFile('foto')) {
            $foto = $request->user_id . "." . $request->file('foto')->getClientOriginalExtension();
            $data['foto'] = $foto;
        }

        $create = Karyawan::create($data);

        if ($create) {
            if ($request->hasFile('foto')) {
                $folderPath = "public/unggah/karyawan/";
                $request->file('foto')->storeAs($folderPath, $foto);
            }
            return redirect()->route('admin.karyawan')->with('success', 'Data Karyawan berhasil disimpan');
        }

        return redirect()->route('admin.karyawan')->with('error', 'Data Karyawan gagal disimpan');

        // } else {
        //     return to_route('admin.karyawan')->with('error', 'Data Karyawan gagal disimpan');
        // }
    }

    public function edit(Request $request)
    {
        $data = Karyawan::where('user_id', $request->user_id)->first();
        return $data;
    }

    public function updateAdmin(Request $request)
    {
        // dd('Method updateAdmin terpanggil');
        // dd($request->all()); 
        $karyawan = Karyawan::where('user_id', $request->user_id_lama)->firstOrFail();
        $data = $request->validate([
            // 'user_id' => ['required', Rule::unique('karyawan')->ignore($karyawan)],
            'departemen_id' => 'required',
            'nama_lengkap' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            // 'pekerjaan' => 'required|string|max:255',
            'telepon' => 'required|string|max:15',
            'email' => ['required', 'email', Rule::unique('karyawan')->ignore($karyawan->user_id, 'user_id')],
        ]);
        
        if ($request->hasFile('foto')) {
            if ($karyawan->foto) {
                Storage::delete("public/unggah/karyawan/" . $karyawan->foto);
            }
            
            $foto_nama = $karyawan->user_id . "." . $request->file('foto')->getClientOriginalExtension();
            $folderPath = "public/unggah/karyawan/";
            $request->file('foto')->storeAs($folderPath, $foto_nama);
            
            $data['foto'] = $foto_nama;
        }

        $update = $karyawan->update($data);

        if ($update) {
            if ($request->hasFile('foto')) {
                $folderPath = "public/unggah/karyawan/";
                $request->file('foto')->storeAs($folderPath, $data['foto']);
            }
            return to_route('admin.karyawan')->with('success', 'Data User berhasil diperbarui');
        } else {
            return to_route('admin.karyawan')->with('error', 'Data User gagal diperbarui');
        }

    }

    public function delete(Request $request)
    {
        DB::beginTransaction();
        try {
            $karyawan = Karyawan::where('user_id', $request->user_id)->first();

            if (!$karyawan) {
                return response()->json(['success' => false, 'message' => 'Data User tidak ditemukan.']);
            }

            DB::table('presensi')->where('user_id', $request->user_id)->delete();

            DB::table('pengajuan_presensi')->where('user_id', $request->user_id)->delete();
            
            if ($karyawan->foto) {
                $folderPath = "public/unggah/karyawan/";
                Storage::delete($folderPath . $karyawan->foto);
            }

            $delete = $karyawan->delete();

            DB::commit(); 

            if ($delete) {
                return response()->json(['success' => true, 'message' => 'Data User dan semua data terkait berhasil dihapus.']);
            } else {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Data User Gagal dihapus.']);
            }
        } catch (\Exception $e) {
            DB::rollBack(); 
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
