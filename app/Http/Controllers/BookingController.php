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
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        
        if ($user->role === 'admin') {
            return response()->json(Booking::with(['user', 'office', 'transaction'])->get());
        }
        
        return response()->json(Booking::where('user_id', $user->id)->with(['office', 'transaction'])->get());
    }

    public function allBookings()
    {
        return response()->json(Booking::with(['user', 'office', 'transaction'])->get());
    }

    public function store(Request $request)
    {
        $price = $request->input('price') ?? $request->input('total_amount');
        $request->merge(['total_amount' => $price]);

        $request->validate([
            'office_id' => 'required|exists:offices,id',
            'total_amount' => 'required|numeric',
        ]);

        $office = Office::findOrFail($request->office_id);

        if ($office->is_full_booked) {
            return response()->json(['error' => 'Office is already full booked'], 422);
        }

        $userId = auth()->id() ?? $request->input('user_id');
        if (!$userId) {
            return response()->json(['error' => 'User ID is required'], 422);
        }

        $booking = Booking::create([
            'booking_trx_id' => 'BELVA-' . strtoupper(Str::random(10)),
            'user_id' => $userId,
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
        $request->validate([
            'payment_method' => 'required|string',
            'payment_proof' => 'required',
            'amount' => 'nullable|numeric',
        ]);

        $path = '';
        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('payments', 'public');
        } else {
            $path = $request->input('payment_proof');
        }

        $amount = $request->input('amount') ?? $booking->total_amount;

        DB::transaction(function () use ($booking, $request, $path, $amount) {
            $booking->transaction()->create([
                'payment_method' => $request->payment_method,
                'payment_proof' => $path,
                'amount' => $amount,
                'status' => 'pending',
            ]);

            $booking->update(['status' => 'paid']);
        });

        return response()->json(['message' => 'Payment uploaded successfully']);
    }

    public function verifyPayment(Request $request, Booking $booking)
    {
        if (auth()->check() && auth()->user()->role === 'customer') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        DB::transaction(function () use ($booking) {
            $booking->update(['status' => 'confirmed']);
            if ($booking->transaction) {
                $booking->transaction->update(['status' => 'verified']);
            } else {
                $booking->transaction()->create([
                    'payment_method' => 'Bank Transfer',
                    'payment_proof' => 'verified_by_admin.jpg',
                    'amount' => $booking->total_amount,
                    'status' => 'verified',
                ]);
            }
            
            $booking->office->update(['is_full_booked' => true]);
        });

        return response()->json(['message' => 'Payment verified and booking confirmed']);
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $booking->update(['status' => $request->status]);

        return response()->json($booking);
    }

    protected function authorizeAccess(Booking $booking)
    {
        if (!auth()->check()) {
            return; // Allow public lookup for local student compatibility
        }
        $user = auth()->user();
        if ($user->role !== 'admin' && $user->id !== $booking->user_id) {
            abort(403, 'Unauthorized access to booking');
        }
    }
}
