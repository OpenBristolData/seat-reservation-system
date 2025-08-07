@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container mx-auto px-4 py-6 sm:px-6 lg:px-8">
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Left Column - Charts and Calendar Cards -->
        <div class="lg:w-2/3 space-y-6">
            <!-- Header -->
            
              <div class="bg-green-50 shadow rounded-lg p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Admin Dashboard</h1>
                <p class="text-gray-600 mt-1">Seat reservation overview</p>
            </div>
        
        </div>
    </div>
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50 rounded-lg p-4 shadow-sm border border-blue-100">
                    <h3 class="text-sm font-medium text-blue-800">Total Seats</h3>
                    <p class="text-3xl font-bold text-blue-600">{{ $totalSeats }}</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4 shadow-sm border border-green-100">
                    <h3 class="text-sm font-medium text-green-800">Available Today</h3>
                    <p class="text-3xl font-bold text-green-600">{{ $availableSeats }}</p>
                </div>
                <div class="bg-red-50 rounded-lg p-4 shadow-sm border border-red-100">
                    <h3 class="text-sm font-medium text-red-800">Booked Today</h3>
                    <p class="text-3xl font-bold text-red-600">{{ $bookedSeats }}</p>
                </div>
            </div>

            <!-- Today's Reservations Pie Chart -->
            <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Today's Seat Distribution</h3>
                <div class="h-64">
                    <canvas id="todayPieChart"></canvas>
                </div>
            </div>

            <!-- Compact Calendar Progress Cards -->
            <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                <h3 class="text-lg font-medium text-gray-800 mb-3">Upcoming Reservations</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-2">
                    @foreach($upcomingDays as $day)
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-700 mb-1">
                            {{ $day['day_name'] }}<br>
                            <span class="text-xs text-gray-500">{{ $day['date_display'] }}</span>
                        </div>
                        <div class="text-lg font-bold">
                            {{ $day['booked'] }}/{{ $day['total_seats'] }}
                        </div>
                      <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
    <div class="bg-{{ $day['booked']/$day['total_seats'] > 0.8 ? 'red' : 'green' }}-500 h-1.5 rounded-full" 
         style="width: {{ min(100, ($day['booked']/$day['total_seats'])*100) }}%">
    </div>
</div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Upcoming Reservations Bar Chart -->
            <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Weekly Reservation Trend</h3>
                <div class="h-64">
                    <canvas id="upcomingBarChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Right Column - Today's Reservations -->
        <div class="lg:w-1/3">
            <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200 sticky top-4">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Today's Reservations ({{ now()->format('D, M j, Y') }})</h3>
                
                @if($reservations->isEmpty())
                <div class="bg-blue-50 text-blue-800 p-3 rounded-lg text-sm">
                    No reservations for today.
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-blue-200">
                            <tr>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Intern</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seat</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($reservations as $reservation)
                            <tr class="hover:bg-blue-100">
                                <td class="px-3 py-2 whitespace-nowrap text-gray-900">{{ $reservation->user->name }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-gray-500">{{ $reservation->seat->seat_number }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-gray-500">{{ $reservation->seat->location }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Auto-refresh at midnight -->
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Today's Pie Chart
    const todayCtx = document.getElementById('todayPieChart').getContext('2d');
    new Chart(todayCtx, {
        type: 'pie',
        data: {
            labels: ['Available', 'Booked'],
            datasets: [{
                data: [{{ $availableSeats }}, {{ $bookedSeats }}],
                backgroundColor: [
                    '#10B981', // green-500
                    '#EF4444'  // red-500
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Upcoming Bar Chart
    const upcomingCtx = document.getElementById('upcomingBarChart').getContext('2d');
    new Chart(upcomingCtx, {
        type: 'bar',
        data: {
            labels: @json($upcomingDays->pluck('day_name')),
            datasets: [{
                label: 'Booked Seats',
                data: @json($upcomingDays->pluck('booked')),
                backgroundColor: @json($upcomingDays->map(function($day) {
                    return $day['booked']/$day['total_seats'] > 0.8 ? '#EF4444' : '#10B981';
                })),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: {{ $totalSeats }},
                    title: {
                        display: true,
                        text: 'Number of Seats'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Calculate milliseconds until midnight
    const now = new Date();
    const midnight = new Date(
        now.getFullYear(),
        now.getMonth(),
        now.getDate() + 1, // Next day
        0, 0, 0 // Midnight
    );
    const msUntilMidnight = midnight - now;
    
    // Refresh page at midnight
    setTimeout(function() {
        window.location.reload();
    }, msUntilMidnight);
});
</script>
@endpush
@endsection