@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="container mx-auto px-4 py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="flex justify-between items-center bg-green-50 rounded-xl p-6 mb-8">
        <div>
            <h1 class="text-2xl font-medium text-gray-800">Reservation Reports</h1>
            <p class="text-gray-600">Analyze seat usage patterns</p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Filter Reports</h3>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('admin.reports') }}">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-5">
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                               id="start_date" name="start_date" value="{{ $startDate }}">
                    </div>
                    <div class="md:col-span-5">
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                               id="end_date" name="end_date" value="{{ $endDate }}">
                    </div>
                    <div class="md:col-span-2 flex items-end space-x-2">
                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md shadow-sm">
                            Filter
                        </button>
                        @if(request()->has('start_date') || request()->has('end_date'))
                            <a href="{{ route('admin.reports') }}" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-center">
                                Clear
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Reservations by Date Chart -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Reservations by Date</h3>
            </div>
            <div class="p-4">
                <canvas id="reservationsChart" height="250"></canvas>
            </div>
        </div>

        <!-- Top Users Card -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Top Users</h3>
            </div>
            <div class="p-4">
                <ul class="divide-y divide-gray-200">
                    @foreach($reservationsByUser as $user => $count)
                    <li class="py-3 flex justify-between items-center">
                        <span class="text-gray-800">{{ $user }}</span>
                        <span class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">
                            {{ $count }} {{ Str::plural('reservation', $count) }}
                        </span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Reservation Details -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Reservation Details</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Intern</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seat</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reservations as $reservation)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('D, M j, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $reservation->user->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $reservation->seat->seat_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $reservation->seat->location }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $reservation->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($reservation->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                            No reservations found for the selected period.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Reservations by Date Chart
    const ctx = document.getElementById('reservationsChart').getContext('2d');
    const reservationsData = @json($reservationsByDate);
    
    // Format the dates to remove time portion
    const formattedLabels = Object.keys(reservationsData).map(dateString => {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { 
            weekday: 'short', 
            month: 'short', 
            day: 'numeric',
            year: 'numeric'
        });
        // Alternative format: 'MMM D, YYYY'
        // return new Date(dateString).toISOString().split('T')[0]; // YYYY-MM-DD format
    });
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: formattedLabels, // Use the formatted dates
            datasets: [{
                label: 'Reservations',
                data: Object.values(reservationsData),
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                },
                x: {
                    ticks: {
                        autoSkip: true,
                        maxRotation: 45,
                        minRotation: 45
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
});
</script>
@endpush
@endsection