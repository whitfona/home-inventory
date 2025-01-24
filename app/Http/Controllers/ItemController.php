<?php

namespace App\Http\Controllers;

use App\Models\Box;
use App\Models\Item;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use Illuminate\Http\Response;

class ItemController extends Controller
{
    public function index(Box $box)
    {
        return response()->json([
            'data' => $box->items
        ], Response::HTTP_OK);
    }

    public function show(Box $box, Item $item)
    {
        if ($item->box_id !== $box->id) {
            abort(404);
        }

        return response()->json([
            'data' => $item
        ], Response::HTTP_OK);
    }

    public function store(StoreItemRequest $request, Box $box)
    {
        $item = $box->items()->create($request->validated());

        return response()->json([
            'data' => $item
        ], Response::HTTP_CREATED);
    }

    public function update(UpdateItemRequest $request, Box $box, Item $item)
    {
        if ($item->box_id !== $box->id) {
            abort(404);
        }

        $item->update($request->validated());

        return response()->json([
            'data' => $item
        ], Response::HTTP_OK);
    }

    public function destroy(Box $box, Item $item)
    {
        if ($item->box_id !== $box->id) {
            abort(404);
        }

        $item->delete();

        return response()->noContent(Response::HTTP_NO_CONTENT);
    }
}
