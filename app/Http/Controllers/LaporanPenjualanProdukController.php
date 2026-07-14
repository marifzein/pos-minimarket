<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanPenjualanProdukController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());
        $sortBy = $request->get('sort_by', 'kode_barang'); 
        $sortDir = $request->get('sort_dir', 'asc');

        // Validasi kolom sorting
        $allowedSorts = ['kode_barang', 'nama_barang', 'harga', 'total_terjual', 'total_pendapatan'];
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'kode_barang';
        if (!in_array($sortDir, ['asc', 'desc'])) $sortDir = 'asc';

        // Query Base
        $query = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->select(
                'transaction_details.kode_barang',
                'transaction_details.nama_barang',
                'transaction_details.harga',
                DB::raw('SUM(transaction_details.qty) as total_terjual'),
                DB::raw('SUM(transaction_details.subtotal) as total_pendapatan')
            )
            ->whereBetween(DB::raw('DATE(transactions.created_at)'), [$startDate, $endDate])
            ->groupBy(
                'transaction_details.product_id', 
                'transaction_details.kode_barang', 
                'transaction_details.nama_barang', 
                'transaction_details.harga'
            );

        // Hitung Grand Total Keseluruhan
        $totals = DB::table(DB::raw("({$query->toSql()}) as sub"))
            ->mergeBindings($query)
            ->select(
                DB::raw('SUM(total_terjual) as grand_qty'),
                DB::raw('SUM(total_pendapatan) as grand_revenue')
            )->first();

        // 💡 JIKA ACTION EXPORT EXCEL ATAU PRINT PDF
        $exportType = $request->get('export');

        if ($exportType === 'excel') {
            // Ambil semua data tanpa batasan pagination untuk ditarik ke Excel
            $reportData = $query->orderBy($sortBy, $sortDir)->get();
            
            $filename = "Laporan_Penjualan_Produk_{$startDate}_to_{$endDate}.xls";
            
            return response()->view('laporan.penjualan-produk.excel', compact('reportData', 'startDate', 'endDate', 'totals'))
                ->header('Content-Type', 'application/vnd.ms-excel')
                ->header('Content-Disposition', "attachment; filename={$filename}")
                ->header('Cache-Control', 'max-age=0');
        }

        if ($exportType === 'pdf') {
            // Ambil semua data tanpa batasan pagination untuk dicetak ke PDF
            $reportData = $query->orderBy($sortBy, $sortDir)->get();
            
            return view('laporan.penjualan-produk.pdf', compact('reportData', 'startDate', 'endDate', 'totals'));
        }

        // Tampilan Standar Web dengan Pagination (25 data)
        $reportData = $query->orderBy($sortBy, $sortDir)->paginate(15)->withQueryString();

        return view('laporan.penjualan-produk.index', compact(
            'reportData', 'startDate', 'endDate', 'sortBy', 'sortDir', 'totals'
        ));
    }
}