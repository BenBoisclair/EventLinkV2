<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/events/{event}/exhibitors', [App\Http\Controllers\API\ExhibitorController::class, 'listForEvent'])
        ->name('api.exhibitors.listForEvent');
});

Route::middleware(['contact.cors', 'throttle:5,1'])->group(function () {
    Route::post('/admin/contact', [App\Http\Controllers\API\ContactController::class, 'store'])
        ->name('api.contact.store');
});
