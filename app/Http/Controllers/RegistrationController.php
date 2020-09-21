<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Registration;
use Tymon\JWTAuth\JWTAuth;

class RegistrationController extends Controller
{
    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }


    public function signup(Request $request)
    {
        $registration = new Registration;

        $registration->name = $request->name;
        $registration->email = $request->email;
        $registration->password = Hash::make($request->password);
        $registration->role = $request->role;
        $registration->createdBy = $request->createdBy;

        $registration->save();
    }

    public function authenticate(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email|max:255',
            'password' => 'required',
        ]);
        try 
        {
            if (! $token = $this->jwt->attempt($request->only('email', 'password'))) 
            {
                return response()->json(['user_not_found'], 404);
            }
        } 
        catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) 
        {
            return response()->json(['token_expired'], 500);
        } 
        catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) 
        {
            return response()->json(['token_invalid'], 500);
        } 
        catch (\Tymon\JWTAuth\Exceptions\JWTException $e) 
        {
            return response()->json(['token_absent' => $e->getMessage()], 500);
        }
        
        return response()->json(compact('token'));

        // $registration = Registration::where('email', $request->input('email'))->first();

        // if(Hash::check($request->input('password'), $registration->password))
        // {
        //     // return response()->json(['status' => 'success']);
        //     return response()->json($registration->name);

        // }
        // else
        // {
        //     return response()->json(['status' => 'fail'],401);
        // }

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
