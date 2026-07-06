@extends('layouts.app')

@section('title','Tambah Pelanggan')

@section('content')

<x-page-header
    title="Tambah Pelanggan"
    subtitle="Tambahkan Pelanggan baru"
>

    <x-slot:action>

        <a href="{{ route('customers.index') }}">

            <x-button color="gray">

                <i class="ri-arrow-left-line"></i>

                Kembali

            </x-button>

        </a>

    </x-slot:action>

</x-page-header>


<x-card>

<form
    method="POST"
    action="{{ route('customers.store') }}"
>

@csrf

<div class="grid grid-cols-2 gap-6">

    {{-- <x-input
        label="Kode Pelanggan"
        name="kode"
        icon="ri-barcode-line"
        required
        :value="old('kode')"
    /> --}}

    <x-input
        label="Nama Pelanggan"
        name="nama"
        icon="ri-user-3-line"
        required
        :value="old('nama')"
    />

    {{-- <x-input
        label="PIC"
        name="pic"
        icon="ri-user-3-line"
        :value="old('pic')"
    /> --}}

    <x-input
        label="Telepon"
        name="telepon"
        icon="ri-phone-line"
        :value="old('telepon')"
    />

    

    <x-input
        label="Email"
        name="email"
        type="email"
        icon="ri-mail-line"
        :value="old('email')"
    />

    <div class="flex items-center mt-8">

        <input
            id="is_member"
            type="checkbox"
            name="is_member"
            value="1"
            {{ old('is_member') ? 'checked' : '' }}
            class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
        >

        <label
            for="is_member"
            class="ml-2 text-sm text-slate-700"
        >
            Member
        </label>

    </div>

    <x-textarea
        label="Catatan"
        name="catatan"
        rows="3"
        placeholder="Catatan..."
    >
    {{ old('catatan') }}
    </x-textarea>

    <x-select
        label="Status"
        name="status"
    >
        <option value="1" selected>
            Aktif
        </option>

        <option value="0">
            Nonaktif
        </option>
    </x-select>

</div>

<div class="mt-6">

    <x-textarea
        label="Alamat"
        name="alamat"
        rows="4"
        placeholder="Alamat Pelanggan..."
    />

</div>

<div class="flex justify-end gap-3 mt-8">

    <a href="{{ route('customers.index') }}">

        <x-button color="secondary" type="button" full>
            <i class="ri-close-circle-line text-red-500 text-base"></i>
            Batal
        </x-button>

    </a>

    <x-button
        color="primary"
        type="submit"
    >

        <i class="ri-save-line"></i>

        Simpan Pelanggan

    </x-button>

</div>

</form>

</x-card>

@endsection