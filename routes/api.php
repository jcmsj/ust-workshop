<?php

use App\Http\Requests\StoreLeadRequest;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/lead', function (StoreLeadRequest $request) {
    Lead::create($request->validated());
    return response()->json(['message' => 'Lead created'], 201);
})->middleware('throttle:30,1'); # 30 requests per minute
