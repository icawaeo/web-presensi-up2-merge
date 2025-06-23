<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __("Monitoring Presensi") }}
            </h2>
        </div>
    </x-slot>

    <div class="container mx-auto px-5 pt-5">
        <div>
            <form action="{{ route("admin.monitoring-presensi") }}" method="get" enctype="multipart/form-data" class="my-3">
                <div class="flex w-full flex-wrap gap-2 md:flex-nowrap">
                    <input type="date" name="tanggal_presensi" placeholder="Pencarian" class="input input-bordered w-full" value="{{ request()->tanggal_presensi ? request()->tanggal_presensi : Carbon\Carbon::now()->format("Y-m-d") }}" />
                    <button type="submit" class="btn btn-success w-full md:w-14">
                        <i class="ri-search-2-line text-lg text-white"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="w-full overflow-x-auto rounded-md bg-slate-200 px-10">
            <table id="tabelPresensi" class="table mb-4 w-full border-collapse items-center border-gray-200 align-top dark:border-white/40">
                <thead class="text-sm text-gray-800 dark:text-gray-300">
                    <tr>
                        <th></th>
                        <th>Nama Lengkap</th>
                        <th>Pekerjaan</th>
                        <th>Email</th>
                        <th>Jam Masuk</th>
                        <th>Foto & Lokasi</th>
                        <th>Jam Keluar</th>
                        <th>Foto & Lokasi</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($monitoring as $value => $item)
                        <tr class="hover">
                            <td class="font-bold">{{ $monitoring->firstItem() + $value }}</td>
                            <td class="text-slate-500 dark:text-slate-300">{{ $item->nama_karyawan }}</td>
                            <td class="text-slate-500 dark:text-slate-300">{{ $item->nama_departemen }}</td>
                            <td class="text-slate-500 dark:text-slate-300">{{ $item->email }}</td>
                            <td class="text-slate-500 dark:text-slate-300">{{ $item->jam_masuk }}</td>
                            <td class="text-slate-500 dark:text-slate-300">
                                <div class="avatar">
                                    <div class="w-24 rounded">
                                        {{-- REVISI: Kirim koordinat yang benar langsung ke fungsi JS --}}
                                        @if ($item->foto_masuk && $item->lokasi_masuk)
                                            <label for="view_modal" class="cursor-pointer" onclick="return showMapModal('{{ $item->lokasi_masuk }}')">
                                                <img src="{{ asset("storage/unggah/presensi/$item->foto_masuk") }}" alt="{{ $item->foto_masuk }}" />
                                            </label>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-slate-500 dark:text-slate-300">
                                @if ($item->jam_keluar)
                                    {{ $item->jam_keluar }}
                                @else
                                    <div class="w-fit rounded-md bg-error p-1 text-white">Belum Presensi</div>
                                @endif
                            </td>
                            <td class="text-slate-500 dark:text-slate-300">
                                <div class="avatar">
                                    <div class="w-24 rounded">
                                        {{-- REVISI: Kirim koordinat yang benar langsung ke fungsi JS --}}
                                        @if ($item->foto_keluar && $item->lokasi_keluar)
                                            <label for="view_modal" class="cursor-pointer" onclick="return showMapModal('{{ $item->lokasi_keluar }}')">
                                                <img src="{{ asset("storage/unggah/presensi/$item->foto_keluar") }}" alt="{{ $item->foto_keluar }}" />
                                            </label>
                                        @else
                                            <span></span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-slate-500 dark:text-slate-300">
                                @if ($item->jam_masuk > Carbon\Carbon::make("08:00:00")->format("H:i:s"))
                                    @php
                                        $masuk = Carbon\Carbon::make($item->jam_masuk);
                                        $batas = Carbon\Carbon::make("08:00:00");
                                        $diff = $masuk->diff($batas);
                                        if ($diff->format("%h") != 0) {
                                            $selisih = $diff->format("%h jam %I menit");
                                        } else {
                                            $selisih = $diff->format("%I menit");
                                        }
                                    @endphp
                                    <div class="w-fit rounded-md bg-error p-1 text-white">Terlambat {{ $selisih }}</div>
                                @else
                                    <div class="w-fit rounded-md bg-success p-1 text-white">Tepat Waktu</div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mx-3 mb-5">
                {{ $monitoring->links() }}
            </div>
        </div>
    </div>

    {{-- Awal Modal View Lokasi --}}
    <input type="checkbox" id="view_modal" class="modal-toggle" />
    <div class="modal" role="dialog">
        <div class="modal-box">
            <div class="mb-3 flex justify-between">
                <h3 class="judul-lokasi text-lg font-bold">Lokasi Presensi</h3>
                <label for="view_modal" class="cursor-pointer">
                    <i class="ri-close-large-fill"></i>
                </label>
            </div>
            <div>
                <label class="form-control w-full">
                    <div class="label">
                        <span class="label-text font-semibold">
                            <span class="label-text font-semibold">Koordinat</span>
                        </span>
                    </div>
                    {{-- Input ini akan diisi oleh JavaScript --}}
                    <input type="text" name="lokasi_modal" placeholder="Lokasi" class="input input-bordered w-full text-blue-700" readonly />
                    <div id="lokasi-map" class="mx-auto mt-3 h-80 w-full rounded-md"></div>
                </label>
            </div>
        </div>
    </div>
    {{-- Akhir Modal View Lokasi --}}

    {{-- REVISI KESELURUHAN BAGIAN SCRIPT --}}
    <script>
        @if (session()->has("success"))
            Swal.fire({
                title: 'Berhasil',
                text: '{{ session("success") }}',
                icon: 'success',
                confirmButtonColor: '#6419E6',
                confirmButtonText: 'OK',
            });
        @endif

        @if (session()->has("error"))
            Swal.fire({
                title: 'Gagal',
                text: '{{ session("error") }}',
                icon: 'error',
                confirmButtonColor: '#6419E6',
                confirmButtonText: 'OK',
            });
        @endif
        
        // Variabel global untuk menyimpan instance peta
        let mapInstance = null;
        let markerInstance = null;

        /**
         * Fungsi baru untuk menampilkan modal dan peta.
         * Menggantikan fungsi lama `viewLokasi` dan `maps`.
         * @param {string} lokasi - Koordinat dalam format "latitude,longitude".
         */
        function showMapModal(lokasi) {
            // Jika lokasi kosong atau tidak valid, jangan lakukan apa-apa
            if (!lokasi) {
                console.error("Koordinat lokasi tidak valid.");
                return;
            }

            // Memasukkan nilai koordinat ke dalam input field di modal
            document.querySelector("input[name='lokasi_modal']").value = lokasi;
            
            const [latitude, longitude] = lokasi.split(",");

            // Hancurkan map lama jika ada, untuk menghindari error "Map container already initialized"
            if (mapInstance) {
                mapInstance.remove();
                mapInstance = null;
            }

            // Buat instance map baru
            mapInstance = L.map('lokasi-map').setView([latitude, longitude], 18);
            
            // Tambahkan tile layer OpenStreetMap
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(mapInstance);

            // Tambahkan marker di lokasi presensi
            markerInstance = L.marker([latitude, longitude]).addTo(mapInstance);
            markerInstance.bindPopup("<b>Lokasi Presensi</b>").openPopup();
            
            // Tambahkan lingkaran radius kantor untuk referensi
            @if($lokasiKantor)
                L.circle([{{ $lokasiKantor->latitude }}, {{ $lokasiKantor->longitude }}], {
                    color: 'red',
                    fillColor: '#f03',
                    fillOpacity: 0.5,
                    radius: {{ $lokasiKantor->radius }}
                }).addTo(mapInstance).bindPopup("<b>Lokasi Kantor</b>");
            @endif
        }
    </script>
</x-app-layout>