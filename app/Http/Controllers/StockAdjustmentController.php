<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustment;
use App\Models\StockAdjustmentDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockAdjustmentController extends Controller
{
    public function index()
    {
        $adjustments = StockAdjustment::with('user')->latest()->paginate(10);
        return view('stock-adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        // Generate Nomor SA otomatis (Format: SA-YYYYMMDD-0001)
        $dateStr = now()->format('Ymd');
        $prefix = 'SA-' . $dateStr . '-';
        
        $lastSA = StockAdjustment::where('nomor_sa', 'like', $prefix . '%')
            ->orderBy('nomor_sa', 'desc')
            ->first();

        if ($lastSA) {
            $lastNum = intval(substr($lastSA->nomor_sa, -4));
            $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '0001';
        }

        $nomor_sa = $prefix . $nextNum;
        $products = Product::where('is_active', 1)->orderBy('nama_barang')->get();

        return view('stock-adjustments.create', compact('nomor_sa'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input (Sesuai dengan name attribute HTML input array)
        $request->validate([
            'nomor_sa'     => 'required|unique:stock_adjustments,nomor_sa',
            'tgl_sa'       => 'required|date',
            'product_id'   => 'required|array|min:1',
            'product_id.*' => 'required|exists:products,id',
            'qty'          => 'required|array|min:1',
            'qty.*'        => 'required|integer|min:1',
            'notes'        => 'nullable|array',
        ]);

        // Menentukan status berdasarkan value tombol submit yang diklik
        $status = $request->input('action') === 'closed' ? 'closed' : 'draft';

        DB::transaction(function () use ($request, $status) {
            // 2. Simpan Master Dokumen (Status tersimpan sesuai klik: draft / closed)
            $sa = StockAdjustment::create([
                'nomor_sa'        => $request->nomor_sa,
                'tgl_sa'          => $request->tgl_sa,
                'user_id'         => Auth::id(),
                'status'          => $status,
                'catatan'         => $request->catatan,
                'tgl_jam_selesai' => $status === 'closed' ? now() : null,
            ]);

            // 3. Loop Item Detail
            foreach ($request->product_id as $index => $productId) {
                $product   = Product::findOrFail($productId);
                $qtyAdjust = (int) $request->qty[$index];
                $itemNotes = $request->notes[$index] ?? null;

                $stockBefore = $product->stok;
                $stockAfter  = $stockBefore - $qtyAdjust;

                // FIX: Menggunakan $sa->id (properti), BUKAN $sa->id() (method)
                StockAdjustmentDetail::create([
                    'stock_adjustment_id' => $sa->id, 
                    'product_id'          => $productId,
                    'stock_system'        => $stockBefore,
                    'qty'                 => $qtyAdjust,
                    'notes'               => $itemNotes,
                ]);

                // Jika tombolnya "Posting & Kunci Stok", jalankan potong stok & mutasi barang
                if ($status === 'closed') {
                    $product->update(['stok' => $stockAfter]);

                    DB::table('stock_movements')->insert([
                        'product_id'   => $product->id,
                        'type'         => 'Stock Adjustment',
                        'qty'          => -$qtyAdjust,
                        'stock_before' => $stockBefore,
                        'stock_after'  => $stockAfter,
                        'reference_no' => $sa->nomor_sa,
                        'notes'        => $itemNotes ?? 'Adjustment barang rusak/expired (' . $sa->nomor_sa . ')',
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                }
            }
        });

        // 4. Kembali ke halaman utama (Index) membawa pesan sukses
        return redirect()->route('stock-adjustments.index')
            ->with('success', $status === 'closed' ? 'Stock Adjustment berhasil diposting!' : 'Draft Stock Adjustment berhasil disimpan ke database.');
    }

    public function post(StockAdjustment $stockAdjustment)
    {
        if ($stockAdjustment->status === 'closed') {
            return back()->with('error', 'Dokumen ini sudah diposting sebelumnya.');
        }

        DB::transaction(function () use ($stockAdjustment) {
            // Muat ulang item detail
            $details = $stockAdjustment->details()->with('product')->get();

            foreach ($details as $detail) {
                $product = $detail->product;
                
                $stockBefore = $product->stok;
                $stockAfter = $stockBefore - $detail->qty; // Selalu Mengurangi Stok sesuai kesepakatan bisnis

                // A. Potong Stok Utama di Tabel Products
                $product->update([
                    'stok' => $stockAfter
                ]);

                // B. Suntik Riwayat Perubahan ke Tabel stock_movements milik kamu
                DB::table('stock_movements')->insert([
                    'product_id' => $product->id,
                    'type' => 'Stock Adjustment',
                    'qty' => -$detail->qty, // disimpan minus karena mengurangi stok
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'reference_no' => $stockAdjustment->nomor_sa,
                    'notes' => $detail->notes ?? 'Stock adjustment dari dokumen ' . $stockAdjustment->nomor_sa,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // C. Kunci Dokumen & Update Status Jadi Closed
            $stockAdjustment->update([
                'status' => 'closed',
                'tgl_jam_selesai' => now()
            ]);
        });

        return redirect()->route('stock-adjustments.index')->with('success', 'Dokumen SA berhasil diposting! Stok produk telah disesuaikan.');
    }
    // edit
    public function edit(StockAdjustment $stockAdjustment)
    {
        // Cegah edit jika status dokumen sudah closed / dikunci
        // if ($stockAdjustment->status === 'closed') {
        //     return redirect()->route('stock-adjustments.index')
        //         ->with('error', 'Dokumen yang sudah diposting tidak dapat diubah kembali.');
        // }

        // Muat data detail produk yang terikat dengan adjustment ini
        $details = $stockAdjustment->details()->with('product')->get();

        // Transformasi ke format JSON/Array agar bisa dibaca oleh JavaScript cart di Blade
        $cartData = $details->map(function ($detail) {
            return [
                'id' => $detail->product_id,
                'name' => $detail->product->nama_barang,
                'code' => $detail->product->kode_barang,
                'qty' => $detail->qty,
                'notes' => $detail->notes ?? ''
            ];
        });

        return view('stock-adjustments.edit', compact('stockAdjustment', 'cartData'));
    }

    // update
    public function update(Request $request, StockAdjustment $stockAdjustment)
    {
        if ($stockAdjustment->status === 'closed') {
            return redirect()->route('stock-adjustments.index')
                ->with('error', 'Dokumen sudah dikunci.');
        }

        // 1. Validasi Input
        $request->validate([
            'tgl_sa'       => 'required|date',
            'product_id'   => 'required|array|min:1',
            'product_id.*' => 'required|exists:products,id',
            'qty'          => 'required|array|min:1',
            'qty.*'        => 'required|integer|min:1',
            'notes'        => 'nullable|array',
        ], [
            'product_id.required' => 'Wajib memilih minimal 1 produk!',
            'qty.*.min' => 'Kuantitas tidak boleh kurang dari 1!'
        ]);

        $status = $request->input('action') === 'closed' ? 'closed' : 'draft';

        DB::transaction(function () use ($request, $stockAdjustment, $status) {
            // 2. Update Master Dokumen
            $stockAdjustment->update([
                'tgl_sa'          => $request->tgl_sa,
                'status'          => $status,
                'catatan'         => $request->catatan,
                'tgl_jam_selesai' => $status === 'closed' ? now() : null,
            ]);

            // 3. Hapus detail lama, nanti kita re-insert yang baru (Pendekatan terbersih untuk baris dinamis)
            $stockAdjustment->details()->delete();

            // 4. Insert Detail Baru & Eksekusi Potong Stok jika status berubah jadi Closed
            foreach ($request->product_id as $index => $productId) {
                $product   = Product::findOrFail($productId);
                $qtyAdjust = (int) $request->qty[$index];
                $itemNotes = $request->notes[$index] ?? null;

                $stockBefore = $product->stok;
                $stockAfter  = $stockBefore - $qtyAdjust;

                StockAdjustmentDetail::create([
                    'stock_adjustment_id' => $stockAdjustment->id,
                    'product_id'          => $productId,
                    'stock_system'        => $stockBefore,
                    'qty'                 => $qtyAdjust,
                    'notes'               => $itemNotes,
                ]);

                if ($status === 'closed') {
                    $product->update(['stok' => $stockAfter]);

                    DB::table('stock_movements')->insert([
                        'product_id'   => $product->id,
                        'type'         => 'Stock Adjustment',
                        'qty'          => -$qtyAdjust,
                        'stock_before' => $stockBefore,
                        'stock_after'  => $stockAfter,
                        'reference_no' => $stockAdjustment->nomor_sa,
                        'notes'        => $itemNotes ?? 'Adjustment barang rusak/expired (' . $stockAdjustment->nomor_sa . ')',
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                }
            }
        });

        return redirect()->route('stock-adjustments.index')
            ->with('success', $status === 'closed' ? 'Stock Adjustment berhasil diposting!' : 'Perubahan Draft berhasil disimpan.');
    }
}