<?php

namespace App\Http\Controllers;

use App\Models\Box;
use App\Http\Requests\StoreBoxRequest;
use App\Http\Requests\UpdateBoxRequest;

class BoxController extends Controller
{
    public function index()
    {
        $boxes = Box::all();

        return response()->json([
            'data' => $boxes
        ]);
    }

    public function store(StoreBoxRequest $request)
    {
        $box = Box::create($request->validated());

        return response()->json([
            'data' => $box
        ], 201);
    }

    public function update(UpdateBoxRequest $request, Box $box)
    {
        $box->update($request->validated());

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
