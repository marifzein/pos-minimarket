@extends('layouts.app')

@section('title','Produk')

@section('content')

<div class="flex justify-between mb-4">

    <h2 class="text-2xl font-bold">
        Master Produk
    </h2>

    <a
        href="/products/create"
        class="bg-indigo-600 text-white px-4 py-2 rounded"
    >
        + Tambah Produk
    </a>

</div>

<div
    class="bg-white rounded-xl shadow"
>

    <table class="w-full">

        <thead>

        <tr
            class="bg-slate-100"
        >

            <th class="p-3 text-left">
                Kode
            </th>

            <th class="p-3 text-left">
                Nama Barang
            </th>

            <th class="p-3 text-right">
                Harga
            </th>

            <th class="p-3 text-right">
                Stok
            </th>

            <th class="p-3 text-center">
                Aksi
            </th>

        </tr>

        </thead>

        <tbody>

        @forelse($products as $item)

        <tr
            class="border-b"
        >

            <td class="p-3">
                {{ $item->kode_barang }}
            </td>

            <td class="p-3">
                {{ $item->nama_barang }}
            </td>

            <td class="p-3 text-right">
                Rp {{ number_format($item->harga,0,',','.') }}
            </td>

            <td class="p-3 text-right">
                {{ $item->stok }}
            </td>

            <td
                class="p-3 text-center"
            >

                <a
                    href="/products/{{ $item->id }}/edit"
                    class="text-blue-600"
                >
                    Edit
                </a>

                |

                <a
                    href="/products/{{ $item->id }}/stock-card"
                    class="text-green-600"
                >
                    Kartu Stok
                </a>

            </td>

        </tr>

        @empty

        <tr>

            <td
                colspan="5"
                class="p-6 text-center text-gray-500"
            >

                Belum ada data produk

            </td>

        </tr>

        @endforelse

        </tbody>

    </table>

</div>

<div class="mt-4">

    {{ $products->links() }}

</div>

@endsection