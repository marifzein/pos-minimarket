<?php

namespace App\Http\Controllers;

use App\Helpers\DocumentNumber;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $customers = Customer::when($search, function ($q) use ($search) {

            $q->where('nama', 'like', "%{$search}%")
              ->orWhere('kode_pelanggan', 'like', "%{$search}%")
              ->orWhere('telepon', 'like', "%{$search}%");

        })
        ->latest()
        ->paginate(10);

        return view('customers.index', compact(
            'customers',
            'search'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'nama'       => 'required|max:150',
            'telepon'    => 'nullable|max:30',
            'alamat'     => 'nullable',
            'email'      => 'nullable|email',
            'catatan'    => 'nullable',
            'is_member'  => 'nullable',
            'status'     => 'required',

        ]);

        $validated['kode_pelanggan'] =
            DocumentNumber::generateMaster(
                'customers',
                'kode_pelanggan',
                'CUST'
            );

        $validated['is_member'] =
            $request->boolean('is_member');

        Customer::create($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([

            'nama'       => 'required|max:150',
            'telepon'    => 'nullable|max:30',
            'alamat'     => 'nullable',
            'email'      => 'nullable|email',
            'catatan'    => 'nullable',
            'is_member'  => 'nullable',
            'status'     => 'required',

        ]);

        $validated['is_member'] =
            $request->boolean('is_member');

        $customer->update($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Pelanggan berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Pelanggan berhasil dihapus.');
    }
}