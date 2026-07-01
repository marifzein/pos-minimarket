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

<body class="bg-slate-100">

    {{-- HEADER --}}
    <div class="bg-white shadow border-b">

        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">

            <h1 class="text-2xl font-bold">

                POS Minimarket

            </h1>

            @auth

            <div
                class="flex items-center gap-4"
            >

                <span
                    class="text-gray-700"
                >

                    {{ auth()->user()->name }}

                </span>

                <form
                    method="POST"
                    action="{{ route('logout') }}"
                >

                    @csrf

                    <button
                        class="bg-red-600 hover:bg-red-600 text-white px-3 py-1 rounded"
                    >

                        Logout

                    </button>

                </form>

            </div>

            @endauth

        </div>

    </div>

    {{-- NAVBAR --}}
    <div class="bg-indigo-600 text-white">

        <div class="max-w-7xl mx-auto px-6">

            <div class="flex gap-6 py-3">

                <a href="/dashboard">
                    Dashboard
                </a>

                <a href="/pos">
                    POS
                </a>

                <a href="/transactions">
                    Transaksi
                </a>

                <a href="/products">
                    Produk
                </a>

                <a href="/stock-opname">
                    Stock Opname
                </a>

                <a href="/users">
                    Master User
                </a>

            </div>

        </div>

    </div>

    {{-- CONTENT --}}
    <div
        class="max-w-7xl mx-auto p-6"
    >

        @yield('content')

    </div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>    
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

{{-- pesan2 sesuai status transaksi success,error dsb --}}
    @if(session('success'))

    <script>

    document.addEventListener('DOMContentLoaded', function () {

        Swal.fire({

            icon: 'success',

            title: 'Berhasil',

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