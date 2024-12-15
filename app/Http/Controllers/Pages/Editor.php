<?php

namespace App\Http\Controllers\Pages;

use Inertia\Inertia;
use App\Models\Event;
use App\Models\Layouts;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Editor extends Controller
{
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'data' => 'required|array',
            'event_id' => 'required|integer',
        ]);

        // Check if there's an existing layout for this event
        $existingLayout = Layouts::where('event_id', $request->event_id)->first();

        if ($existingLayout) {
            // Update the existing layout
            $existingLayout->update([
                'data' => json_encode($request->data),
            ]);

            // return response()->json(['message' => 'Layout updated successfully!'], 200);
        } else {
            // Create a new layout
            Layouts::create([
                'event_id' => $request->event_id,
                'data' => json_encode($request->data),
            ]);

            // return response()->json(['message' => 'Layout created successfully!'], 201);
        }
    }
    public function show($organization,$event)
    {
        $eventVal = Event::where('event_slug', $event)
            ->with('organization')
            ->with('tickets')
            ->first();
        $layout = Layouts::where('event_id', $eventVal->id)->first();

        return Inertia::render('index', [
            'eventData' => $eventVal,
            'layout' => $layout ? $layout->data : null,
            'organization' => $organization
        ]);
    }
}
