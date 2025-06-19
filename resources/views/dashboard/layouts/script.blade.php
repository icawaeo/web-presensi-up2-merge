{{-- JQuery (wajib dimuat pertama) --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

{{-- Core Plugins for Argon Theme --}}
{{-- PENTING: Muat library PerfectScrollbar SEBELUM script konfigurasinya --}}
<script src="{{ asset("js/plugins/perfect-scrollbar.min.js") }}"></script>

{{-- Argon Theme Scripts (tanpa loader utama) --}}
<script src="{{ asset("js/dropdown.js") }}"></script>
<script src="{{ asset("js/fixed-plugin.js") }}"></script>
<script src="{{ asset("js/navbar-collapse.js") }}"></script>
<script src="{{ asset("js/navbar-sticky.js") }}"></script>
<script src="{{ asset("js/sidenav-burger.js") }}"></script>
<script src="{{ asset("js/tooltips.js") }}"></script>
{{-- Script ini mengkonfigurasi PerfectScrollbar, jadi harus setelah library-nya dimuat --}}
<script src="{{ asset("js/perfect-scrollbar.js") }}"></script>

{{-- ChartJS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

{{-- DataTables --}}
<script src="https://cdn.datatables.net/2.0.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>

{{-- Alphine Init --}}
<script src="{{ asset('js/init-alpine.js') }}"></script>

{{-- Sweetalert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>