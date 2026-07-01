<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        @yield('title','POS Minimarket')
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <link
        href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css"
        rel="stylesheet"
    />

    <script defer
        src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js">
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>


<body class="bg-slate-100 text-slate-800">

<div class="flex h-screen">

    {{-- Sidebar --}}
    @include('layouts.partials.sidebar')

    {{-- Content --}}
    <div class="flex-1 flex flex-col bg-slate-100">

    @include('layouts.partials.topbar')

    <main class="flex-1 overflow-y-auto">

        <div class="p-8">

            <div class="rounded-2xl">

                @yield('content')

            </div>

        </div>

    </main>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>    


{{-- pesan2 sesuai status transaksi success,error dsb --}}
    @if(session('success'))

    <script>

    document.addEventListener('DOMContentLoaded', function () {

        Swal.fire({

            icon: 'success',

            title: 'Sukses',

            text: @json(session('success')),

            timer: 2200,

            showConfirmButton: false,

            confirmButtonColor: '#4F46E5'

        });

    });

    </script>

    @endif


    @if(session('error'))

    <script>

    document.addEventListener('DOMContentLoaded', function () {

        Swal.fire({

            icon: 'error',

            title: 'Terjadi Kesalahan',

            text: @json(session('error')),

            confirmButtonColor: '#DC2626'

        });

    });

    </script>

    @endif


    @if(session('warning'))

    <script>

    document.addEventListener('DOMContentLoaded', function () {

        Swal.fire({

            icon: 'warning',

            title: 'Perhatian',

            text: @json(session('warning')),

            confirmButtonColor: '#F59E0B'

        });

    });

    </script>

    @endif
{{--pesan2 end --}}

@stack('scripts')
</body>

</html>