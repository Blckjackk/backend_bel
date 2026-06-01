<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OfficeController extends Controller
{
    public function index(Request $request)
    {
        $query = Office::with(['city', 'features', 'images']);

        if ($request->has('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        return response()->json($query->where('is_open', true)->get());
    }

    public function store(Request $request)
    {
        if (auth()->user()->role === 'customer') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:255',
            'thumbnail' => 'required|image',
            'about' => 'required|string',
            'address' => 'required|string',
            'price' => 'required|numeric',
            'duration_type' => 'required|string',
        ]);

        $validated['slug'] = Str::slug($request->name);
        $validated['provider_id'] = auth()->id();
        $validated['thumbnail'] = $request->file('thumbnail')->store('offices', 'public');

        $office = Office::create($validated);

        return response()->json($office, 201);
    }

    public function show($slug)
    {
        $office = Office::where('slug', $slug)->with(['city', 'features', 'images', 'testimonials.user', 'provider'])->firstOrFail();
        return response()->json($office);
    }

    public function update(Request $request, Office $office)
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $user->id !== $office->provider_id) {
            return response()->json(['message' => 'Unauthorized to edit this office'], 403);
        }

        $validated = $request->validate([
            'name' => 'string|max:255',
            'about' => 'string',
            'address' => 'string',
            'price' => 'numeric',
            'is_open' => 'boolean',
            'is_full_booked' => 'boolean',
        ]);

        if ($request->has('name')) {
            $validated['slug'] = Str::slug($request->name);
        }

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('offices', 'public');
        }

        $office->update($validated);

        return response()->json($office);
    }

    public function destroy(Office $office)
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $user->id !== $office->provider_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $office->delete();
        return response()->json(['message' => 'Office deleted successfully']);
    }
}
