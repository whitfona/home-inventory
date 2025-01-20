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
            ->where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->orWhere('location', 'like', "%{$query}%")
            ->get();

        $boxIds = $boxes->pluck('id');

        $items = Item::query()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->orWhereIn('box_id', $boxIds)
            ->get();

        return response()->json([
            'data' => [
                'boxes' => $boxes,
                'items' => $items
            ]
        ], Response::HTTP_OK);
    }
}
