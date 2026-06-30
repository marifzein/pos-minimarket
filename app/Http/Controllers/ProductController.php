<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\StockMovement;

class ProductController extends Controller
{
    // cari produk
    public function search(Request $request)
    {
        $q = trim($request->q);

        if (!$q) {
            return response()->json([]);
        }

        $products = Product::query()
            ->where('nama_barang', 'like', "%{$q}%")
            ->orWhere('kode_barang', 'like', "%{$q}%")
            ->orWhere('barcode', 'like', "%{$q}%")
            ->limit(10)
            ->get([
                'id',
                'kode_barang',
                'barcode',
                'nama_barang',
                'harga',
                'harga_diskon',
                'stok'
            ]);

        return response()->json($products);
    }


    public function index()
    {
        $products =
            Product::orderBy(
                'nama_barang'
            )->paginate(20);

        return view(
            'products.index',
            compact('products')
        );
    }

    // bikin produk baru
    public function create()
    {
        return view('products.create');
    }
        
    // save data produk
    public function store(Request $request)
    {
        $request->validate([

            'kode_barang' =>
                'required|unique:products',

            'nama_barang' =>
                'required',

            'harga' =>
                'required|numeric|min:0',

            'stok' =>
                'required|integer|min:0',
        ]);

        $product = Product::create([

            'kode_barang' =>
                $request->kode_barang,

            'barcode' =>
                $request->barcode,

            'nama_barang' =>
                $request->nama_barang,

            'harga' =>
                $request->harga,

            'harga_diskon' =>
                $request->harga_diskon,

            'stok' =>
                $request->stok,
        ]);

        if ($request->stok > 0)
        {
            StockMovement::create([

                'product_id' =>
                    $product->id,

                'type' =>
                    'OPENING',

                'qty' =>
                    $request->stok,

                'stock_before' =>
                    0,

                'stock_after' =>
                    $request->stok,

                'reference_no' =>
                    'OPENING',

                'notes' =>
                    'Stok awal produk'
            ]);
        }

        return redirect('/products')
            ->with(
                'success',
                'Produk berhasil ditambahkan'
            );
    }

    // edit produk
    public function edit(Product $product)
    {
        return view(
            'products.edit',
            compact('product')
        );
    }

    // update/simpan perubahan produk
    public function update(
        Request $request,
        Product $product    
    )
    {
        $request->validate([

            'kode_barang' =>
                'required|unique:products,kode_barang,' .
                $product->id,

            'nama_barang' =>
                'required',

            'harga' =>
                'required|numeric|min:0',

            
        ]);

        $product->update([

            'kode_barang' =>
                $request->kode_barang,

            'barcode' =>
                $request->barcode,

            'nama_barang' =>
                $request->nama_barang,

            'harga' =>
                $request->harga,

            'harga_diskon' =>
                $request->harga_diskon,

            
        ]);

        return redirect('/products')
            ->with(
                'success',
                'Produk berhasil diupdate'
            );
    }

    // kartu stok
    public function stockCard(
        Product $product
    )
    {
        $movements =
            $product->stockMovements()
            ->latest()
            ->get();

        return view(
            'products.stock-card',
            compact(
                'product',
                'movements'
            )
        );
    }
}