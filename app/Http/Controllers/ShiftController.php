<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\Transaction; // 💡 Pastikan model Transaction di-import di sini
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ShiftController extends Controller
{
    // 1. TAMPILKAN FORM ISI MODAL AWAL
    public function showOpenForm()
    {
        $activeShift = Shift::where('user_id', Auth::id())
                            ->where('status', 'open')
                            ->exists();

        if ($activeShift) {
            return redirect('/pos');
        }

        return view('pos.open-shift');
    }

    // 2. SIMPAN MODAL AWAL KE DATABASE
    public function storeOpenShift(Request $request)
    {
        $request->validate([
            'starting_cash' => 'required|numeric|min:0',
        ], [
            'starting_cash.required' => 'Uang modal awal wajib diisi!',
            'starting_cash.numeric' => 'Format harus berupa angka!',
            'starting_cash.min' => 'Uang modal tidak boleh minus!',
        ]);

        $activeShift = Shift::where('user_id', Auth::id())
                            ->where('status', 'open')
                            ->exists();

        if ($activeShift) {
            return redirect('/pos');
        }

        Shift::create([
            'user_id' => Auth::id(),
            'starting_cash' => $request->starting_cash,
            'total_cash_sales' => 0,
            'operational_expense' => 0,
            'expected_cash' => $request->starting_cash,
            'status' => 'open',
            'opened_at' => now(),
        ]);

        return redirect('/pos')->with('success', 'Shift berhasil dibuka. Selamat bertugas!');
    }

    // 3. 💡 TAMPILKAN HALAMAN TUTUP SHIFT (KALKULASI SEBELUM CLOSING)
    public function showCloseForm()
    {
        $activeShift = Shift::where('user_id', Auth::id())
                            ->where('status', 'open')
                            ->first();

        if (!$activeShift) {
            return redirect('/pos')->with('error', 'Tidak ada shift aktif yang perlu ditutup.');
        }

        // Hitung total penjualan cash (cash - kembalian)
        $totalCashSales = Transaction::where('shift_id', $activeShift->id)
                                     ->sum('cash'); 
                                     
        $totalKembalian = Transaction::where('shift_id', $activeShift->id)
                                     ->sum('kembalian');
                                     
        $netCashSales = $totalCashSales - $totalKembalian;

        // Ekspektasi saldo laci = Modal awal + penjualan bersih - pengeluaran operasional
        $expectedCash = $activeShift->starting_cash + $netCashSales - $activeShift->operational_expense;

        return view('pos.close-shift', compact('activeShift', 'netCashSales', 'expectedCash'));
    }

    // 4. 💡 PROSES TUTUP SHIFT & SIMPAN PERBEDAAN (VARIANCE) KE DB
    public function storeCloseShift(Request $request)
    {
        $request->validate([
            'ending_cash_actual' => 'required|numeric|min:0',
            'variance_reason' => 'nullable|string|max:500',
        ]);

        $activeShift = Shift::where('user_id', Auth::id())
                            ->where('status', 'open')
                            ->first();

        if (!$activeShift) {
            return redirect('/pos')->with('error', 'Shift tidak ditemukan.');
        }

        $totalCashSales = Transaction::where('shift_id', $activeShift->id)->sum('cash');
        $totalKembalian = Transaction::where('shift_id', $activeShift->id)->sum('kembalian');
        $netCashSales = $totalCashSales - $totalKembalian;
        
        $expectedCash = $activeShift->starting_cash + $netCashSales - $activeShift->operational_expense;
        
        $endingCashActual = $request->ending_cash_actual;
        $variance = $endingCashActual - $expectedCash;

        $activeShift->update([
            'total_cash_sales' => $netCashSales,
            'expected_cash' => $expectedCash,
            'ending_cash_actual' => $endingCashActual,
            'variance' => $variance,
            'variance_reason' => $request->variance_reason,
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        return redirect('/pos/open-shift')->with('success', 'Shift berhasil ditutup! Laporan shift telah disimpan.');
    }
}