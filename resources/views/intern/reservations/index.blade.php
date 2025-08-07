@extends('layouts.app')

@section('title', 'My Reservations')

@section('content')
<div class="container mx-auto px-4 py-6 sm:px-6 lg:px-8">
   <div class="bg-green-50 rounded-xl p-6 mb-8 relative overflow-hidden">
    <!-- Left decorative rectangle with user icon -->
    <div class="absolute left-6 top-1/2 -translate-y-1/2 w-16 h-16 bg-green-100 rounded-lg flex items-center justify-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
        </svg>
    </div>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 relative z-10 pl-24">
        <div>
             <h1 class="text-2xl font-bold text-blue-900">Manage My Reservations</h1>
                <p class="text-gray-600 mt-1">{{ explode(' ', auth()->user()->name)[0] }} Seats are like cookies - they disappear fast</p>
        </div>
        <a href="{{ route('reservations.create') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-full transition duration-150 ease-in-out shadow-sm hover:shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
            </svg>
            New Reservation
        </a>
    </div>
</div>

    <!-- Reservations Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-200">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-m font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-m font-medium text-gray-500 uppercase tracking-wider">Seat</th>
                        <th scope="col" class="px-6 py-3 text-left text-m font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th scope="col" class="px-6 py-3 text-left text-m font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-right text-m font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                   @forelse($reservations as $reservation)
    @php
        $reservationDate = \Carbon\Carbon::parse($reservation->reservation_date);
        $now = \Carbon\Carbon::now();
        
        // Check if it's the same day
        $isToday = $reservationDate->isSameDay($now);
        
        // Check if it's past 4 PM on the reservation day
        $isPassed = $isToday 
            ? ($now->hour >= 16)  // 16 = 4 PM in 24-hour format
            : $reservationDate->isPast();
        
        $status = $reservation->status === 'active' 
            ? ($isToday && $now->hour < 16 ? 'actively ongoing' : ($isPassed ? 'passed' : 'active'))
            : $reservation->status;
    @endphp
                    <tr class="hover:bg-blue-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-m font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('D, M j, Y') }}
                            </div>
                            @if($isPassed && $status !== 'cancelled')
                                <div class="text-m text-gray-500">(Completed)</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-m leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ $reservation->seat->seat_number }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-m text-gray-500">
                            {{ $reservation->seat->location }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusClasses = [
                                    'actively ongoing' => 'bg-blue-100 text-blue-800',
                                    'active' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                    'passed' => 'bg-blue-100 text-blue+
                                    -800'
                                ];
                            @endphp
                            <span class="px-2 inline-flex text-m leading-5 font-semibold rounded-full {{ $statusClasses[$status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            @if($status === 'actively ongoing' || $status === 'active')
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
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-m text-gray-500">
                            No reservations found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    
{{ $reservations->links('vendor.pagination.tailwind') }}
    
</div>

<!-- SweetAlert Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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