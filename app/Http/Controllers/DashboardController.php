<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\StockOpname;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $todaySales =
            Transaction::whereDate(
                'created_at',
                today()
            )
            ->sum('grand_total');

        $todayTransactions =
            Transaction::whereDate(
                'created_at',
                today()
            )
            ->count();

        $totalProducts =
            Product::count();
        
        $hargaBeliNolCount = Product::where(function($query) {
            $query->where('harga_beli', 0)
                ->orWhereNull('harga_beli');
        })->count();

        $totalStock =
            Product::sum('stok');

        $lowStocks =
            Product::where('stok', '<=', 5)
            ->orderBy('stok')
            ->limit(10)
            ->get();
        
        $latestTransactions =
            Transaction::latest()
            ->limit(10)
            ->get();

        $topProducts = TransactionDetail::select(
                'nama_barang',
                DB::raw('SUM(qty) as total_terjual')
            )
            ->groupBy('nama_barang')
            ->orderByDesc('total_terjual')
            ->limit(10)
            ->get();
        
        $lastOpname = StockOpname::latest()->first();
        
        
        /*
        |--------------------------------------------------------------------------
        | Grafik Penjualan 7 Hari
        |--------------------------------------------------------------------------
        */

        $salesChart = [];

        for ($i = 6; $i >= 0; $i--) {

            $date = Carbon::today()->subDays($i);

            $salesChart[] = [

                'tanggal' => $date->format('d M'),

                'total' => Transaction::whereDate(
                    'created_at',
                    $date
                )->sum('grand_total')

            ];

        }

        return view(
            'dashboard.index',
            compact(
                'todaySales',
                'todayTransactions',
                'totalProducts',
                'hargaBeliNolCount',
                'totalStock',
                'lowStocks',
                'latestTransactions',
                'topProducts',
                'lastOpname',
                'salesChart'
            )
        );
    }

    
}