@extends('layouts.app')

@section('title','Developer')

@section('content')

<div class="max-w-4xl mx-auto">

    <x-card>

        <h2
            class="text-2xl font-bold mb-8"
        >

            Developer Tools

        </h2>

        <div
            class="grid grid-cols-1 md:grid-cols-3 gap-5"
        >

            {{-- Reset Transaksi --}}

            <form
                action="{{ route('developer.reset.transaksi') }}"
                method="POST"
            >

                @csrf

                <button
                    onclick="return confirm('Reset semua transaksi ?')"
                    class="w-full rounded-xl bg-red-500 text-white py-5 hover:bg-red-600"
                >

                    Reset Transaksi

                </button>

            </form>

            {{-- Reset Master --}}

            <form
                action="{{ route('developer.reset.master') }}"
                method="POST"
            >

                @csrf

                <button
                    onclick="return confirm('Reset seluruh master ?')"
                    class="w-full rounded-xl bg-orange-500 text-white py-5 hover:bg-orange-600"
                >

                    Reset Master

                </button>

            </form>

            {{-- Seed --}}

            <form
                action="{{ route('developer.seed') }}"
                method="POST"
            >

                @csrf

                <button
                    onclick="return confirm('Generate demo data ?')"
                    class="w-full rounded-xl bg-indigo-600 text-white py-5 hover:bg-indigo-700"
                >   

                    Seed Demo Data

                </button>

            </form>

        </div>

    </x-card>

</div>

@endsection