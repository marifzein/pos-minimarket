<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;

class PosController extends Controller
{
    public function index()
    {
        $products = Product::select(
            'id',
            'kode_barang',
            'barcode',
            'nama_barang',
            'harga',
            'harga_diskon',
            'stok'
        )->get();

        $noNota = Transaction::generateNoNota();

        return view(
            'pos.index',
            compact(
                'products',
                'noNota'
            )
        );
    }
}