@extends('layouts.app')

@section('title','Edit Pelanggan')

@section('content')

<x-page-header
    title="Edit Pelanggan"
    subtitle="Perbarui data Pelanggan"
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
    action="{{ route('customers.update',$customer) }}"
>

@csrf
@method('PUT')

<div class="grid grid-cols-2 gap-6">

    {{-- <x-input
        label="Kode Pelanggan"
        name="kode"
        icon="ri-barcode-line"
        required
        :value="old('kode',$customer->kode)"
    /> --}}

    <x-input
        label="Nama Pelanggan"
        name="nama"
        icon="ri-truck-line"
        required
        :value="old('nama',$customer->nama)"
    />

   

    <x-input
        label="Telepon"
        name="telepon"
        icon="ri-phone-line"
        :value="old('telepon',$customer->telepon)"
    />

    <x-input
        label="Email"
        name="email"
        type="email"
        icon="ri-mail-line"
        :value="old('email',$customer->email)"
    />

    <div class="flex items-center mt-8">

        <input
            id="is_member"
            type="checkbox"
            name="is_member"
            value="1"
            {{ old('is_member',$customer->is_member) ? 'checked' : '' }}
            class="h-4 w-4 rounded border-slate-300 text-indigo-600"
        >

        <label
            for="is_member"
            class="ml-2 text-sm"
        >
            Member
        </label>

    </div>

    <x-select
        label="Status"
        name="status"
    >

        <option
            value="1"
            {{ old('status',$customer->status)==1 ? 'selected' : '' }}
        >
            Aktif
        </option>

        <option
            value="0"
            {{ old('status',$customer->status)==0 ? 'selected' : '' }}
        >
            Nonaktif
        </option>

    </x-select>

    <x-input
        label="Catatan"
        name="catatan"
        icon="ri-phone-line"
        :value="old('catatan',$customer->catatan)"
    />

</div>

<div class="mt-6">

    <x-textarea
        label="Alamat"
        name="alamat"
        rows="4"
    >{{ old('alamat',$customer->alamat) }}</x-textarea>

</div>

<div class="flex justify-end gap-3 mt-8">

    <a href="{{ route('customers.index') }}">

        <x-button color="gray" type="button">

            <i class="ri-close-line"></i>

            Batal

        </x-button>

    </a>

    <x-button
        color="primary"
        type="submit"
    >

        <i class="ri-save-line"></i>

        Simpan Perubahan

    </x-button>

</div>

</form>

</x-card>

@endsection