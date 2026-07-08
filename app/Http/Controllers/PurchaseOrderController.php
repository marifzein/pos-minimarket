<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Helpers\DocumentNumber;
use Barryvdh\DomPDF\Facade\Pdf; // Jika menggunakan dompdf

class PurchaseOrderController extends Controller
{
    protected $fillable = [

        'po_number',

        'supplier_id',

        'po_date',

        'status',

        'total',

        'notes',

        'user_id'

    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /*
    |--------------------------------------------------------------------------
    | List Purchase Order
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $search = trim($request->search);

        $purchaseOrders = PurchaseOrder::with('supplier')

            ->when($search, function ($q) use ($search) {

                $q->where('po_number', 'like', "%{$search}%")

                  ->orWhereHas('supplier', function ($supplier) use ($search) {

                        $supplier->where(
                            'nama',
                            'like',
                            "%{$search}%"
                        );

                  });

            })

            ->latest()

            ->paginate(15)

            ->withQueryString();

        return view(
            'purchasing.index',
            compact('purchaseOrders')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Form Create
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)
            ->orderBy('nama')
            ->get();

        // pake helper  generator 
        // $poNumber = 'PO-'.date('Ymd-His');
        $poNumber = DocumentNumber::generate(
            'purchase_orders',
            'po_number',
            'PO'    
        );

        return view(
            'purchasing.create',
            compact(
                'suppliers',
                'poNumber'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Simpan
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([

            'po_number'   => 'required|unique:purchase_orders,po_number',
            'supplier_id' => 'required|exists:suppliers,id',
            'po_date'     => 'required|date',

            'product_id'  => 'required|array|min:1',
            'qty'         => 'required|array|min:1',
            'price'       => 'required|array|min:1',

        ]);

        DB::transaction(function () use ($request) {

            $status =
                $request->input('action') == 'ordered'
                ? 'ORDERED'
                : 'DRAFT';

            $po = PurchaseOrder::create([

                'po_number'   => $request->po_number,
                'supplier_id' => $request->supplier_id,
                'po_date'     => $request->po_date,
                'status'      => $status,
                'notes'       => $request->notes,
                'user_id'     => Auth::id(),
                'total'       => 0,

            ]);

            $grandTotal = 0;

            foreach ($request->product_id as $i => $productId) {

                $qty = (int)$request->qty[$i];

                $price = (int)$request->price[$i];

                $subtotal = $qty * $price;

                PurchaseOrderItem::create([

                    'purchase_order_id' => $po->id,

                    'product_id' => $productId,

                    'qty' => $qty,

                    'price' => $price,

                    'subtotal' => $subtotal,

                ]);

                
                $grandTotal += $subtotal;
            }

            $po->update([

                'total' => $grandTotal

            ]);

        });

        return redirect()

            ->route('purchasing.index')

            ->with(

                'success',

                'Purchase Order berhasil disimpan.'

            );
    }

    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit(PurchaseOrder $purchasing)
    {
        // KEAMANAN BACKEND: Jika status bukan DRAFT, tolak akses dan kembalikan dengan pesan error
        if ($purchasing->status !== 'DRAFT') {
            return redirect()->route('purchasing.index')
                ->with('error', 'Purchase Order yang sudah diproses tidak dapat diedit.');
        }

        $purchasing->load(

            'supplier',

            'items.product'

        );

        $suppliers = Supplier::where(
            'is_active',
            1
        )->orderBy('nama')->get();

        $cart = $purchasing->items->map(function ($item) {

                return [

                    'id'    => $item->product_id,
                    'name'  => $item->product->nama_barang,
                    'price' => $item->price,
                    'qty'   => $item->qty,

                ];

            })->values();

        return view(
            'purchasing.edit',
            [

                'po' => $purchasing,

                'suppliers' => $suppliers,

                'cart'      => $cart,


            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
    Request $request,
    PurchaseOrder $purchasing
    )
    {
        $request->validate([

            'supplier_id' => 'required|exists:suppliers,id',

            'po_date' => 'required|date',

            'product_id' => 'required|array|min:1',

            'qty' => 'required|array|min:1',

            'price' => 'required|array|min:1',

        ]);

        DB::transaction(function() use($request,$purchasing){

            $status =
                $request->action == 'ordered'
                ? 'ORDERED'
                : 'DRAFT';

            $purchasing->update([

                'supplier_id'=>$request->supplier_id,

                'po_date'=>$request->po_date,

                'status'=>$status,

                'notes'=>$request->notes,

            ]);

            /*
            |-----------------------------------------
            | hapus item lama
            |-----------------------------------------
            */

            $purchasing->items()->delete();

            $grandTotal = 0;

            foreach($request->product_id as $i=>$productId){

                $qty=(int)$request->qty[$i];

                $price=(int)$request->price[$i];

                $subtotal=$qty*$price;

                PurchaseOrderItem::create([

                    'purchase_order_id'=>$purchasing->id,

                    'product_id'=>$productId,

                    'qty'=>$qty,

                    'price'=>$price,

                    'subtotal'=>$subtotal,

                ]);

                $grandTotal += $subtotal;

            }

            $purchasing->update([

                'total'=>$grandTotal

            ]);

        });

        return redirect()

            ->route('purchasing.index')

            ->with(

                'success',

                $request->action=='ordered'
                ? 'Purchase Order berhasil diposting.'
                : 'Draft Purchase Order berhasil diperbarui.'

            );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function destroy(PurchaseOrder $purchasing)
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | View Detail (Readonly)
    |--------------------------------------------------------------------------
    */
    public function show(PurchaseOrder $purchasing)
    {
        // Load relasi supplier dan item produk
        $purchasing->load(['supplier', 'items.product']);

        return view('purchasing.show', [
            'po' => $purchasing
        ]);
    }

    

    /*
    |--------------------------------------------------------------------------
    | Cetak PDF Purchase Order
    |--------------------------------------------------------------------------
    */
    public function printPdf(PurchaseOrder $purchasing)
    {
        // Keamanan Tingkat Menengah: Hanya status ORDERED & RECEIVED yang boleh di-print
        if (!in_array($purchasing->status, ['ORDERED', 'RECEIVED'])) {
            return redirect()->route('purchasing.index')
                ->with('error', 'Cetak gagal! Dokumen Purchase Order harus berstatus ORDERED.');
        }

        // Load data relasi lengkap
        $purchasing->load(['supplier', 'items.product', 'user']);

        // Render file view khusus cetak ke dalam format PDFStream
        $pdf = Pdf::loadView('purchasing.print', [
            'po' => $purchasing
        ])->setPaper('a4', 'portrait');

        // Download / Stream PDF ke browser dengan nama file yang rapi
        return $pdf->stream("PO-{$purchasing->po_number}.pdf");
    }
}