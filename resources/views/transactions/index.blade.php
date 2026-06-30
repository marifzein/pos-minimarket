@extends('layouts.app')

@section('title','Riwayat Transaksi')

@section('content')

<h1 class="text-2xl font-bold mb-4">
    Riwayat Transaksi
</h1>

<div class="max-w-7xl mx-auto p-6">

    

    <div class="bg-white rounded-xl shadow">

        <table class="w-full">

            <thead>

            <tr class="bg-slate-100">

                <th class="p-3 text-left">
                    No Nota
                </th>

                <th class="p-3 text-left">
                    Tanggal
                </th>

                <th class="p-3 text-right">
                    Total
                </th>
                <th class="p-3 text-center">
                    Aksi
                </th>

            </tr>

            </thead>

            <tbody>

            @forelse($transactions as $trx)

                <tr class="border-b">

                    <td class="p-3">
                        {{ $trx->no_nota }}
                    </td>

                    <td class="p-3">
                        {{ $trx->created_at }}
                    </td>

                    <td class="p-3 text-right">
                        Rp {{ number_format($trx->grand_total,0,',','.') }}
                    </td>

                    <td class="p-3 text-center">

                        <a
                            href="{{ route('transactions.show',$trx->id) }}"
                            class="bg-blue-500 text-white px-3 py-1 rounded"
                        >
                            Detail
                        </a>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="3"
                        class="p-10 text-center text-gray-400">

                        Belum ada transaksi

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection