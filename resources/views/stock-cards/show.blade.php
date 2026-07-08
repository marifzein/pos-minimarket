@extends('layouts.app')

@section('title', 'Detail Kartu Stok')

@section('content')

{{-- 1. Menggunakan komponen x-page-header dengan slot action button Kembali --}}
<x-page-header
    title="Detail Kartu Stok"
    subtitle="Riwayat keluar masuk barang untuk {{ $product->nama_barang }}"
>
    <x-slot:action>
        <a href="{{ route('stock-cards.index') }}">
            <x-button color="gray" type="button">
                <i class="ri-arrow-left-line"></i>
                Kembali
            </x-button>
        </a>
    </x-slot:action>
</x-page-header>

{{-- 2. Ringkasan Informasi Produk menggunakan grid x-card --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <x-card class="flex flex-col justify-between p-5">
        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Kode Barang</span>
        <span class="text-lg font-bold text-slate-700 mt-1">{{ $product->kode_barang }}</span>
    </x-card>
    
    <x-card class="flex flex-col justify-between p-5">
        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Nama Produk</span>
        <span class="text-lg font-bold text-slate-700 mt-1">{{ $product->nama_barang }}</span>
    </x-card>

    <x-card class="flex flex-col justify-between p-5">
        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Stok Saat Ini</span>
        <div class="mt-1">
            @if($product->stok <= $product->min_stok)
                <x-badge color="red" size="md" class="font-bold">{{ $product->stok }} {{ $product->satuan }}</x-badge>
            @else
                <x-badge color="green" size="md" class="font-bold">{{ $product->stok }} {{ $product->satuan }}</x-badge>
            @endif
        </div>
    </x-card>
</div>

{{-- 3. Tabel Riwayat Mutasi Stok dengan Komponen Sistem --}}
<x-card>
    <x-table>
        <x-table-header>
            <tr>
                <x-table-head class="text-left">Tanggal</x-table-head>
                <x-table-head class="text-right">Qty</x-table-head>
                <x-table-head class="text-right">Sebelum</x-table-head>
                <x-table-head class="text-right">Sesudah</x-table-head>
                <x-table-head class="text-center">Tipe</x-table-head>
                <x-table-head class="text-left">Referensi / Alasan</x-table-head>
            </tr>
        </x-table-header>

        <tbody>
        @forelse($movements as $row)
            <tr class="hover:bg-slate-50 transition-colors duration-150">
                <x-table-cell class="text-left">
                    <span class="text-slate-600 font-medium">{{ $row->created_at->format('d M Y H:i') }}</span>
                </x-table-cell>
                
                

                {{-- <x-table-cell class="text-right font-bold {{ $row->qty > 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $row->qty > 0 ? '+'.$row->qty : $row->qty }}
                </x-table-cell> --}}
                <x-table-cell class="text-right font-bold">
                    @if($row->qty > 0)
                        <span class="text-emerald-600">+{{ $row->qty }}</span>
                    @else
                        <span class="text-red-600">{{ $row->qty }}</span>
                    @endif
                </x-table-cell>

                <x-table-cell class="text-right text-slate-500">
                    {{ $row->stock_before }}
                </x-table-cell>

                <x-table-cell class="text-right text-emerald-600 font-semibold">
                    {{ $row->stock_after }}
                </x-table-cell>

                {{-- <x-table-cell class="text-center">
                    @if(in_array(strtolower($row->type), ['in', 'masuk', 'purchase']))
                        <x-badge color="green">Masuk</x-badge>
                    @elseif(in_array(strtolower($row->type), ['out', 'keluar', 'sale']))
                        <x-badge color="red">Keluar</x-badge>
                    @else
                        <x-badge color="yellow">{{ ucfirst($row->type) }}</x-badge>
                    @endif
                </x-table-cell> --}}

                <x-table-cell class="text-center">
                    @php
                        // Tentukan warna badge berdasarkan nilai Qty
                        if ($row->qty > 0) {
                            // Jika tipe opening biasanya awal awal/stok awal, bisa pakai yellow atau green
                            $badgeColor = (strtolower($row->type) === 'opening') ? 'yellow' : 'green';
                        } else {
                            $badgeColor = 'red';
                        }
                    @endphp

                    {{-- Menampilkan string type asli dari database (misal: SALE, Stock Adjustment) --}}
                    <x-badge :color="$badgeColor">
                        {{ $row->type }}
                    </x-badge>
                </x-table-cell>


                <x-table-cell class="text-left">
                    <div class="text-slate-700 font-medium">{{ $row->reference_no ?? '-' }}</div>
                    @if($row->notes)
                        <div class="text-xs text-slate-400 italic mt-0.5">{{ $row->notes }}</div>
                    @endif
                </x-table-cell>
            </tr>
        @empty
            <tr>
                <td colspan="6">
                    <x-empty-state
                        icon="ri-history-line"
                        title="Belum ada mutasi stok"
                        description="Seluruh riwayat perubahan stok produk ini akan tercatat di sini."
                    />
                </td>
            </tr>
        @endforelse
        </tbody>
    </x-table>
</x-card>

@endsection