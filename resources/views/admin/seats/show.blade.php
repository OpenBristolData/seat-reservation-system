@extends('layouts.app')

@section('title', 'Seat Details')

@section('content')
<div class="container mx-auto px-4 py-6 m:px-6 lg:px-8">
    <div class="max-w-md mx-auto">
        <!-- Card Container -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
            <!-- Card Header -->
            <div class="bg-blue-200 px-6 py-4 border-b border-gray-200 text-center">
                <h2 class="text-xl font-semibold text-gray-800">Seat Details</h2>
            </div>
            
            <!-- Card Body -->
            <div class="p-6">
                <div class="mb-4 text-center">
                    <h3 class="text-lg font-medium text-gray-900">{{ $seat->seat_number }}</h3>
                </div>
                
                <div class="space-y-4">
                    <!-- Location -->
                    <div class="flex items-start">
                        <span class="text-gray-600 font-medium w-24">Location:</span>
                        <span class="text-gray-800">{{ $seat->location }}</span>
                    </div>
                    
                    <!-- Status -->
                    <div class="flex items-center">
                        <span class="text-gray-600 font-medium w-24">Status:</span>
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $seat->status === 'available' ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }} transition-colors duration-200">
                            {{ ucfirst($seat->status) }}
                        </span>
                    </div>
                    
                    <!-- Timestamps -->
                    <div class="flex items-start">
                        <span class="text-gray-600 font-medium w-24">Created:</span>
                        <span class="text-gray-800">{{ $seat->created_at->format('M j, Y H:i') }}</span>
                    </div>
                    
                    <div class="flex items-start">
                        <span class="text-gray-600 font-medium w-24">Updated:</span>
                        <span class="text-gray-800">{{ $seat->updated_at->format('M j, Y H:i') }}</span>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="mt-6 flex flex-col m:flex-row justify-between gap-3">
                    <a href="{{ route('admin.seats.edit', $seat) }}" class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-yellow-300 rounded-md shadow-m text-m font-medium text-yellow-700 bg-white hover:bg-yellow-50 hover:border-yellow-400 hover:text-yellow-900 transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        Edit
                    </a>
                    
                    <form action="{{ route('admin.seats.destroy', $seat) }}" method="POST" class="flex-1" id="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confirmDelete()" class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 rounded-md shadow-m text-m font-medium text-red-700 bg-white hover:bg-red-50 hover:border-red-400 hover:text-red-900 transition-all duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            Delete
                        </button>
                    </form>
                    
                    <a href="{{ route('admin.seats.index') }}" class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-m text-m font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 hover:text-gray-900 transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete() {
        Swal.fire({
            title: 'Confirm Seat Deletion',
            text: "This will permanently remove the seat.  ",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Delete Seat',
            cancelButtonText: 'Cancel',
            focusCancel: true,
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form').submit();
            }
        })
    }
</script>
@endsection