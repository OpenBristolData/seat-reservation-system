<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Seat;

class ReservationController extends Controller
{
 public function index(Request $request)
{
    $query = Reservation::with(['user', 'seat'])
        ->orderBy('reservation_date', 'desc');

    // Date filter
    if ($request->has('date') && $request->date != '') {
        $query->where('reservation_date', $request->date);
    }

    // User filter - only apply if user parameter exists and is not empty
    if ($request->filled('user')) {
        $query->where('user_id', $request->user);
    }

    $reservations = $query->paginate(20);
    $users = User::where('role', 'intern')->get();

    return view('admin.reservations.index', compact('reservations', 'users'));
}

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();
        return back()->with('success', 'Reservation deleted successfully!');
    }
}