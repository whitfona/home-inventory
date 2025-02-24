<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Box;
use App\Models\Item;
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
        $data = $request->validated();

        // Handle file upload
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('photos', 'public');
        }

        $item = $box->items()->create($data);

        return response()->json([
            'data' => $item
        ], Response::HTTP_CREATED);
    }

    public function update(UpdateItemRequest $request, Box $box, Item $item)
    {
        if ($item->box_id !== $box->id) {
            abort(404);
        }

        $data = $request->validated();

        // Handle file upload
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('photos', 'public');
        }
        $item->update($data);

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
