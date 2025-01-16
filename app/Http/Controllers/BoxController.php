<?php

namespace App\Http\Controllers;

use App\Models\Box;
use Illuminate\Http\Request;

class BoxController extends Controller
{
    public function index()
    {
        $boxes = Box::all();

        return response()->json([
            'data' => $boxes
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
        ]);

        $box = Box::create($validated);

        return response()->json([
            'data' => $box
        ], 201);
    }

    public function update(Request $request, Box $box)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
        ]);

        $box->update($validated);

        return response()->json([
            'data' => $box
        ]);
    }

    public function destroy(Box $box)
    {
        $box->delete();

        return response()->noContent();
    }
}
