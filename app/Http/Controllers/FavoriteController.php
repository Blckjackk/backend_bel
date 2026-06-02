<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        if (!$userId) {
            return response()->json([]);
        }

        $favorites = Favorite::where('user_id', $userId)
            ->with('office')
            ->get();
        return response()->json($favorites);
    }

    public function getUserFavorites($userId)
    {
        $favorites = Favorite::where('user_id', $userId)
            ->with('office')
            ->get();
        return response()->json($favorites);
    }

    public function store(Request $request)
    {
        $request->validate([
            'office_id' => 'required|exists:offices,id',
        ]);

        $userId = auth()->id() ?? $request->input('user_id');

        if (!$userId) {
            return response()->json(['error' => 'User ID is required'], 422);
        }

        $favorite = Favorite::firstOrCreate([
            'user_id' => $userId,
            'office_id' => $request->office_id,
        ]);

        return response()->json([
            'message' => 'Added to favorites',
            'data' => $favorite
        ], 201);
    }

    public function destroy($id)
    {
        $favorite = Favorite::find($id);

        if (!$favorite) {
            return response()->json(['error' => 'Favorite not found'], 404);
        }

        if (auth()->check() && $favorite->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $favorite->delete();
        return response()->json(['message' => 'Removed from favorites']);
    }
}
