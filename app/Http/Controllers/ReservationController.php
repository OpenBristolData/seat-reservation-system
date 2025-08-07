<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Seat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{   
    public function dashboard()
{
    $today = now()->format('Y-m-d');
    $reservations = auth()->user()->reservations()
        ->where('reservation_date', '>=', $today)
        ->where('status', 'active')
        ->with('seat')
        ->orderBy('reservation_date', 'asc') 
        ->get();
    
    return view('intern.dashboard', compact('reservations'));
}

public function index()
{
    $reservations = auth()->user()->reservations()
        ->with('seat')
        ->orderBy('reservation_date', 'desc')
        ->paginate(10);
    
    return view('intern.reservations.index', compact('reservations'));
}

    public function create()
    {
        $now = Carbon::now();
        $currentTime = $now->format('H:i');

        if ($now->isWeekend()) {
            // If today is weekend, start from next Monday
            $startDate = $now->next(Carbon::MONDAY);
        } elseif ($currentTime >= '16:00') {
            // If after 4:00 PM on a weekday, start from the next business day (tomorrow, skipping weekends)
            $startDate = $now->addDay(); // Move to tomorrow
            if ($startDate->isWeekend()) { // If tomorrow is Saturday/Sunday, jump to Monday
                $startDate = $startDate->next(Carbon::MONDAY);
            }
        } else {
            // Before 4:00 PM on a weekday, allow booking from today
            $startDate = $now->copy()->startOfDay();
        }
        
        // Generate 7 days (excluding weekends)
        $availableDates = collect();
        $daysAdded = 0;
        $currentDate = $startDate->copy();
        
        while ($daysAdded < 7) {
            if (!$currentDate->isWeekend()) {
                $availableDates->push($currentDate->copy());
                $daysAdded++;
            }
            $currentDate->addDay();
        }
        
        return view('intern.reservations.create', [
            'availableDates' => $availableDates,
            'currentDate' => $now->format('Y-m-d'),
            'currentTime' => $currentTime
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reservation_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $reservationDate = Carbon::parse($value);
                    $now = Carbon::now();
                    
                    // Past dates not allowed
                    if ($reservationDate->lt($now->startOfDay())) {
                        $fail('Past dates cannot be booked.');
                    }
                    
                    // Weekend dates not allowed
                    if ($reservationDate->isWeekend()) {
                        $fail('Weekend dates cannot be booked.');
                    }
                    
                    // Same-day booking before 7:30AM
                    if ($reservationDate->isToday() && $now->format('H:i') >= '07:30') {
                        $fail('Same-day reservations must be made before 7:30 AM.');
                    }
                    
                    // Current date disabled after 4:00 PM
                    if ($reservationDate->isToday() && $now->format('H:i') >= '16:00') {
                        $fail('Today\'s reservations are closed after 4:00 PM.');
                    }
                }
            ],
            'seat_id' => 'required|exists:seats,id',
        ]);

        // Check for existing reservation
        $existingReservation = Reservation::where('user_id', Auth::id())
            ->where('reservation_date', $validated['reservation_date'])
            ->where('status', 'active')
            ->exists();

        if ($existingReservation) {
            return back()->withErrors([
                'reservation_date' => 'You already have a reservation for this date.'
            ])->withInput();
        }

        // Check seat availability
        $seat = Seat::findOrFail($validated['seat_id']);
        if (!$seat->isAvailableForDate($validated['reservation_date'])) {
            return back()->withErrors([
                'seat_id' => 'This seat is already booked for the selected date.'
            ])->withInput();
        }

        Reservation::create([
            'user_id' => Auth::id(),
            'seat_id' => $validated['seat_id'],
            'reservation_date' => $validated['reservation_date'],
            'status' => 'active',
        ]);

        return redirect()->route('reservations.index')->with('success', 'Reservation created successfully!');
    }

    public function destroy(Reservation $reservation)
    {
        // Users can only cancel their own reservations
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        // Cancellation allowed anytime
        $reservation->update(['status' => 'cancelled']);
        
        return back()->with('success', 'Reservation cancelled successfully!');
    }

    public function getAvailableSeats(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $date = Carbon::parse($request->date);
        
        // Validate date rules again for API
        if ($date->isWeekend()) {
            return response()->json(['error' => 'Weekend dates cannot be booked'], 400);
        }
        
        if ($date->lt(Carbon::today())) {
            return response()->json(['error' => 'Past dates cannot be booked'], 400);
        }

        $availableSeats = Seat::where('status', 'available')
            ->whereDoesntHave('reservations', function($query) use ($date) {
                $query->where('reservation_date', $date->format('Y-m-d'))
                      ->where('status', 'active');
            })
            ->get();

        return response()->json($availableSeats);
    }
}