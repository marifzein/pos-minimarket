@extends('layouts.app')

@section('title','Import Produk')

@section('content')
{{-- <!DOCTYPE html>
<html>

<head>
    <title>Detail Transaksi</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100"> --}}

<div class="max-w-5xl mx-auto p-6">

    <div class="flex gap-2 mb-4 justify-end">

        <a
            href="/transactions"
        >
            {{-- ← Kembali --}}
             <x-button color="secondary">
                <i class="ri-arrow-left-circle-line"></i>
                Kembali
            </x-button>
        </a>

        <a
            href="{{ route('transactions.print',$transaction->id) }}"
            target="_blank"
            {{-- class="bg-green-600 text-white px-4 py-2 rounded" --}}
        >
            {{-- 🖨 Cetak --}}
            <x-button color="green">
                <i class="ri-printer-line"></i>
                Cetak
            </x-button>
           
        </a>

    </div>

    <div class="bg-white rounded-xl shadow p-6">

        <h1 class="text-2xl font-bold mb-6">
            Detail Transaksi
        </h1>

        <div class="grid grid-cols-2 gap-4 mb-6">

            <div>

                <div>
                    <b>No Nota :</b>
                    {{ $transaction->no_nota }}
                </div>

                <div>
                    <b>Tanggal :</b>
                    {{ $transaction->created_at }}
                </div>

            </div>

            <div>

                <div>
                    <b>Pelanggan :</b>
                    {{ $transaction->pelanggan ?? 'Umum' }}
                </div>

            </div>

        </div>

        <table class="w-full border">

            <thead>

            <tr class="bg-slate-100">

                <th class="p-3 text-left">
                    Barang
                </th>

                <th class="p-3 text-center">
                    Qty
                </th>

                <th class="p-3 text-right">
                    Harga
                </th>

                <th class="p-3 text-right">
                    Subtotal
                </th>

            </tr>

            </thead>

            <tbody>

            @foreach($transaction->details as $item)

                <tr class="border-t">

                    <td class="p-3">
                        {{ $item->nama_barang }}
                    </td>

                    <td class="p-3 text-center">
                        {{ $item->qty }}
                    </td>

                    <td class="p-3 text-right">
                        {{ number_format($item->harga,0,',','.') }}
                    </td>

                    <td class="p-3 text-right">
                        {{ number_format($item->subtotal,0,',','.') }}
                    </td>

                </tr>

            @endforeach

            </tbody>

        </table>

        <div class="mt-6 flex justify-end">

            <div class="w-80 space-y-2">

                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span>
                        Rp {{ number_format($transaction->subtotal,0,',','.') }}
                    </span>
                </div>

                <div class="flex justify-between">
                    <span>Voucher</span>
                    <span>
                        Rp {{ number_format($transaction->voucher,0,',','.') }}
                    </span>
                </div>

                <div class="flex justify-between">
                    <span>Card</span>
                    <span>
                        Rp {{ number_format($transaction->card,0,',','.') }}
                    </span>
                </div>

                <hr>

                <div class="flex justify-between font-bold text-xl">
                    <span>Total</span>
                    <span>
                        Rp {{ number_format($transaction->grand_total,0,',','.') }}
                    </span>
                </div>

                <div class="flex justify-between">
                    <span>Bayar</span>
                    <span>
                        Rp {{ number_format($transaction->cash,0,',','.') }}
                    </span>
                </div>

                <div class="flex justify-between text-green-600 font-bold">
                    <span>Kembalian</span>
                    <span>
                        Rp {{ number_format($transaction->kembalian,0,',','.') }}
                    </span>
                </div>

            </div>

        </div>

    </div>

</div>

@endsection