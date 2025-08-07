@extends('layouts.app')

@section('title', 'Edit Seat')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
             <div class="bg-blue-200 px-6 py-4 border-b border-gray-200 text-center">
                <h2 class="text-xl font-semibold text-gray-800">Seat Details</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.seats.update', $seat) }}" id="edit-seat-form">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="seat_number" class="form-label">Seat Number</label>
                        <input type="text" class="form-control" id="seat_number" name="seat_number" value="{{ $seat->seat_number }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" value="{{ $seat->location }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="available" {{ $seat->status === 'available' ? 'selected' : '' }}>Available</option>
                            <option value="unavailable" {{ $seat->status === 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                        </select>
                    </div>
                    
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.seats.index') }}" class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-gray-100 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 hover:text-gray-900 transition-all duration-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
        </svg>
        Back to List
    </a>
    <button type="button" onclick="confirmEdit()" class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 hover:border-blue-800 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
        </svg>
        Update Seat
    </button>
    
    
</div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Include SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmEdit() {
    Swal.fire({
        title: 'Update Seat?',
        text: "Are you sure you want to update this seat information?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, update it!'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('edit-seat-form').submit();
        }
    });
}
</script>
@endpush
@endsection