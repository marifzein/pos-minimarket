@extends('layouts.app')

@section('title','Dashboard')

@section('content')

<h2
    class="text-2xl font-bold mb-6"
>
    Dashboard
</h2>

    <div
        class="grid grid-cols-1 md:grid-cols-4 gap-4"
    >

        <div
            class="bg-white p-5 rounded-xl shadow"
        >

            <div
                class="text-gray-500"
            >
                Penjualan Hari Ini
            </div>

            <div
                class="text-2xl font-bold"
            >
                Rp {{ number_format($todaySales,0,',','.') }}
            </div>

        </div>

        <div
            class="bg-white p-5 rounded-xl shadow"
        >

            <div
                class="text-gray-500"
            >
                Transaksi Hari Ini
            </div>

            <div
                class="text-2xl font-bold"
            >
                {{ $todayTransactions }}
            </div>

        </div>

        <div
            class="bg-white p-5 rounded-xl shadow"
        >

            <div
                class="text-gray-500"
            >
                Total Produk
            </div>

            <div
                class="text-2xl font-bold"
            >
                {{ $totalProducts }}
            </div>

        </div>

        <div
            class="bg-white p-5 rounded-xl shadow"
        >

            <div
                class="text-gray-500"
            >
                Total Stok
            </div>

            <div
                class="text-2xl font-bold"
            >
                {{ number_format($totalStock) }}
            </div>

        </div>
    </div>
    
    <!-- tambahan -->
     <div class="grid md:grid-cols-3 gap-4 mt-6">
    
        <div class="bg-white rounded-xl shadow p-4 min-h-[350px]">

            <h3 class="font-bold mb-3">
                Stok Menipis
            </h3>

            <table class="w-full">

                @forelse($lowStocks as $item)

                <tr class="border-b">

                    <td class="py-2">
                        {{ $item->nama_barang }}
                    </td>

                    <td class="text-right text-red-600 font-bold">
                        {{ $item->stok }}
                    </td>

                </tr>

                @empty

                <tr>

                    <td class="text-gray-500">
                        Tidak ada stok menipis
                    </td>

                </tr>

                @endforelse

            </table>

        </div>
    

        <div class="bg-white rounded-xl shadow p-4 min-h-[350px]">

                <h3 class="font-bold mb-3">
                    Transaksi Terakhir
                </h3>

                <table class="w-full">

                    @foreach($latestTransactions as $trx)

                    <tr class="border-b">

                        <td class="py-2">
                            {{ $trx->no_nota }}
                        </td>

                        <td class="text-right">

                            Rp
                            {{ number_format(
                                $trx->grand_total,
                                0,
                                ',',
                                '.'
                            ) }}

                        </td>

                    </tr>

                    @endforeach

                </table>

           

        </div>


        <div class="bg-white rounded-xl shadow p-4 min-h-[350px]">

            <h3 class="font-bold mb-3">
                Produk Terlaris
            </h3>

            <table class="w-full">

                @forelse($topProducts as $item)

                <tr class="border-b">

                    <td class="py-2">
                        {{ $item->nama_barang }}
                    </td>

                    <td class="text-right font-bold">

                        {{ number_format(
                            $item->total_terjual
                        ) }}

                        pcs

                    </td>

                </tr>

                @empty

                <tr>

                    <td class="text-gray-500">
                        Belum ada penjualan
                    </td>

                </tr>

                @endforelse

            </table>

        </div>

    </div>

@endsection