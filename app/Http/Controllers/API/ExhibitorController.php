<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExhibitorController extends Controller
{
    public function listForEvent(Request $request, Event $event): JsonResponse
    {
        $exhibitors = $event->exhibitors()->get();

        return response()->json([
            'data' => $exhibitors
        ]);
    }
}