<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;

class TransactionController extends Controller
{
    // list transaksi
    public function show($id)
    {
        $transaction =
            Transaction::with('details')
            ->findOrFail($id);

        return view(
            'transactions.show',
            compact('transaction')
        );
    }

    // load page
    public function index()
    {
        $transactions =
            Transaction::latest()
            ->paginate(20);

        return view(
            'transactions.index',
            compact('transactions')
        );
    }

    // save transaksi
    public function store(Request $request)
    {
        $cart = $request->cart ?? [];

        if (count($cart) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cart kosong'
            ], 422);
        }

        $customer = null;

        if ($request->pelanggan) {

            $customer = Customer::where(
                'kode_pelanggan',
                $request->pelanggan
            )->first();

        }
        
        $subtotal =
            (float) $request->subtotal;

        $voucher =
            (float) $request->voucher;

        $card =
            (float) $request->card;

        $grandTotal =
            (float) $request->grand_total;

        $cash =
            (float) $request->cash;

        $paymentTotal =
            $cash + $card + $voucher;

        if ($paymentTotal < $grandTotal)
        {
            return response()->json([
                'success' => false,
                'message' =>
                    'Pembayaran kurang'
            ], 422);
        }

        DB::beginTransaction();

        try {

            // $noNota ='INV-' . now()->format('YmdHis');
            $noNota = Transaction::generateNoNota();

             $customer = null;

            if ($request->pelanggan) {

                $customer = Customer::where(
                    'kode_pelanggan',
                    $request->pelanggan
                )->first();

            }

            $transaction = Transaction::create([

                'no_nota'      => $noNota,
                'user_id'      => 1,

                'pelanggan' => $request->pelanggan,

                'telp' => $customer?->telepon,

                'subtotal'     => $request->subtotal,

                'voucher'      => $request->voucher,

                'card'         => $request->card,

                'grand_total'  => $request->grand_total,

                'cash'         => $request->cash,

                'kembalian'    => $request->kembalian,
            ]);

            foreach ($cart as $item) {

                if (
                        !isset($item['qty'])
                        ||
                        $item['qty'] < 1
                    )
                    {
                        throw new \Exception(
                            'Qty tidak valid'
                        );
                    }

                $product =
                    Product::findOrFail(
                        $item['id']
                    );

                if (
                    $product->stok <
                    $item['qty']
                ) {
                    throw new \Exception(
                        $product->nama_barang .
                        ' stok tidak cukup'
                    );
                }

                TransactionDetail::create([

                    'transaction_id' =>
                        $transaction->id,

                    'product_id' =>
                        $product->id,

                    'kode_barang' =>
                        $product->kode_barang,

                    'nama_barang' =>
                        $product->nama_barang,

                    'harga' =>
                        $item['harga'],

                    'qty' =>
                        $item['qty'],

                    'subtotal' =>
                        $item['harga'] *
                        $item['qty']
                ]);

                $stokSebelum =
                    $product->stok;

                $stokSesudah =
                    $stokSebelum -
                    $item['qty'];

                $product->update([
                    'stok' =>
                        $stokSesudah
                ]);

                StockMovement::create([

                    'product_id' =>
                        $product->id,

                    'type' =>
                        'SALE',

                    'qty' =>
                        -$item['qty'],

                    'stock_before' =>
                        $stokSebelum,

                    'stock_after' =>
                        $stokSesudah,

                    'reference_no' =>
                        $noNota
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'transaction_id' => $transaction->id,
                'no_nota' => $noNota
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // cetak struk
    public function print($id)
    {
        $transaction =
            Transaction::with('details')
            ->findOrFail($id);

        $customer = null;

        if ($transaction->pelanggan) {

            $customer = Customer::where(
                'kode_pelanggan',
                $transaction->pelanggan
            )->first();

        }

        return view(
            'transactions.print',
            compact(
                'transaction',
                'customer'
            )
        );
    }

    
}