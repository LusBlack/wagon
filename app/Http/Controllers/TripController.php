<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TripController extends Controller
{
    public function store(Request $request) {
        $request->validate([
            "origin" => 'required',
            "destination" => 'required',
            'destination_name' => 'required'

        ]);

        $request->user()->trips()->create($request->only([
            'origin',
            'destination',
            'destination_name'
        ]));
    }
}
