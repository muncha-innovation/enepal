@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6 space-y-6">
    <!-- Notification Header -->
    <div class="bg-white shadow rounded-xl p-4 flex items-start justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">{{ $notification->title }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $notification->content }}</p>
        </div>
        <a href="{{ route('business.communications.index', ['business' => $business, 'type' => 'notifications']) }}"
           class="text-sm text-indigo-600 hover:underline font-medium">← Back</a>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-3 gap-4">
        <x-stat-card label="Total Sent" :value="$totalUsers" color="blue" />
        <x-stat-card label="Opened" :value="$opened" color="green" />
        <x-stat-card label="Unopened" :value="$unopened" color="red" />
    </div>

    <!-- Chart & Users Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-md font-semibold text-gray-800">Reads Over Time</h3>
                <select id="chartSelector" class="text-sm border-gray-300 rounded-md">
                    <option value="daily" selected>Daily</option>
                    <option value="weekly">Weekly</option>
                </select>
            </div>
            <canvas id="readsChart" height="250"></canvas>
        </div>

        <div class="bg-white rounded-xl shadow p-4">
            <h3 class="text-md font-semibold text-gray-800 mb-2">User Delivery Details</h3>
            <table class="w-full text-sm text-left text-gray-600">
                <thead>
                    <tr class="border-b">
                        <th class="px-2 py-1">User</th>
                        <th class="px-2 py-1">Email</th>
                        <th class="px-2 py-1">Read At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-2 py-1">{{ $user->name }}</td>
                            <td class="px-2 py-1">{{ $user->email }}</td>
                            <td class="px-2 py-1">
                                @if ($user->pivot->read_at)
                                    <span class="text-green-700">{{ \Carbon\Carbon::parse($user->pivot->read_at)->diffForHumans() }}</span>
                                @else
                                    <span class="text-red-600">Not Read</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const dailyLabels = {!! json_encode($dailyStats->pluck('date')) !!};
    const dailyData = {!! json_encode($dailyStats->pluck('total')) !!}; // already % from controller

    const weeklyLabels = {!! json_encode($weeklyStats->pluck('label')) !!};
    const weeklyData = {!! json_encode($weeklyStats->pluck('total')) !!}; // already % from controller

    const ctx = document.getElementById('readsChart').getContext('2d');
    let currentChart;

    function createChart(type) {
        const labels = type === 'weekly' ? weeklyLabels : dailyLabels;
        const data = type === 'weekly' ? weeklyData : dailyData;
        const color = type === 'weekly' ? '#10B981' : '#6366F1';

        if (currentChart) currentChart.destroy();

        currentChart = new Chart(ctx, {
            type: type === 'weekly' ? 'line' : 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: type === 'weekly' ? 'Weekly Read Rate (%)' : 'Daily Read Rate (%)',
                    data: data,
                    backgroundColor: type === 'weekly' ? '#D1FAE5' : color,
                    borderColor: color,
                    fill: type === 'weekly',
                    tension: 0.4,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
    }

    document.getElementById('chartSelector').addEventListener('change', function () {
        createChart(this.value);
    });

    createChart('daily'); // Initial render
</script>
@endpush

