<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\Signupverify;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Registration;
use Tymon\JWTAuth\JWTAuth;

class RegistrationController extends Controller
{

    public function signup(Request $request)
    {
        $this->validate($request, [
            'name'     => 'required',
            'email'    => 'required|email|max:255',
            'password' => 'required',
            'role'     => 'required',
            'createdBy'=> 'required',
        ]);

        $registration = new Registration;
        
        $registration->name = $request->name;
        $registration->email = $request->email;
        $registration->password = Hash::make($request->password);
        $registration->role = $request->role;
        $registration->createdBy = $request->createdBy;
        
        $token = 'alpha';
        $registration->token = $token;
        $registration->save();
        
        Mail::to($request->email)->send(new Signupverify($token));
        
        return response()->json(compact('token'));    
    }



    public function authenticate(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email|max:255',
            'password' => 'required',
        ]);

        $credentials = $request->only(['email', 'password']);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        return $this->respondWithToken($token);
    }



    public function forgotpassword(Request $request)
    {
        $this->validate($request, ['email' => 'required', 'new_password' => 'required']);

        $registration = Registration::where('email', $request->input('email'))->first();
        
        $registration->password = Hash::make($request->new_password);

        $registration->save();
    }


}    

?>
