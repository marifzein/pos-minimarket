<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\StockMovement;
use App\Models\Category;
use App\Models\ProductPrice;

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


    public function index(Request $request)
    {
        $products = Product::with('category');

        // ======================
        // Search
        // ======================

        if ($request->filled('search')) {

            $search = trim($request->search);

            $products->where(function ($q) use ($search) {

                $q->where('nama_barang','like',"%{$search}%")
                ->orWhere('kode_barang','like',"%{$search}%")
                ->orWhere('barcode','like',"%{$search}%");

            });

        }

        // ======================
        // Filter kategori
        // ======================

        if ($request->filled('category')) {

            $products->where(
                'category_id',
                $request->category
            );

        }

        // ======================
        // Filter stok
        // ======================

        if ($request->filled('stock')) {

            switch($request->stock){

                case 'available':

                    $products->where('stok','>',0);

                    break;

                case 'empty':

                    $products->where('stok',0);

                    break;

                case 'low':

                    $products->whereColumn(
                        'stok',
                        '<=',
                        'min_stok'
                    );

                    break;

            }

        }

        $products = $products
            ->orderBy('nama_barang')
            ->paginate(20)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view(
            'products.index',
            compact(
                'products',
                'categories'
            )
        );
    }

    // bikin produk baru
    public function create()
    {
         $categories = Category::where('is_active', true)
                    ->orderBy('name')
                    ->get();

        return view(
            'products.create',
            compact('categories')
        );
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
            
            'min_qty.*' =>
                'nullable|integer|min:2',

            'potongan.*' =>
                'nullable|numeric|min:1',
        ]);
        
        // simpan produk
        $product = Product::create([

            'kode_barang'   => $request->kode_barang,

            'barcode'       => $request->barcode,

            'nama_barang'   => $request->nama_barang,

            'category_id'   => $request->category_id,

            'harga'         => $request->harga,

            'harga_diskon'  => $request->harga_diskon,

            'stok'          => $request->stok,

            'min_stok'      => $request->min_stok,

            'satuan'        => $request->satuan,

            'catatan'       => $request->catatan,

            'is_active'     => $request->has('is_active')

        ]);

        /*
        |--------------------------------------------------------------------------
        | Harga Grosir
        |--------------------------------------------------------------------------
        */

        if($request->min_qty){

            foreach($request->min_qty as $i=>$qty){

                // parsiapan
                if(
                    empty($qty)
                    ||
                    empty($request->potongan[$i])
                ){
                    continue;
                }

                ProductPrice::create([

                    'product_id' => $product->id,

                    'min_qty'    => $qty,

                    // Simpan nilai  potongan 
                    'potongan'   => $request->potongan[$i]

                ]);

            }

        }

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
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'products.edit',
            compact(
                'product',
                'categories'
            )
        );
    }

    // update/simpan perubahan produk
   public function update(Request $request, Product $product)
    {
        $request->validate([
            'nama_barang' => 'required',
            'category_id' => 'nullable|exists:categories,id',
            'harga'        => 'required|numeric|min:0',
            'harga_diskon' => 'nullable|numeric|min:0',
            'min_stok'     => 'required|integer|min:0',
            'satuan'       => 'required',
            
            // Validasi Grosir sama seperti saat create produk
            'min_qty.*'      => 'nullable|integer|min:2',
            'potongan.*' => 'nullable|numeric|min:1',
        ]);

        $product->update([
            'barcode'      => $request->barcode,
            'nama_barang'  => $request->nama_barang,
            'category_id'  => $request->category_id,
            'harga'        => $request->harga,
            'harga_diskon' => $request->harga_diskon,
            'min_stok'     => $request->min_stok,
            'satuan'       => $request->satuan,
            'catatan'      => $request->catatan,
            'is_active'    => $request->has('is_active') ? 1 : 0,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Update Harga Grosir (Sync)
        |--------------------------------------------------------------------------
        */
        // 1. Hapus data harga grosir lama milik produk ini
        $product->prices()->delete(); 

        // 2. Simpan ulang data grosir yang baru dikirim dari form jika ada
        if ($request->min_qty) {
            foreach ($request->min_qty as $i => $qty) {
                if (empty($qty) || empty($request->potongan[$i])) {
                    continue;
                }

                ProductPrice::create([
                    'product_id' => $product->id,
                    'min_qty'    => $qty,
                    'potongan'   => $request->potongan[$i]
                ]);
            }
        }

        return redirect('/products')
            ->with('success', 'Produk berhasil diupdate');
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