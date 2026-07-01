@props([

    'color' => 'primary',

    'size' => 'md',

    'type' => 'button'

])

@php

$colors = [

    'primary' =>
    'bg-indigo-600 hover:bg-indigo-700 text-white',

    'blue' =>
    'bg-blue-500 hover:bg-blue-700 text-white',

    'green' =>
    'bg-emerald-600 hover:bg-emerald-700 text-white',

    'red' =>
    'bg-red-600 hover:bg-red-700 text-white',

    'warning' =>
    'bg-red-500 hover:bg-red-600 text-white',

    'gray' =>
    'bg-slate-100 hover:bg-slate-200 text-slate-700',

    'orange' =>
    'bg-orange-500 hover:bg-orange-600 text-white',


];

$sizes = [

    'sm' => 'px-3 py-1.5 text-sm',

    'md' => 'px-4 py-2',

    'lg' => 'px-6 py-3 text-lg',

];

@endphp

<button

    type="{{ $type }}"

    {{ $attributes->merge([

        'class'=>

        'inline-flex items-center justify-center gap-2
        rounded-xl font-medium transition duration-200
        focus:outline-none focus:ring-2
        focus:ring-indigo-400 disabled:opacity-50 disabled:cursor-not-allowed
        '

        .$colors[$color].' '

        .$sizes[$size]

    ]) }}

>

    {{ $slot }}

</button> 