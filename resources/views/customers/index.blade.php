@extends('layouts.app')

@section('title','Master Pelanggan')

@section('content')

<x-page-header

title="Master Pelanggan"

subtitle="Kelola data Pelanggan"

>

<x-slot:action>

<a href="{{ route('customers.create') }}">

<x-button color="primary">

<i class="ri-add-line"></i>

Tambah Pelanggan

</x-button>

</a>

</x-slot:action>

</x-page-header>

<x-card>

<form
method="GET"
class="mb-6"
>

<div class="flex gap-3">

<x-input

name="search"

placeholder="Cari Pelanggan..."

:value="request('search')"

/>

<x-button>

Cari

</x-button>

</div>

</form>

<x-table>

<x-table-header>

<tr>



<x-table-head class="text-left">Nama</x-table-head>

<x-table-head class="text-left">Alamat</x-table-head>

<x-table-head class="text-left">Telepon</x-table-head>

<x-table-head>Member</x-table-head>

<x-table-head>Status</x-table-head>

<x-table-head class="text-center">

Aksi

</x-table-head>

</tr>

</x-table-header>

<tbody>

@forelse($customers as $customer)

<tr>



<x-table-cell class="text-left">

{{ $customer->nama }}

</x-table-cell>

<x-table-cell class="max-w-xs truncate text-left">
    {{ $customer->alamat ?: '-' }}
</x-table-cell>

<x-table-cell class="text-left">

{{ $customer->telepon  }}

</x-table-cell>

<x-table-cell class="text-center">

{{ $customer->is_member ? 'Member' : '-' }}

</x-table-cell>

<x-table-cell class="text-center">

@if($customer->status)
    <x-badge color="green">
        Aktif
    </x-badge>
@else
    <x-badge color="red">
        Nonaktif
    </x-badge>
@endif

</x-table-cell>

<x-table-cell>

<div class="flex justify-center">

<a href="{{ route('customers.edit',$customer) }}">

<x-button
size="sm"
color="blue"
>

<i class="ri-edit-line"></i>

</x-button>

</a>

</div>

</x-table-cell>

</tr>

@empty

<tr>

<td colspan="6">

<x-empty-state

icon="ri-user-3-line"

title="Belum ada Pelanggan"

description="Klik Tambah Pelanggan."

/>

</td>

</tr>

@endforelse

</tbody>

</x-table>

<div class="mt-6">

{{ $customers->links() }}

</div>

</x-card>

@endsection