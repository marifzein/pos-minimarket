<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;

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
            
        return view(
            'dashboard.index',
            compact(
                'todaySales',
                'todayTransactions',
                'totalProducts',
                'totalStock',
                'lowStocks',
                'latestTransactions',
                'topProducts',
            )
        );
    }

    
}