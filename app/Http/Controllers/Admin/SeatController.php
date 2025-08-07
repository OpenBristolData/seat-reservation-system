<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Seat;

class SeatController extends Controller
{
    public function index()
    {
        $seats = Seat::orderBy('seat_number')->paginate(20);
        return view('admin.seats.index', compact('seats'));
    }

    public function create()
    {
        return view('admin.seats.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'seat_number' => 'required|string|unique:seats',
            'location' => 'required|string',
            'status' => 'required|in:available,unavailable',
        ]);

        Seat::create($validated);
        return redirect()->route('admin.seats.index')->with('success', 'Seat created successfully!');
    }

    public function show(Seat $seat)
    {
        return view('admin.seats.show', compact('seat'));
    }

    public function edit(Seat $seat)
    {
        return view('admin.seats.edit', compact('seat'));
    }

    public function update(Request $request, Seat $seat)
    {
        $validated = $request->validate([
            'seat_number' => 'required|string|unique:seats,seat_number,' . $seat->id,
            'location' => 'required|string',
            'status' => 'required|in:available,unavailable',
        ]);

        $seat->update($validated);
        return redirect()->route('admin.seats.index')->with('success', 'Seat updated successfully!');
    }

    public function destroy(Seat $seat)
    {
        // Check if seat has any reservations
        if ($seat->reservations()->exists()) {
            return back()->with('error', 'Cannot delete seat with existing reservations.');
        }

        $seat->delete();
        return redirect()->route('admin.seats.index')->with('success', 'Seat deleted successfully!');
    }
}