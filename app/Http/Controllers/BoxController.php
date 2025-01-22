<?php

namespace App\Http\Controllers;

use App\Models\Box;
use App\Http\Requests\StoreBoxRequest;
use App\Http\Requests\UpdateBoxRequest;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class BoxController extends Controller
{
    public function index()
    {
        $boxes = Box::all();

        return response()->json([
            'data' => $boxes->load('items')
        ], Response::HTTP_OK);
    }

    public function show(Box $box)
    {
        return response()->json([
            'data' => $box->load('items')
        ], Response::HTTP_OK);
    }

    public function store(StoreBoxRequest $request)
    {
        $box = Box::create($request->validated());

        return response()->json([
            'data' => $box
        ], Response::HTTP_CREATED);
    }

    public function update(UpdateBoxRequest $request, Box $box)
    {
        $box->update($request->validated());

        return response()->json([
            'data' => $box
        ], Response::HTTP_OK);
    }

    public function destroy(Box $box)
    {
        $box->delete();

        return response()->noContent(Response::HTTP_NO_CONTENT);
    }
}
