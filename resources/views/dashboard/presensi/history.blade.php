@extends("dashboard.layouts.main")

@section("js")
    <script>
        $(document).ready(function() {
            $("#searchButton").click(function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "{{ route("karyawan.history.search") }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        bulan: $("#bulan").val(),
                        tahun: $("#tahun").val()
                    },
                    cache: false,
                    success: function(res) {
                        // $("#tabelPresensi").remove();
                        $("#searchPresensi").html(res);
                    }
                });
            });
        });
    </script>
@endsection

@section("container")
    <div>
        <div class="-mx-3 mt-6 flex flex-wrap">
            <div class="mb-6 mt-0 w-full max-w-full px-3">
                <div class="dark:bg-slate-850 dark:shadow-dark-xl border-black-125 relative flex min-w-0 flex-col break-words rounded-2xl border-0 border-solid bg-white bg-clip-border shadow-xl">
                    <div class="rounded-t-4 mb-0 p-4 pb-0">
                        <div class="flex justify-between">
                            <h6 class="mb-2 font-bold dark:text-white">Riwayat Presensi</h6>
                        </div>
                    </div>

                    <div class="mt-3 flex w-full flex-col gap-y-5">
                        {{-- Input Filter --}}
                        <div class="flex flex-wrap">
                            <div class="md:flex-0 w-full max-w-full shrink-0 px-3 md:w-1/2">
                                <div class="mb-4">
                                    <label for="bulan" class="mb-2 ml-1 inline-block text-xs font-bold text-slate-700 dark:text-white/80">Bulan</label>
                                    <select name="bulan" id="bulan" class="focus:shadow-primary-outline dark:bg-slate-850 leading-5.6 ease select select-bordered block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 text-sm font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none dark:text-white" required>
                                        <option disabled selected>Pilih Bulan</option>
                                        @foreach ($bulan as $value => $item)
                                            <option value="{{ $value + 1 }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="md:flex-0 w-full max-w-full shrink-0 px-3 md:w-1/2">
                                <div class="mb-4">
                                    <label for="tahun" class="mb-2 ml-1 inline-block text-xs font-bold text-slate-700 dark:text-white/80">Tahun</label>
                                    <select name="tahun" id="tahun" class="focus:shadow-primary-outline dark:bg-slate-850 leading-5.6 ease select select-bordered block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 text-sm font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none dark:text-white" required>
                                        <option disabled selected>Pilih Tahun</option>
                                        @php
                                            $tahunMulai = $riwayatPresensi[0] ? date("Y", strtotime($riwayatPresensi[0]->tanggal_presensi)) : date("Y");
                                        @endphp
                                        @for ($tahun = $tahunMulai; $tahun <= date("Y"); $tahun++)
                                            <option value="{{ $tahun }}">{{ $tahun }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-5 flex w-full max-w-full justify-center px-3">
                            <button type="button" id="searchButton" class="btn btn-warning btn-block">Search</button>
                        </div>

                        {{-- Tabel Riwayat Presensi --}}
                        <div id="searchPresensi" class="w-full overflow-x-auto px-10">
                            <table id="tabelPresensi" class="table mb-4 w-full border-collapse items-center border-gray-200 align-top dark:border-white/40">
                                <thead class="text-sm text-gray-800 dark:text-gray-300">
                                    <tr>
                                        <th></th>
                                        <th>Hari</th>
                                        <th>Tanggal</th>
                                        <th>Jam Masuk</th>
                                        <th>Jam Keluar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($riwayatPresensi as $value => $item)
                                        <tr class="hover">
                                            <td class="font-bold">{{ $riwayatPresensi->firstItem() + $value }}</td>
                                            <td class="text-slate-500 dark:text-slate-300">{{ date("l", strtotime($item->tanggal_presensi)) }}</td>
                                            <td class="text-slate-500 dark:text-slate-300">{{ date("d-m-Y", strtotime($item->tanggal_presensi)) }}</td>
                                            <td class="{{ $item->jam_masuk < "08:00" ? "text-success" : "text-error" }}">{{ date("H:i:s", strtotime($item->jam_masuk)) }}</td>
                                            @if ($item != null && $item->jam_keluar != null)
                                                <td class="{{ $item->jam_keluar > "16:00" ? "text-success" : "text-error" }}">{{ date("H:i:s", strtotime($item->jam_keluar)) }}</td>
                                            @else
                                                <td>Belum Presensi</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="mx-3 mb-5">
                                {{ $riwayatPresensi->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
