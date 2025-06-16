@props(['label', 'value', 'color'])

<div class="bg-{{ $color }}-100 rounded-xl p-4 text-center shadow">
    <div class="text-{{ $color }}-700 text-sm font-semibold">{{ $label }}</div>
    <div class="text-2xl text-{{ $color }}-900 font-bold mt-1">{{ $value }}</div>
</div>