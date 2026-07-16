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

                <th class="p-3 text-left">
                    Kasir
                </th>

                <th class="p-3 text-left">
                    Pelanggan
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

                    <td class="p-3 text-slate-600">
                        {{ $trx->user?->name ?? 'System' }}
                    </td>

                    <!-- 💡 Menampilkan Nama Pelanggan / Kode Pelanggan -->
                    <td class="p-3">
                        @if($trx->pelanggan)
                            <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-md text-xs font-medium">
                                {{ $trx->customerRelation->nama }}
                            </span>
                        @else
                            <span class="text-gray-400 text-xs italic">Umum (Non-Member)</span>
                        @endif
                    </td>

                    <td class="p-3 text-right">
                        Rp {{ number_format($trx->grand_total,0,',','.') }}
                    </td>

                    <td class="p-3 text-center">

                        <a
                            href="{{ route('transactions.show',$trx->id) }}"
                            {{-- class="bg-blue-500 text-white px-3 py-1 rounded" --}}
                        >
                            <x-button color="green">
                                <i class="ri-printer-line"></i>
                                Detail
                            </x-button>
                            
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