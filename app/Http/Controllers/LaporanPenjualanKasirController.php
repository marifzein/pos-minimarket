<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanPenjualanKasirController extends Controller
{
    public function index(Request $request)
    {
        // Default tanggal dari tanggal 1 bulan ini sampai tanggal hari ini (2026)
        $dari_tanggal = $request->get('dari_tanggal', Carbon::now()->startOfMonth()->toDateString());
        $sampai_tanggal = $request->get('sampai_tanggal', Carbon::now()->toDateString());

        // Perbaikan Query: cash dikurangi kembalian agar mencerminkan omzet riil kasir
        $query = DB::table('transactions')
            ->join('users', 'transactions.user_id', '=', 'users.id')
            ->select(
                DB::raw('DATE(transactions.created_at) as tanggal'),
                'users.name as nama_kasir',
                DB::raw('SUM(transactions.cash - transactions.kembalian) as total_cash'),
                DB::raw('SUM(transactions.card) as total_card'),
                DB::raw('SUM(transactions.voucher) as total_voucher'),
                DB::raw('SUM((transactions.cash - transactions.kembalian) + transactions.card + transactions.voucher) as total_grand')
            )
            ->whereBetween(DB::raw('DATE(transactions.created_at)'), [$dari_tanggal, $sampai_tanggal])
            ->groupBy(DB::raw('DATE(transactions.created_at)'), 'transactions.user_id', 'users.name')
            ->orderBy('tanggal', 'asc')
            ->orderBy('nama_kasir', 'asc');

        $reports = $query->paginate(20)->withQueryString();

        // Perbaikan hitungan total footer bawah agar sinkron seirama
        $totals = DB::table('transactions')
            ->select(
                DB::raw('SUM(transactions.cash - transactions.kembalian) as total_cash'),
                DB::raw('SUM(transactions.card) as total_card'),
                DB::raw('SUM(transactions.voucher) as total_voucher'),
                DB::raw('SUM((transactions.cash - transactions.kembalian) + transactions.card + transactions.voucher) as total_grand')
            )
            ->whereBetween(DB::raw('DATE(transactions.created_at)'), [$dari_tanggal, $sampai_tanggal])
            ->first();

        return view('laporan.penjualan-kasir', compact('reports', 'totals', 'dari_tanggal', 'sampai_tanggal'));
    }
}