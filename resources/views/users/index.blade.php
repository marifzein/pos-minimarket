@extends('layouts.app')

@section('title','Manajemen User')

@section('content')

@if(session('success'))

<x-alert>

    {{ session('success') }}

</x-alert>

@endif

<x-page-header

    title="Manajemen User"

    subtitle="Kelola akun pengguna sistem"

>

    <x-slot:action>

        <a href="/users/create">

            <x-button>

                <i class="ri-user-add-line"></i>

                Tambah User

            </x-button>

        </a>

    </x-slot:action>

</x-page-header>

<x-card :padding="false">

    <x-table>

        <x-table-header>

            <tr>

                <x-table-head>

                    Nama

                </x-table-head>

                <x-table-head>

                    Email

                </x-table-head>

                <x-table-head class="text-center">

                    Role

                </x-table-head>

                <x-table-head class="text-center">

                    Dibuat

                </x-table-head>

                <x-table-head class="text-center">

                    Status

                </x-table-head>

                <x-table-head class="text-center">

                    Aksi

                </x-table-head>

            </tr>

        </x-table-header>

        <x-table-body>

        @forelse($users as $user)

            <x-table-row>

                <x-table-cell>

                    <div class="font-medium text-slate-800">

                        {{ $user->name }}

                    </div>

                </x-table-cell>

                <x-table-cell>

                    {{ $user->email }}

                </x-table-cell>

                <x-table-cell class="text-center">

                    @if($user->role=='Admin')

                        <x-badge color="red">

                            Admin

                        </x-badge>

                    @elseif($user->role=='Supervisor')

                        <x-badge color="yellow">

                            Supervisor

                        </x-badge>

                    @else

                        <x-badge color="green">

                            Kasir

                        </x-badge>

                    @endif

                </x-table-cell>

                <x-table-cell class="text-center">

                    {{ $user->created_at->format('d-m-Y') }}

                </x-table-cell>

                <x-table-cell class="text-center">

                    @if($user->is_active)

                        <x-badge color="green">

                            Aktif

                        </x-badge>

                    @else

                        <x-badge>

                            Nonaktif

                        </x-badge>

                    @endif

                </x-table-cell>

                <x-table-cell class="text-center">

                    <a href="/users/{{ $user->id }}/edit">

                        <x-button

                            color="blue"

                            size="sm"

                        >

                            <i class="ri-edit-line"></i>

                            Edit

                        </x-button>

                    </a>

                </x-table-cell>

            </x-table-row>

        @empty

            <tr>

                <td colspan="6">

                    <x-empty-state

                        title="Belum ada User"

                        description="Silakan tambahkan user pertama."

                    >

                        <a href="/users/create">

                            <x-button>

                                <i class="ri-user-add-line"></i>

                                Tambah User

                            </x-button>

                        </a>

                    </x-empty-state>

                </td>

            </tr>

        @endforelse

        </x-table-body>

    </x-table>

</x-card>

<div class="mt-6">

    {{ $users->links() }}

</div>

@endsection