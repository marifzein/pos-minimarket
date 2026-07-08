@extends('layouts.app')

@section('title', 'Laporan Penjualan - KASIR')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <x-card class="bg-white shadow rounded-lg p-6">
        
        <!-- Header & Judul -->
        <div class="flex items-center mb-6">
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                <i class="ri-file-list-3-line text-indigo-600"></i> Laporan Penjualan - KASIR
            </h2>
        </div>

        <!-- Filter Tanggal -->
        <form method="GET" action="{{ route('laporan.penjualan-kasir') }}" class="bg-slate-50 border border-slate-100 rounded-xl p-5 mb-6">
            <div class="flex flex-wrap items-end gap-5">
                <div class="w-full sm:w-auto">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Dari Tanggal</label>
                    <input type="date" name="dari_tanggal" value="{{ $dari_tanggal }}" 
                        class="rounded-xl border border-slate-300 px-4 py-2.5 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 w-full sm:w-56">
                </div>
                <div class="w-full sm:w-auto">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Sampai Tanggal</label>
                    <input type="date" name="sampai_tanggal" value="{{ $sampai_tanggal }}" 
                        class="rounded-xl border border-slate-300 px-4 py-2.5 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 w-full sm:w-56">
                </div>
                <div>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition duration-150">
                        Tampilkan
                    </button>
                </div>
            </div>
        </form>

       <!-- Tabel Laporan -->
        <x-table class="border-collapse">
            <x-table-header>
                <tr class="border-b border-slate-200">
                    <x-table-head class="text-center w-12 font-bold bg-slate-100 text-slate-700 border-r border-slate-200" rowspan="2">No</x-table-head>
                    <x-table-head class="text-left font-bold bg-slate-100 text-slate-700 border-r border-slate-200" rowspan="2">Tanggal</x-table-head>
                    <x-table-head class="text-left font-bold bg-slate-100 text-slate-700 border-r border-slate-200" rowspan="2">Nama Kasir</x-table-head>
                    <x-table-head class="text-center font-bold bg-slate-100 text-slate-700 border-b border-slate-200" colspan="4">Penjualan</x-table-head>
                </tr>
                <tr class="border-b border-slate-200">
                    <x-table-head class="text-right font-bold bg-slate-50 text-slate-700 w-44 border-r border-slate-200">Omzet Cash</x-table-head>
                    <x-table-head class="text-right font-bold bg-slate-50 text-slate-700 w-44 border-r border-slate-200">Omzet Card</x-table-head>
                    <x-table-head class="text-right font-bold bg-slate-50 text-slate-700 w-44 border-r border-slate-200">Omzet Voucher</x-table-head>
                    <x-table-head class="text-right font-bold bg-slate-50 text-slate-700 w-44">Total</x-table-head>
                </tr>
            </x-table-header>
            
            <x-table-body>
                @forelse ($reports as $report)
                    <x-table-row>
                        <x-table-cell class="text-center border-r border-slate-200">{{ ($reports->currentPage() - 1) * $reports->perPage() + $loop->iteration }}</x-table-cell>
                        <x-table-cell class="border-r border-slate-200">{{ \Carbon\Carbon::parse($report->tanggal)->translatedFormat('d F Y') }}</x-table-cell>
                        <x-table-cell class="font-medium text-slate-900 border-r border-slate-200">{{ $report->nama_kasir }}</x-table-cell>
                        <x-table-cell class="text-right border-r border-slate-200">Rp {{ number_format($report->total_cash, 0, ',', '.') }}</x-table-cell>
                        <x-table-cell class="text-right border-r border-slate-200">Rp {{ number_format($report->total_card, 0, ',', '.') }}</x-table-cell>
                        <x-table-cell class="text-right border-r border-slate-200">Rp {{ number_format($report->total_voucher, 0, ',', '.') }}</x-table-cell>
                        <x-table-cell class="text-right font-semibold text-slate-900">Rp {{ number_format($report->total_grand, 0, ',', '.') }}</x-table-cell>
                    </x-table-row>
                @empty
                    <x-table-row>
                        <x-table-cell colspan="7" class="text-center py-8 text-slate-400 italic">
                            Tidak ada data penjualan pada rentang tanggal ini.
                        </x-table-cell>
                    </x-table-row>
                @endforelse
            </x-table-body>

            <!-- Bagian Total Footer -->
            @if($reports->count() > 0)
                <tfoot class="bg-slate-100 border-t-2 border-slate-300 font-bold text-slate-800 text-sm">
                    <tr>
                        <td class="px-3 py-4 text-center border-r border-b border-slate-200" colspan="3">TOTAL PERIODE INI</td>
                        <td class="px-2 py-4 text-right text-indigo-700 border-r border-b border-slate-200">Rp {{ number_format($totals->total_cash, 0, ',', '.') }}</td>
                        <td class="px-2 py-4 text-right text-indigo-700 border-r border-b border-slate-200">Rp {{ number_format($totals->total_card, 0, ',', '.') }}</td>
                        <td class="px-2 py-4 text-right text-indigo-700 border-r border-b border-slate-200">Rp {{ number_format($totals->total_voucher, 0, ',', '.') }}</td>
                        <td class="px-2 py-4 text-right text-green-700 text-base border-b border-slate-200">Rp {{ number_format($totals->total_grand, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            @endif
        </x-table>

        <!-- Pagination Links -->
        <div class="mt-5">
            {{ $reports->links() }}
        </div>

    </x-card>
</div>
@endsection