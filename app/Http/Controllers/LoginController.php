<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\LoginNeedsVerification;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function submit(Request $request) {
        //validate the phone number

        $request->validate([
            'phone' => 'required|numeric|min:10'
        ]);


        //find or create a user model
        $user = User::firstOrCreate([
            'phone' => $request->phone
        ]);

        if (!$user) {
            return response()->json(['message'=> 'Could not process a user with that phone number'], 401);
        }

        //$user->notify(new LoginNeedsVerification);

        // return a response
        return response()->json(['message' => 'Text message notification sent. ']);
    }

    public function verify(Request $request) {
        $request->validate([
            'phone' => 'required|numeric|min:10'
            //'login_code' => 'required|numeric|between:111111,999999'
        ]);

        //find the user
        $user = User::where('phone', $request->phone)

       // ->where('login_code', $request->login_code)
        ->first();

        if($user) {
            // $user->update([
            //     'login_code' => null

            // ]);
            return $user->createToken($request->phone)->plainTextToken;
        }


        return response()->json(['message' => 'user not found'], 401);

    }

}
