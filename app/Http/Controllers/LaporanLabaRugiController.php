<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanLabaRugiController extends Controller
{
    public function index(Request $request)
    {
        // Default filter tanggal: dari awal bulan sampai hari ini
        $dari_tanggal = $request->get('dari_tanggal', Carbon::now()->startOfMonth()->toDateString());
        $sampai_tanggal = $request->get('sampai_tanggal', Carbon::now()->toDateString());

        // Subquery untuk menghitung omzet & HPP per item terelasi, di-group berdasarkan TANGGAL
        $reports = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->select(
                DB::raw('DATE(transactions.created_at) as tanggal'),
                DB::raw('SUM(transaction_details.subtotal) as total_pendapatan'),
                DB::raw('SUM(transaction_details.qty * products.harga_beli) as total_hpp'),
                DB::raw('SUM(transaction_details.subtotal) - SUM(transaction_details.qty * products.harga_beli) as laba_kotor')
            )
            ->whereBetween(DB::raw('DATE(transactions.created_at)'), [$dari_tanggal, $sampai_tanggal])
            ->groupBy(DB::raw('DATE(transactions.created_at)'))
            ->orderBy('tanggal', 'asc')
            ->paginate(20)
            ->withQueryString();

        // Hitung total akumulasi di bagian bawah (Footer)
        $totals = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->select(
                DB::raw('SUM(transaction_details.subtotal) as total_pendapatan'),
                DB::raw('SUM(transaction_details.qty * products.harga_beli) as total_hpp'),
                DB::raw('SUM(transaction_details.subtotal) - SUM(transaction_details.qty * products.harga_beli) as laba_kotor')
            )
            ->whereBetween(DB::raw('DATE(transactions.created_at)'), [$dari_tanggal, $sampai_tanggal])
            ->first();

        return view('laporan.laba-rugi', compact('reports', 'totals', 'dari_tanggal', 'sampai_tanggal'));
    }

    // Method untuk Export Excel
    public function exportExcel(Request $request)
    {
        $dari_tanggal = $request->get('dari_tanggal', Carbon::now()->startOfMonth()->toDateString());
        $sampai_tanggal = $request->get('sampai_tanggal', Carbon::now()->toDateString());

        // Tarik semua data tanpa pagination untuk laporan penuh
        $reports = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->select(
                DB::raw('DATE(transactions.created_at) as tanggal'),
                DB::raw('SUM(transaction_details.subtotal) as total_pendapatan'),
                DB::raw('SUM(transaction_details.qty * products.harga_beli) as total_hpp'),
                DB::raw('SUM(transaction_details.subtotal) - SUM(transaction_details.qty * products.harga_beli) as laba_kotor')
            )
            ->whereBetween(DB::raw('DATE(transactions.created_at)'), [$dari_tanggal, $sampai_tanggal])
            ->groupBy(DB::raw('DATE(transactions.created_at)'))
            ->orderBy('tanggal', 'asc')
            ->get();

        $totals = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->select(
                DB::raw('SUM(transaction_details.subtotal) as total_pendapatan'),
                DB::raw('SUM(transaction_details.qty * products.harga_beli) as total_hpp'),
                DB::raw('SUM(transaction_details.subtotal) - SUM(transaction_details.qty * products.harga_beli) as laba_kotor')
            )
            ->whereBetween(DB::raw('DATE(transactions.created_at)'), [$dari_tanggal, $sampai_tanggal])
            ->first();

        $filename = "Laporan_Laba_Rugi_" . $dari_tanggal . "_s_d_" . $sampai_tanggal . ".xls";

        // Menggunakan return response bawaan Laravel agar output buffer aman dan resmi
        return response()
            ->view('laporan.laba-rugi-excel', compact('reports', 'totals', 'dari_tanggal', 'sampai_tanggal'))
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    // Method untuk Cetak PDF
    public function exportPdf(Request $request)
    {
        $dari_tanggal = $request->get('dari_tanggal', Carbon::now()->startOfMonth()->toDateString());
        $sampai_tanggal = $request->get('sampai_tanggal', Carbon::now()->toDateString());

        $reports = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->select(
                DB::raw('DATE(transactions.created_at) as tanggal'),
                DB::raw('SUM(transaction_details.subtotal) as total_pendapatan'),
                DB::raw('SUM(transaction_details.qty * products.harga_beli) as total_hpp'),
                DB::raw('SUM(transaction_details.subtotal) - SUM(transaction_details.qty * products.harga_beli) as laba_kotor')
            )
            ->whereBetween(DB::raw('DATE(transactions.created_at)'), [$dari_tanggal, $sampai_tanggal])
            ->groupBy(DB::raw('DATE(transactions.created_at)'))
            ->orderBy('tanggal', 'asc')
            ->get();

        $totals = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->select(
                DB::raw('SUM(transaction_details.subtotal) as total_pendapatan'),
                DB::raw('SUM(transaction_details.qty * products.harga_beli) as total_hpp'),
                DB::raw('SUM(transaction_details.subtotal) - SUM(transaction_details.qty * products.harga_beli) as laba_kotor')
            )
            ->whereBetween(DB::raw('DATE(transactions.created_at)'), [$dari_tanggal, $sampai_tanggal])
            ->first();

        return view('laporan.laba-rugi-pdf', compact('reports', 'totals', 'dari_tanggal', 'sampai_tanggal'));
    }
}