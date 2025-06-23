@extends("dashboard.layouts.main")

@section("css")
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        @media (max-width: 425px) {

            #webcam-capture,
            #webcam-capture video {
                width: 300px !important;
                height: 380px !important;
                margin auto;
                border-radius: 33px;
            }
        }

        @media (min-width: 640px) {

            #webcam-capture,
            #webcam-capture video {
                width: 480px !important;
                height: 640px !important;
                margin auto;
                border-radius: 33px;
            }
        }

        @media (min-width: 768px) {

            #webcam-capture,
            #webcam-capture video {
                width: 640px !important;
                height: 480px !important;
                margin auto;
                border-radius: 33px;
            }
        }
    </style>
@endsection

@section("js")
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js" integrity="sha512-dQIiHSl2hr3NWKKLycPndtpbh5iaHLo6MwrXm7F0FM5e+kL2U16oE9uIwPHUl6fQBeCthiEuV/rzP3MiAB8Vfw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            Webcam.set({
                width: 640,
                height: 480,
                image_format: 'jpeg',
                jpeg_quality: 90,
                force_flash: false,
                flip_horiz: false,
            });
            Webcam.attach('#webcam-capture');

            let lokasi = document.getElementById('lokasi');
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
            } else {
                $("#lokasi-status").html(`<span class="text-danger">Geolocation tidak didukung oleh browser Anda.</span>`);
            }

            function successCallback(position) {
                let latitude = position.coords.latitude;
                let longitude = position.coords.longitude;
                lokasi.value = latitude + ", " + longitude;

                $(".btn-presensi").prop('disabled', false);
                $("#lokasi-status").html(`<strong>Lokasi berhasil didapatkan.</strong> Silakan lakukan presensi.`);

                let map = L.map('map').setView([latitude, longitude], 17);
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(map);

                let marker = L.marker([latitude, longitude]).addTo(map);
                marker.bindPopup("<b>Anda berada di sini</b>").openPopup();

                let circle = L.circle([{{ $lokasiKantor->latitude }}, {{ $lokasiKantor->longitude }}], {
                    color: 'red',
                    fillColor: '#f03',
                    fillOpacity: 0.5,
                    radius: {{ $lokasiKantor->radius }}
                }).addTo(map);
            }

            function errorCallback(error) {
                let message = "";
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        message = "Anda menolak permintaan untuk Geolokasi. Silakan izinkan akses lokasi pada browser Anda.";
                        break;
                    case error.POSITION_UNAVAILABLE:
                        message = "Informasi lokasi tidak tersedia.";
                        break;
                    case error.TIMEOUT:
                        message = "Permintaan untuk mendapatkan lokasi pengguna timeout.";
                        break;
                    case error.UNKNOWN_ERROR:
                        message = "Terjadi kesalahan yang tidak diketahui.";
                        break;
                }
                $("#lokasi-status").html(`<span class="text-danger">${message}</span>`);
            }

            let notifikasi_presensi_masuk = document.getElementById('notifikasi_presensi_masuk');
            let notifikasi_presensi_keluar = document.getElementById('notifikasi_presensi_keluar');
            
            $(".btn-presensi").click(function() {
                Webcam.snap(function(uri) {
                    image = uri;
                });
                $.ajax({
                    type: "POST",
                    url: "{{ route("karyawan.presensi.store") }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        image: image,
                        lokasi: lokasi.value,
                        jenis: $("input[name='presensi']").val(),
                    },
                    cache: false,
                    success: function(res) {
                        if (res.status == 200) {
                            let notifTitle = "Berhasil";
                            let notifMessage = "Presensi Anda telah berhasil direkam.";
                            
                            if (res.jenis_presensi == "masuk") {
                                notifikasi_presensi_masuk.play();
                                if (res.status_radius == "out") {
                                    notifMessage = "Presensi berhasil, namun Anda terdeteksi berada di luar radius kantor.";
                                }
                            } else if (res.jenis_presensi == "keluar") {
                                notifikasi_presensi_keluar.play();
                            }

                            Swal.fire({
                                title: notifTitle,
                                text: notifMessage,
                                icon: "success",
                                confirmButtonText: "OK"
                            });
                            setTimeout("location.href='{{ route("karyawan.dashboard") }}'", 5000);

                        } else if (res.status == 500) {
                            Swal.fire({
                                title: "Presensi Gagal",
                                text: res.message,
                                icon: "error",
                                confirmButtonText: "OK"
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection

@section("container")
    <div>
        <audio id="notifikasi_presensi_masuk">
            <source src="{{ asset("audio/notifikasi_presensi_masuk.mp3") }}" type="audio/mpeg">
        </audio>
        <audio id="notifikasi_presensi_keluar">
            <source src="{{ asset("audio/notifikasi_presensi_keluar.mp3") }}" type="audio/mpeg">
        </audio>
        <audio id="notifikasi_presensi_gagal_radius">
            <source src="{{ asset("audio/notifikasi_presensi_gagal_radius.mp3") }}" type="audio/mpeg">
        </audio>
        <div class="-mx-3 flex flex-wrap">
            <div class="mb-6 w-full max-w-full px-3 sm:flex-none">
                <div class="dark:bg-slate-850 dark:shadow-dark-xl relative flex min-w-0 flex-col break-words rounded-2xl bg-white bg-clip-border shadow-xl">
                    <div class="flex-auto p-4">
                        <input type="text" name="lokasi" id="lokasi" class="input input-primary" hidden>
                        <div id="webcam-capture" class="mx-auto"></div>
                        <div class="flex justify-center mt-4">
                            @if ($presensiKaryawan == null)
                                <input type="text" name="presensi" id="presensi" value="masuk" hidden>
                                <button class="btn-presensi btn btn-primary btn-wide text-white" disabled>
                                    <i class="ri-camera-line text-lg"></i>
                                    Presensi Masuk
                                </button>
                            @elseif ($presensiKaryawan->jam_keluar != null)
                                <button class="btn btn-disabled btn-ghost btn-wide text-white dark:bg-slate-600/60 dark:text-white">
                                    <i class="ri-camera-line text-lg"></i>
                                    Presensi Selesai
                                </button>
                            @elseif ($presensiKaryawan != null)
                                <input type="text" name="presensi" id="presensi" value="keluar" hidden>
                                <button class="btn-presensi btn btn-secondary btn-wide text-white" disabled>
                                    <i class="ri-camera-line text-lg"></i>
                                    Presensi Keluar
                                </button>
                            @endif
                        </div>
                        <div id="lokasi-status" class="text-center mt-3" style="font-style: italic;">
                            Mendapatkan lokasi...
                        </div>
                    </div>
                </div>
                <div class="dark:bg-slate-850 dark:shadow-dark-xl relative mt-3 flex min-w-0 flex-col break-words rounded-2xl bg-white bg-clip-border shadow-xl">
                    <div class="flex-auto p-4">
                        <div id="map" class="mx-auto h-80 w-full"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection