@extends('layouts.app')

@section('title', 'Book a Seat')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-blue-200 text-center">Book a Seat</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('reservations.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label">Select Date</label>
                            <div class="date-scroller">
                                @foreach($availableDates as $date)
                                    @php
                                        $isToday = $date->isToday();
                                        $isDisabled = ($isToday && $currentTime >= '16:00') || 
                                                     ($isToday && $currentTime >= '07:30');
                                    @endphp
                                    <div class="date-option {{ $isDisabled ? 'disabled' : '' }}"
                                         data-date="{{ $date->format('Y-m-d') }}"
                                         title="{{ $isDisabled ? ($isToday && $currentTime >= '16:00' ? 'Today\'s bookings closed after 4:00 PM' : 'Same-day bookings must be made before 7:30 AM') : '' }}">
                                        <div class="day">{{ $date->format('D') }}</div>
                                        <div class="number">{{ $date->format('j') }}</div>
                                        @if($date->isToday())
                                            <div class="today-label">Today</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" name="reservation_date" id="reservation_date" required>
                            @error('reservation_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="seat_id" class="form-label">Available Seats</label>
                            <select class="form-select @error('seat_id') is-invalid @enderror" 
                                    id="seat_id" 
                                    name="seat_id" 
                                    required
                                    disabled>
                                <option value="">Select a date first</option>
                            </select>
                            @error('seat_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Book Seat</button>
                            <a href="{{ route('reservations.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                    <hr class="my-4">
<h5 class="text-center mb-3">Select Date to view Availability</h5>
<div id="seat-grid" class="grid grid-cols-10 gap-2 justify-items-center text-sm"></div>

<div class="mt-3 text-center">
    <span class="badge bg-success me-2">Available</span>
    <span class="badge bg-danger me-2">Unavailable</span>
    <span class="badge bg-primary">Selected</span>
</div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    #seat-grid .seat-box {
    width: 35px;
    height: 35px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: bold;
    color: white;
}
.seat-available {
    background-color: #28a745; /* Green */
}
.seat-unavailable {
    background-color: #dc3545; /* Red */
}
.seat-selected {
    background-color: #0d6efd; /* Blue */
}

.date-scroller {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    padding: 10px 0;
    margin-bottom: 15px;
}
.date-option {
    text-align: center;
    padding: 10px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    min-width: 60px;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
}
.date-option:hover:not(.disabled) {
    background-color: #f8f9fa;
    border-color: #adb5bd;
}
.date-option.active {
    background-color: #0d6efd;
    color: white;
    border-color: #0d6efd;
}
.date-option.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background-color: #f8f9fa;
}
.day {
    font-size: 0.8rem;
    font-weight: bold;
    text-transform: uppercase;
}
.number {
    font-size: 1.2rem;
    font-weight: bold;
}
.today-label {
    font-size: 0.6rem;
    position: absolute;
    top: -8px;
    right: -5px;
    background: #0d6efd;
    color: white;
    padding: 2px 5px;
    border-radius: 10px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dateOptions = document.querySelectorAll('.date-option:not(.disabled)');
    const dateInput = document.getElementById('reservation_date');
    const seatSelect = document.getElementById('seat_id');
    const seatGrid = document.getElementById('seat-grid');

    // Handle date selection
    dateOptions.forEach(option => {
        option.addEventListener('click', function () {
            document.querySelectorAll('.date-option').forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
            dateInput.value = this.dataset.date;
            loadAvailableSeats(this.dataset.date);
        });
    });

    // Load seats if a date is already selected
    const activeOption = document.querySelector('.date-option.active');
    if (activeOption) {
        loadAvailableSeats(activeOption.dataset.date);
    }

    function loadAvailableSeats(date) {
        seatSelect.disabled = true;
        seatSelect.innerHTML = '<option value="">Loading seats...</option>';
        seatGrid.innerHTML = '';

        fetch(`/reservations/available-seats?date=${date}`)
            .then(response => {
                if (!response.ok) throw new Error('Failed to load seats');
                return response.json();
            })
            .then(data => {
                // Populate seat dropdown
                seatSelect.innerHTML = '<option value="">Select a seat</option>';
                data.forEach(seat => {
                    const option = document.createElement('option');
                    option.value = seat.id;
                    option.textContent = `${seat.seat_number} - ${seat.location}`;
                    seatSelect.appendChild(option);
                });
                seatSelect.disabled = false;

                // Build a map of seat_number => seat.id
                const seatMap = {};
                const availableSeatNumbers = [];

                data.forEach(seat => {
                   const num = parseInt(seat.seat_number.replace(/\D/g, ''));

                    seatMap[seat.id] = num;
                    availableSeatNumbers.push(num);
                });

                // Render grid 1–100
                for (let i = 1; i <= 100; i++) {
                    const seatBox = document.createElement('div');
                    seatBox.textContent = i;
                    seatBox.classList.add('seat-box');

                    if (availableSeatNumbers.includes(i)) {
                        seatBox.classList.add('seat-available');
                    } else {
                        seatBox.classList.add('seat-unavailable');
                    }

                    seatGrid.appendChild(seatBox);
                }

                // Highlight selected seat in blue
                seatSelect.addEventListener('change', function () {
                    const selectedSeatId = Number(this.value);
                    const selectedSeatNumber = seatMap[selectedSeatId];

                    document.querySelectorAll('.seat-box').forEach(box => {
                        const boxNumber = Number(box.textContent);
                        box.classList.remove('seat-selected', 'seat-available');

                        if (boxNumber === selectedSeatNumber) {
                            box.classList.add('seat-selected');
                        } else if (availableSeatNumbers.includes(boxNumber)) {
                            box.classList.add('seat-available');
                        }
                    });
                });
            })
            .catch(error => {
                seatSelect.innerHTML = '<option value="">Error loading seats</option>';
                console.error('Error loading seats:', error);
            });
    }
});
</script>
@endpush
