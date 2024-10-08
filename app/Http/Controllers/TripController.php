<?php

namespace App\Http\Controllers;

use App\Events\TripLocationUpdated;
use App\Models\Trip;
use App\Events\TripEnded;
use App\Events\TripStarted;
use App\Events\TripAccepted;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function store(Request $request) {
        //validate request
        $request->validate([
            "origin" => 'required',
            "destination" => 'required',
            'destination_name' => 'required'

        ]);
        //store validated request
        return $request->user()->trips()->create($request->only([
            'origin',
            'destination',
            'destination_name'
        ]));
    }

    public function show (Request $request, Trip $trip) {
        if($trip->user->id === $request->user()->id) {
            return $trip;
        }
        if($trip->driver && $request->user->driver) {
            if($trip->driver->id === $request->user()->id) {
                return $trip;
            }
        }
        return response()->json(['message'=> 'Cannot find this trip'], 404);
    }

    public function accept (Request $request, Trip $trip) {
        $request->validate([
            'driver_location' => 'required'
        ]);

        $trip->update([
            'driver_id' => $request->user()->id,
            'driver_location' => $request->driver_location,
        ]);

        $trip->load('driver.user');

        TripAccepted::dispatch($trip, $request->user());

        return $trip;
    }

    public function start (Request $request, Trip $trip) {
        $trip->update([
            'is_started' => true
        ]);

        $trip->load('driver.user');

        TripStarted::dispatch($trip, $request->user());

        return $trip;
    }

    public function end (Request $request, Trip $trip) {
        $trip->update([
            'is_completed' => true
        ]);

        $trip->load('driver.user');

        TripEnded::dispatch($trip, $request->user());

        return $trip;
    }

    public function location (Request $request, Trip $trip) {
        $trip->validate([
            'driver_location' => 'required'
        ]);

        $trip->update([
            'driver_location' => $request->driver_location
        ]);

        $trip->load('driver.user');

        TripLocationUpdated::dispatch($trip, $request->user());

        return $trip;
    }
}
