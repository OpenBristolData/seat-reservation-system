@extends('layouts.app')

@section('title', 'Manage Seats')

@section('content')
<div class="container mx-auto px-4 py-6 m:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="bg-green-50 shadow rounded-lg p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Seat Management</h1>
                <p class="text-gray-600 mt-1">Manage all seating arrangements and locations</p>
            </div>
            <a href="{{ route('admin.seats.create') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition duration-150 ease-in-out shadow-m">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                </svg>
                Add New Seat
            </a>
        </div>
    </div>

    <!-- Seats Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-200">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-m font-medium text-gray-500 uppercase tracking-wider">Seat Number</th>
                        <th scope="col" class="px-6 py-3 text-left text-m font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th scope="col" class="px-6 py-3 text-left text-m font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-center text-m font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($seats as $seat)
                    <tr class="hover:bg-blue-100">
                        <td class="px-6 py-4 whitespace-nowrap text-m font-medium text-gray-900">
                            {{ $seat->seat_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-m text-gray-500">
                            {{ $seat->location }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-m leading-5 font-semibold rounded-full {{ $seat->status === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($seat->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex justify-center space-x-4">
                                <a href="{{ route('admin.seats.show', $seat) }}" class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md shadow-m text-m font-medium text-gray-700 bg-white hover:bg-gray-100 hover:shadow-md transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                    </svg>
                                    View
                                </a>
                                <a href="{{ route('admin.seats.edit', $seat) }}" class="inline-flex items-center px-3 py-1 border border-yellow-300 rounded-md shadow-m text-m font-medium text-yellow-700 bg-white hover:bg-yellow-100 hover:shadow-md transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                    Edit
                                </a>
                                <form action="{{ route('admin.seats.destroy', $seat) }}" method="POST" class="inline-block" id="delete-form-{{ $seat->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete(event, 'delete-form-{{ $seat->id }}')" class="inline-flex items-center px-3 py-1 border border-red-300 rounded-md shadow-m text-m font-medium text-red-700 bg-white hover:bg-red-100 hover:shadow-md transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-m text-gray-500">
                            No seats found in the system.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        
    </div>
</div>
  {{ $seats->links('vendor.pagination.tailwind') }}
@push('scripts')
<!-- Include SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(event, formId) {
    event.preventDefault();
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
}
</script>
@endpush
@endsection