<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Http\Resources\EventResource;
use App\Http\Traits\CanLoadRelationships;
// use Illuminate\Contracts\Auth\Access\Gate as GateContract; 

class EventController extends Controller
{
    use CanLoadRelationships;
    private array $relationships = ['user', 'attendees', 'attendees.user'];

    public function __construct()
    {
        $this->middleware(['auth:sanctum'])->only(['store', 'update', 'destroy']);
        $this->authorizeResource(Event::class, 'event');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $relationships = ['user', 'attendees', 'attendees.user'];
        // $query = Event::query();
        $query = $this->loadRelationships(Event::query());

        // foreach ($relations as $relation) {
        //     $query->when($this->shouldIncludeRelation($relation),
        //     fn($query) => $query->with($relation));

        // }
        // $this->shouldIncludeRelation('attendees');
        // return EventResource::collection(Event:: with('attendees')->paginate());
        return EventResource::collection($query->latest()->paginate());
    }

    // protected function shouldIncludeRelation(string $relation): bool
    // {
    //     $include = request()->query('include');
    //     if(!$include) {
    //         return false;
    //     }
    //     $relations = array_map("trim", explode(',', $include));

    //     return in_array($relation, $relations);
    // }

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
        create(array_merge([
            'user_id' => $request->user()->id
        ], $request->all()));
    return new EventResource($this->loadRelationships($event));
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        $event->load('user', 'attendees');
        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        // if ($gate->denies('update-event', $event)) {
        //     abort(403, 'You cannot edit this event');
        // }
        // $this->authorize('update-event', $event);
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'sometimes|date',
            'end_time' => 'sometimes|date|after:start_time',
        ]);
        $event->update($request->all());
        return new EventResource($this->loadRelationships($event));
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
