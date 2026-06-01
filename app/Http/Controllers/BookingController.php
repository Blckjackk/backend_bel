<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            return response()->json(Booking::with(['user', 'office'])->get());
        }
        
        return response()->json(Booking::where('user_id', $user->id)->with('office')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'office_id' => 'required|exists:offices,id',
            'total_amount' => 'required|numeric',
        ]);

        $office = Office::findOrFail($request->office_id);

        if ($office->is_full_booked) {
            return response()->json(['message' => 'Office is already full booked'], 422);
        }

        if (auth()->user()->role !== 'customer') {
            return response()->json(['message' => 'Only customers can make bookings'], 403);
        }

        $booking = Booking::create([
            'booking_trx_id' => 'BELVA-' . strtoupper(Str::random(10)),
            'user_id' => auth()->id(),
            'office_id' => $request->office_id,
            'status' => 'pending',
            'total_amount' => $request->total_amount,
        ]);

        return response()->json($booking, 201);
    }

    public function show(Booking $booking)
    {
        $this->authorizeAccess($booking);
        return response()->json($booking->load(['user', 'office', 'transaction']));
    }

    public function uploadPayment(Request $request, Booking $booking)
    {
        $this->authorizeAccess($booking);

        $request->validate([
            'payment_method' => 'required|string',
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'amount' => 'required|numeric',
        ]);

        $path = $request->file('payment_proof')->store('payments', 'public');

        DB::transaction(function () use ($booking, $request, $path) {
            $booking->transaction()->create([
                'payment_method' => $request->payment_method,
                'payment_proof' => $path,
                'amount' => $request->amount,
                'status' => 'pending',
            ]);

            $booking->update(['status' => 'paid']);
        });

        return response()->json(['message' => 'Payment uploaded successfully']);
    }

    public function verifyPayment(Request $request, Booking $booking)
    {
        if (auth()->user()->role === 'customer') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        DB::transaction(function () use ($booking) {
            $booking->update(['status' => 'confirmed']);
            $booking->transaction->update(['status' => 'verified']);
            
            // Auto-fill office if it's confirmed
            $booking->office->update(['is_full_booked' => true]);
        });

        return response()->json(['message' => 'Payment verified and booking confirmed']);
    }

    protected function authorizeAccess(Booking $booking)
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $user->id !== $booking->user_id) {
            abort(403, 'Unauthorized access to booking');
        }
    }
}
