
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __("Laporan Presensi") }}
            </h2>
        </div>
    </x-slot>

    <div class="container mx-auto px-5 pt-5">
        <div class="w-full rounded-md bg-slate-200 px-10 mb-10">
            <h1 class="py-3 font-bold text-xl">Laporan Presensi Tenaga Ahli Daya</h1>
            <form action="{{ route("admin.laporan.presensi.karyawan") }}" method="post" target="_blank" enctype="multipart/form-data" class="pb-3">
                @csrf
                <div class="flex w-full flex-wrap gap-2 lg:flex-nowrap">
                    <input type="month" name="bulan" class="input input-bordered w-full" value="{{ Carbon\Carbon::now()->format("Y-m") }}" required />
                    <select id="selectKaryawan" name="karyawan" class="select select-bordered w-full text-blue-700" required>
                        <option value="" disabled selected>Pilih Tenaga Ahli Daya</option>
                        @foreach ($karyawan as $item)
                            <option value="{{ $item->user_id }}">{{ $item->nama_lengkap }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-error w-full lg:w-14">
                        <i class="ri-file-pdf-2-fill text-lg text-white"></i>
                    </button>
                    <button type="submit" formaction="{{ route('admin.laporan.presensi.karyawan.excel') }}" class="btn btn-success w-full lg:w-14 tooltip" data-tip="Export Excel">
                        <i class="ri-file-excel-2-line text-lg text-white"></i>
                    </button>
                </div>
            </form>
        </div>

        <div class="w-full rounded-md bg-slate-200 px-10">
            <h1 class="py-3 font-bold text-xl">Ringkasan Presensi Bulanan Tenaga Ahli Daya</h1>
            <form action="{{ route("admin.laporan.presensi.semua-karyawan") }}" method="post" target="_blank" enctype="multipart/form-data" class="pb-3">
                @csrf
                <div class="flex w-full flex-wrap gap-2 lg:flex-nowrap">
                    <input type="month" name="bulan" class="input input-bordered w-full" value="{{ Carbon\Carbon::now()->format("Y-m") }}" required />
                    <button type="submit" class="btn btn-error w-full lg:w-14">
                        <i class="ri-file-pdf-2-fill text-lg text-white"></i>
                    </button>
                    <button type="submit" formaction="{{ route('admin.laporan.presensi.semua-karyawan.excel') }}" class="btn btn-success w-full lg:w-14 tooltip" data-tip="Export Excel">
                        <i class="ri-file-excel-2-line text-lg text-white"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectKaryawan = document.getElementById('selectKaryawan');

            if (selectKaryawan) {
                selectKaryawan.addEventListener('invalid', function(event){
                    event.preventDefault();
                    alert('Silakan pilih nama Tenaga Ahli Daya terlebih dahulu.');
                });
            }
        });
    </script>
    @endpush

</x-app-layout>
