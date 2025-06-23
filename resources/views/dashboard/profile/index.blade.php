@extends("dashboard.layouts.main")

@section("js")
    <script>
        let notifikasi_berhasil = document.getElementById('notifikasi_berhasil');
        setInterval(() => {
            notifikasi_berhasil.style.display = 'none';
        }, 3000);

        function previewImage() {
            const image = document.querySelector('#foto');
            const imgPreview = document.querySelector('.img-preview');

            imgPreview.style.display = 'block';

            const oFReader = new FileReader();
            oFReader.readAsDataURL(image.files[0]);

            oFReader.onload = function(oFREvent) {
                imgPreview.src = oFREvent.target.result;
            }
        }
    </script>
@endsection

@section("container")
    <div>
        <div class="relative mx-auto mt-36 w-full">
            <div class="dark:bg-slate-850 dark:shadow-dark-xl shadow-3xl relative mx-6 flex min-w-0 flex-auto flex-col overflow-hidden break-words rounded-2xl border-0 bg-white bg-clip-border p-4">
                <div class="-mx-3 flex flex-wrap">
                    <div class="w-auto max-w-full flex-none px-3">
                        <div class="h-19 w-19 relative flex items-center justify-center rounded-xl text-5xl text-slate-700 transition-all duration-200 ease-in-out dark:text-white">
                            @if ($karyawan->foto)
                                <div class="avatar">
                                    <div class="w-20 rounded-full">
                                        <img src="{{ asset("storage/unggah/karyawan/$karyawan->foto") }}" />
                                    </div>
                                </div>
                            @else
                                <i class="ri-user-fill"></i>
                            @endif
                        </div>
                    </div>
                    <div class="my-auto w-auto max-w-full flex-none px-3">
                        <div class="h-full">
                            <h5 class="mb-1 dark:text-white">{{ $karyawan->nama_lengkap }}</h5>
                            @if ($karyawan->departemen)
                                <p class="mb-0 text-sm font-semibold leading-normal dark:text-white dark:opacity-60">{{ $karyawan->departemen->nama_departemen }}</p>
                            @else
                                <p class="mb-0 text-sm font-semibold leading-normal dark:text-white dark:opacity-60 text-red-500">Departemen belum diatur</p>
                            @endif
                        </div>
                    </div>
                    @if (session()->get("success"))
                        <div id="notifikasi_berhasil" class="mx-auto mt-4 w-full max-w-full px-3 sm:my-auto sm:mr-0 md:w-1/2 md:flex-none lg:w-4/12">
                            <div class="relative right-0">
                                <ul class="relative flex list-none flex-wrap rounded-xl bg-success p-1">
                                    <li class="z-30 flex-auto text-center">
                                        <a class="z-30 mb-0 flex w-full items-center justify-center rounded-lg border-0 bg-inherit px-0 py-1 text-white transition-all ease-in-out">
                                            <i class="ni ni-app"></i>
                                            <span class="ml-2">{{ session("success") }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endif
                    @if (session()->get("error"))
                        <div id="notifikasi_berhasil" class="mx-auto mt-4 w-full max-w-full px-3 sm:my-auto sm:mr-0 md:w-1/2 md:flex-none lg:w-4/12">
                            <div class="relative right-0">
                                <ul class="relative flex list-none flex-wrap rounded-xl bg-error p-1">
                                    <li class="z-30 flex-auto text-center">
                                        <a class="z-30 mb-0 flex w-full items-center justify-center rounded-lg border-0 bg-inherit px-0 py-1 text-white transition-all ease-in-out">
                                            <i class="ni ni-app"></i>
                                            <span class="ml-2">{{ session("error") }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <form action="{{ route("karyawan.profile.update") }}" method="post" enctype="multipart/form-data" class="-mx-3 flex flex-wrap p-6">
            @csrf
            <div class="md:flex-0 w-full max-w-full shrink-0 px-3 md:w-8/12">
                <div class="dark:bg-slate-850 dark:shadow-dark-xl relative flex min-w-0 flex-col break-words rounded-2xl border-0 bg-white bg-clip-border shadow-xl">
                    <div class="border-black/12.5 rounded-t-2xl border-b-0 border-solid p-6 pb-0">
                        <div class="flex items-center">
                            <p class="mb-0 dark:text-white/80">Edit Profil Akun</p>
                            <button type="submit" onclick="return confirm('Are you sure?')" class="tracking-tight-rem hover:shadow-xs active:opacity-85 mb-4 ml-auto inline-block cursor-pointer rounded-lg border-0 bg-yellow-400 px-8 py-2 text-center align-middle text-xs font-bold leading-normal text-white shadow-md transition-all ease-in hover:-translate-y-px">Update</button>
                        </div>
                    </div>
                    <div class="flex-auto p-6">
                        <p class="text-sm uppercase leading-normal dark:text-white dark:opacity-60">Identitas Anda</p>
                        <div class="-mx-3 flex flex-wrap">
                            {{-- <div class="md:flex-0 w-full max-w-full shrink-0 px-3 md:w-6/12">
                                <div class="mb-4">
                                    <label for="user_id" class="mb-2 ml-1 inline-block text-xs font-bold text-slate-700 dark:text-white/80">NIK</label>
                                    <input type="text" name="nik" value="{{ $karyawan->nik }}" class="focus:shadow-primary-outline dark:bg-slate-850 leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 text-sm font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none dark:text-white" readonly />
                                </div>
                            </div> --}}
                            <div class="md:flex-0 w-full max-w-full shrink-0 px-3 md:w-6/12">
                                <div class="mb-4">
                                    <label for="email" class="mb-2 ml-1 inline-block text-xs font-bold text-slate-700 dark:text-white/80">Email</label>
                                    <input 
                                        type="email" 
                                        name="email" 
                                        value="{{ $karyawan->email }}" 
                                        class="focus:shadow-none bg-gray-100 dark:bg-slate-700 cursor-not-allowed leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-clip-padding px-3 py-2 text-sm font-normal text-gray-400 outline-none transition-all placeholder:text-gray-400 focus:border-gray-300 focus:outline-none dark:text-gray-400" 
                                        readonly 
                                        tabindex="-1"
                                        style="pointer-events: none;"
                                    />
                                </div>
                            </div>
                            <div class="md:flex-0 w-full max-w-full shrink-0 px-3 md:w-6/12">
                                <div class="mb-4">
                                    <label for="nama_lengkap" class="mb-2 ml-1 inline-block text-xs font-bold text-slate-700 dark:text-white/80">Nama Lengkap</label>
                                    <input type="text" name="nama_lengkap" value="{{ $karyawan->nama_lengkap }}" class="focus:shadow-primary-outline dark:bg-slate-850 leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 text-sm font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none dark:text-white" />
                                </div>
                            </div>
                            <div class="md:flex-0 w-full max-w-full shrink-0 px-3 md:w-6/12">
                                <div class="mb-4">
                                    <label for="telepon" class="mb-2 ml-1 inline-block text-xs font-bold text-slate-700 dark:text-white/80">Telepon</label>
                                    <input type="text" name="telepon" value="{{ $karyawan->telepon }}" class="focus:shadow-primary-outline dark:bg-slate-850 leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 text-sm font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none dark:text-white" />
                                </div>
                            </div>
                            <div class="md:flex-0 w-full max-w-full shrink-0 px-3 md:w-6/12">
                                <div class="mb-4">
                                    <label for="password" class="mb-2 ml-1 inline-block text-xs font-bold text-slate-700 dark:text-white/80">Password</label>
                                    <div class="relative">
                                        <input 
                                            type="password" 
                                            name="password" 
                                            id="password" 
                                            class="focus:shadow-primary-outline dark:bg-slate-850 leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 text-sm font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none dark:text-white pr-10" 
                                        />
                                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 focus:outline-none" tabindex="-1">
                                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <script>
                                    function togglePassword() {
                                        const passwordInput = document.getElementById('password');
                                        const eyeIcon = document.getElementById('eyeIcon');
                                        if (passwordInput.type === 'password') {
                                            passwordInput.type = 'text';
                                            eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.956 9.956 0 012.293-3.95m1.414-1.414A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.965 9.965 0 01-4.293 5.95M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />`;
                                        } else {
                                            passwordInput.type = 'password';
                                            eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
                                        }
                                    }
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="md:flex-0 mt-6 w-full max-w-full shrink-0 px-3 md:mt-0 mb-1 md:w-4/12">
                @if ($karyawan->foto)
                    <img src="{{ asset("storage/unggah/karyawan/$karyawan->foto") }}" class="img-preview mb-3 rounded" />
                @else
                    <img src="{{ asset("img/carousel-3.jpg") }}" class="img-preview mb-3 rounded" />
                @endif
                <input type="file" name="foto" id="foto" class="file-input file-input-bordered w-full" onchange="previewImage()" />
            </div>
        </form>
    </div>
@endsection
