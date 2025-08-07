<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;

class AdminController extends Controller
{
   public function dashboard()
    {
        $today = now()->format('Y-m-d');
        
        // Today's stats
        $totalSeats = \App\Models\Seat::count();
        $bookedSeats = Reservation::where('reservation_date', $today)
            ->where('status', 'active')
            ->count();
        $availableSeats = $totalSeats - $bookedSeats;
        
        // Upcoming 6 days stats (excluding weekends)
        $upcomingDays = collect();
        $currentDate = Carbon::today();
        $daysCollected = 0;
        
        while ($daysCollected < 6) {
            $currentDate->addDay();
            
            if (!$currentDate->isWeekend()) {
                $dateStr = $currentDate->format('Y-m-d');
                $upcomingDays->push([
                    'date' => $dateStr,
                    'day_name' => $currentDate->format('D'),
                    'date_display' => $currentDate->format('M j'),
                    'booked' => Reservation::where('reservation_date', $dateStr)
                        ->where('status', 'active')
                        ->count(),
                    'total_seats' => $totalSeats
                ]);
                $daysCollected++;
            }
        }
        
        // Today's reservations
        $reservations = Reservation::with(['user', 'seat'])
            ->where('reservation_date', $today)
            ->where('status', 'active')
            ->get();

        return view('admin.dashboard', compact(
            'totalSeats',
            'bookedSeats',
            'availableSeats',
            'reservations',
            'upcomingDays'
        ));
    }
    public function reports()
    {
        $startDate = request('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = request('end_date', now()->format('Y-m-d'));

        $reservations = Reservation::whereBetween('reservation_date', [$startDate, $endDate])
            ->with(['user', 'seat'])
            ->orderBy('reservation_date')
            ->get();

        // Group by date for chart
        $reservationsByDate = $reservations->groupBy('reservation_date')
            ->map(function($items) {
                return $items->count();
            });

        // Group by user for user stats
        $reservationsByUser = $reservations->groupBy('user.name')
            ->map(function($items) {
                return $items->count();
            })
            ->sortDesc();

        return view('admin.reports', compact(
            'reservations',
            'reservationsByDate',
            'reservationsByUser',
            'startDate',
            'endDate'
        ));
    }
}