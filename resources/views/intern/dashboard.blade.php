@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto px-4 py-6 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="bg-green-50 rounded-xl p-6 mb-8 relative overflow-hidden">
        <!-- Left decorative rectangle -->
        <div class="absolute left-4 top-1/2 -translate-y-1/2 w-16 h-16 bg-green-200 rounded-lg flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
            </svg>
        </div>

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 relative z-10 pl-20 pr-10">
            <div class="ml-8"> <!-- Added ml-8 for additional right shift -->
                <h1 class="text-2xl font-bold text-blue-900">Welcome, {{ auth()->user()->name }}!</h1>
                <p class="text-gray-600 mt-1">Hey reserve your spot before someone else's does</p>
            </div>
            <a href="{{ route('reservations.create') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-full transition duration-150 ease-in-out shadow-sm hover:shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                </svg>
                Book a Seat
            </a>
        </div>
    </div>
</div>

    @php
        $now = now();
        $currentTime = $now->format('H:i');
        $hideCurrentDate = $currentTime >= '16:00'; // Hide current date after 4PM
    @endphp

    @if($reservations->isEmpty())
 <div class="bg-blue-400 text-white p-4 rounded-lg flex items-center">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd" />
    </svg>
    <span class="mr-3">You don't have any upcoming reservations.</span>
    <a href="{{ route('reservations.create') }}" class="inline-flex items-center px-3 py-1 bg-white text-blue-600 font-medium rounded-full text-m shadow-sm hover:bg-gray-100 transition duration-150 ease-in-out">
        Book a seat now
    </a>
</div>
    @else
    <!-- Reservations Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg  text-center font-medium text-blue-800 ">Your Upcoming Reservations</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-blue-200">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-m font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-m font-medium text-gray-500 uppercase tracking-wider">Seat Number</th>
                        <th scope="col" class="px-6 py-3 text-left text-m font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th scope="col" class="px-6 py-3 text-left text-m font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-right text-m font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($reservations as $reservation)
                        @php
                            $reservationDate = \Carbon\Carbon::parse($reservation->reservation_date);
                            $isToday = $reservationDate->isToday();
                            
                            // Skip current date if it's after 4PM
                            if ($hideCurrentDate && $isToday) {
                                continue;
                            }
                        @endphp
                        <tr class="hover:bg-blue-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-m font-medium text-gray-900">{{ $reservationDate->format('D, M j, Y') }}</div>
                                    @if($isToday)
                                        <span class="ml-2 px-2 inline-flex text-m leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Today</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-m leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ $reservation->seat->seat_number }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-m text-gray-500">{{ $reservation->seat->location }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-m leading-5 font-semibold rounded-full {{ $reservation->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($reservation->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-m font-medium">
                                @if($reservation->status === 'active')
                                <form action="{{ route('reservations.destroy', $reservation) }}" method="POST" class="inline-block" id="cancel-form-{{ $reservation->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmCancel('{{ $reservation->id }}')" class="text-red-600 hover:text-red-900 inline-flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        Cancel
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

 
<script>
    function confirmCancel(reservationId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, cancel it!',
            cancelButtonText: 'No, keep it'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`cancel-form-${reservationId}`).submit();
            }
        })
    }
</script>
@endsection