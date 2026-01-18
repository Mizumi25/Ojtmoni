<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\LocationUpdated;
use App\Models\Location;
use Illuminate\Support\Facades\Log; // Import the Log facade

class LocationController extends Controller
{


    public function update(Request $request)
    {
        $user = auth()->user();
        $location = $user->location;

        // Log the received data
        Log::info('Location Update Request:', [
            'user_id' => $user->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'location_name' => $request->location_name,
        ]);

        // Convert input to floats to avoid string vs float mismatch
        $newLat = (float) $request->latitude;
        $newLng = (float) $request->longitude;
        $newName = $request->location_name;

        if (!$location) {
            // No location exists yet — create it
            $location = Location::create([
                'latitude' => $newLat,
                'longitude' => $newLng,
                'location_name' => $newName,
            ]);

            $user->location_id = $location->id;
            $user->save();

            broadcast(new LocationUpdated($user->id, $newLat, $newLng, $newName))->toOthers();

            return response()->json(['message' => 'New location created and broadcasted.']);
        }

        // Check if values actually changed
        $hasChanged = $location->latitude != $newLat ||
                            $location->longitude != $newLng ||
                            $location->location_name !== $newName;

        if (!$hasChanged) {
            return response()->json(['message' => 'No changes. Skipped DB update and broadcast.']);
        }

        // Update only if something changed
        $location->update([
            'latitude' => $newLat,
            'longitude' => $newLng,
            'location_name' => $newName,
        ]);

        broadcast(new LocationUpdated($user->id, $newLat, $newLng, $newName))->toOthers();

        return response()->json(['message' => 'Location updated and broadcasted.']);
    }
}