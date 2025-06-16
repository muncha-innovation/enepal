@props(['title', 'chartId'])

<div class="bg-white p-4 rounded-xl shadow">
    <h4 class="text-sm font-semibold text-gray-700 mb-2">{{ $title }}</h4>
    <canvas id="{{ $chartId }}" height="140"></canvas>
</div>