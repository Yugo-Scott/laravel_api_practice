<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Event::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);
        $event = Event::
        create(array_merge(['user_id' => 1], $request->all()));
        return $event;
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        // $event->load('attendees');
        return $event;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'sometimes|date',
            'end_time' => 'sometimes|date|after:start_time',
        ]);
        $event->update($request->all());
        return $event;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();
        // return response()->json(['message' => 'Event deleted successfully']);
        return response(status: 204);
    }
}
