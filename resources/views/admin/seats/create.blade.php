@extends('layouts.app')

@section('title', 'Add New Seat')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Add New Seat</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.seats.store') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="seat_number" class="form-label">Seat Number</label>
                        <input type="text" class="form-control" id="seat_number" name="seat_number" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="available">Available</option>
                            <option value="unavailable">Unavailable</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Save Seat</button>
                    <a href="{{ route('admin.seats.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection