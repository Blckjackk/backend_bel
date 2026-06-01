<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CityController extends Controller
{
    public function index()
    {
        return response()->json(City::withCount('offices')->get());
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|unique:cities,name',
            'image' => 'required|image',
        ]);

        $path = $request->file('image')->store('cities', 'public');

        $city = City::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => $path,
        ]);

        return response()->json($city, 201);
    }

    public function show(City $city)
    {
        return response()->json($city->load('offices'));
    }
}
