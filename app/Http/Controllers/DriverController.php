<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function show(Request $request) {

        //return the user and assoc driver model
        $user = $request->user();
        $user->load('driver');

       // return $request->user();
       return $user;
    }

    public function update(Request $request) {

        $request->validate([
            'year' => 'required|numerica|between:2010,2024',
            'make' => 'required',
            'model' => 'required',
            'color' => 'requiredalpha',
            'license_plate' => 'required',
            'name' => 'required'
        ]);

        $user = $request->user();

        $user->update($request->only('name'));

        $user->driver()->updateOrCreate($request->only([
            'year',
            'make',
            'model',
            'color',
            'license_plate'

        ]));

        $user->load('driver');

        return $user;

    }
}
