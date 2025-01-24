<?php

namespace App\Http\Controllers;

use App\Models\Box;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'q' => ['required', 'string']
        ]);

        $query = $request->input('q');

        $boxes = Box::query()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('location', 'like', "%{$query}%");
            })
            ->orWhereHas('items', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->with(['items' => function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            }])
            ->get();

        return response()->json([
            'data' => $boxes
        ], Response::HTTP_OK);
    }
}
