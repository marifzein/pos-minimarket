<!DOCTYPE html>
<html lang="id">
<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title','POS Minimarket')
    </title>

    <script defer
        src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js">
    </script>

    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-slate-100">

    <!-- HEADER -->

    <div class="bg-white border-b shadow-sm">

        <div class="max-w-7xl mx-auto px-6 py-4">

            <h1 class="text-2xl font-bold">
                POS Minimarket
            </h1>

        </div>

    </div>

    <!-- NAVBAR -->

    <div class="bg-indigo-600 text-white">

        <div
            class="max-w-7xl mx-auto px-6"
        >

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

                <a href="/stock-opname"
                    class="hover:underline"
                >
                    Stok Opname
                </a>

            </div>

        </div>

    </div>

    <!-- CONTENT -->

    <div
        class="max-w-7xl mx-auto p-4"
    >

        @yield('content')

    </div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
</body>
</html>