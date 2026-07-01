@props([

'label'=>'',

'name',

'rows'=>4,

'placeholder'=>''

])

<div class="space-y-2">

@if($label)

<label class="block text-sm font-semibold text-slate-700">

{{ $label }}

</label>

@endif

<textarea

name="{{ $name }}"

rows="{{ $rows }}"

placeholder="{{ $placeholder }}"

{{ $attributes->merge([

'class'=>

'w-full

rounded-xl

border

border-slate-300

hover:border-slate-400

px-4

py-3

focus:border-indigo-500

focus:ring-4

focus:ring-indigo-100

outline-none

transition'

]) }}

>{{ old($name) }}</textarea>

@error($name)

<p class="text-red-500 text-sm">

{{ $message }}

</p>

@enderror

</div>