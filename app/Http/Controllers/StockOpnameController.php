<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\StockMovement;
use App\Models\StockOpname;
use App\Models\StockOpnameDetail;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    
    public function index()
    {
        $openOpname = StockOpname::where(
            'status',
            'OPEN'
        )->first();

        if($openOpname)
        {
            return redirect(
                '/stock-opname/'.$openOpname->id
            );
        }

        $opnames = StockOpname::withCount(
            'details'
        )
        ->latest()
        ->paginate(10);
        // ->get();

        return view(
            'stock-opname.index',
            compact('opnames')
        );
    }

    private function generateOpnameNo()
    {
        $today = now()->format('Ymd');

        $last = StockOpname::whereDate(
            'created_at',
            today()
        )->count() + 1;

        return 'SO-' .
            $today .
            '-' .
            str_pad(
                $last,
                4,
                '0',
                STR_PAD_LEFT
            );
    }

    public function start()
    {
        $open = StockOpname::where(
            'status',
            'OPEN'
        )->first();

        if($open)
        {
            return redirect(
                '/stock-opname/'.$open->id
            );
        }

        $opname = StockOpname::create([

            'opname_no'   => $this->generateOpnameNo(),

            'opname_date' => now(),

            'user_name'   => 'Admin',

            'status'      => 'OPEN',

            'notes'       => null

        ]);

        return redirect(
            '/stock-opname/'.$opname->id
        );
    }

    public function show(
        StockOpname $stockOpname
    )
    {
        $details = $stockOpname
            ->details()
            ->with('product')
            ->get();

        return view(
            'stock-opname.show',
            compact(
                'stockOpname',
                'details'
            )
        );
    }

    public function store(
        Request $request,
        StockOpname $stockOpname
    )
    {
        $request->validate([

            'product_id' => 'required',

            'stok_fisik' => 'required|integer|min:0'

        ]);

        try{

            $detail = DB::transaction(function () use (

                $request,
                $stockOpname

            ){

                $product = Product::findOrFail(
                    $request->product_id
                );

                $stokSystem = $product->stok;

                $stokFisik = $request->stok_fisik;

                $selisih = $stokFisik - $stokSystem;

                /*
                |--------------------------------------------------------------------------
                | Barang sudah discan?
                |--------------------------------------------------------------------------
                */

                $exists = StockOpnameDetail::where(

                    'stock_opname_id',
                    $stockOpname->id

                )
                ->where(

                    'product_id',
                    $product->id

                )
                ->exists();

                if($exists){

                    return [

                        'success'=>false,

                        'message'=>'Barang sudah discan pada Stock Opname.'

                    ];

                }

                /*
                |--------------------------------------------------------------------------
                | Simpan Detail
                |--------------------------------------------------------------------------
                */

                $detail = StockOpnameDetail::create([

                    'stock_opname_id'=>$stockOpname->id,

                    'product_id'=>$product->id,

                    'stock_system'=>$stokSystem,

                    'stock_physical'=>$stokFisik,

                    'difference'=>$selisih,

                    'notes'=>$request->notes

                ]);

                /*
                |--------------------------------------------------------------------------
                | Update stok
                |--------------------------------------------------------------------------
                */

                $product->update([

                    'stok'=>$stokFisik

                ]);

                /*
                |--------------------------------------------------------------------------
                | Kartu stok
                |--------------------------------------------------------------------------
                */

                StockMovement::create([

                    'product_id'=>$product->id,

                    'type'=>'STOCK_OPNAME',

                    'qty'=>abs($selisih),

                    'stock_before'=>$stokSystem,

                    'stock_after'=>$stokFisik,

                    'reference_no'=>$stockOpname->opname_no,

                    'notes'=>$request->notes

                ]);

                return [

                    'success'=>true,

                    'message'=>'Item berhasil disimpan.',

                    'detail'=>[

                        'kode_barang'=>$product->kode_barang,

                        'nama_barang'=>$product->nama_barang,

                        'stock_system'=>$stokSystem,

                        'stock_physical'=>$stokFisik,

                        'difference'=>$selisih,

                        'notes'=>$request->notes

                    ]

                ];

            });

            return response()->json($detail);

        }

        catch(\Throwable $e){

            return response()->json([

                'success'=>false,

                'message'=>$e->getMessage()

            ],500);

        }

    }

    // finish
    public function finish(
    StockOpname $stockOpname
    )
    {
        // dd(
        //     $stockOpname->id,
        //     $stockOpname->status,
        //     $stockOpname->toArray()
        // );
        if($stockOpname->status!='OPEN')
        {
            return redirect('/stock-opname')

            ->with(

                'warning',

                'Stock Opname sudah diposting.'

            );
        }

        $stockOpname->update([

            'status'=>'POSTED',

            'finished_at'=>now()

        ]);

        return redirect('/stock-opname')

        ->with(

            'success',

            'Stock Opname berhasil diposting.'

        );
    }

    public function print(
        StockOpname $stockOpname
    )
    {
        $details = $stockOpname
            ->details()
            ->with('product')
            ->get();

        return view(
            'stock-opname.print',
            compact(
                'stockOpname',
                'details'
            )
        );
    }
}