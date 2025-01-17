<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use Illuminate\Http\Response;

class ItemController extends Controller
{
    public function store(StoreItemRequest $request)
    {
        $item = Item::create($request->validated());

        return response()->json([
            'data' => $item
        ], Response::HTTP_CREATED);
    }

    public function update(UpdateItemRequest $request, Item $item)
    {
        $item->update($request->validated());

        return response()->json([
            'data' => $item
        ], Response::HTTP_OK);
    }

    public function destroy(Item $item)
    {
        $item->delete();

        return response()->noContent(Response::HTTP_NO_CONTENT);
    }
}
