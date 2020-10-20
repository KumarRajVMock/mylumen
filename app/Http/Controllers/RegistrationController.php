<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
// use App\Mail\Signupverify;
// use App\Mail\Resetpassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Registration;
use App\Models\Forgotpassword;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Jobs\SignupJob;
use App\Jobs\ResetpasswordJob;

class RegistrationController extends Controller
{

    public function signup(Request $request)
    {
        $this->validate($request, [
            'name'     => 'required|string',
            'email'    => 'required|email|max:255',
            'password' => 'required|string|min:4',
        ]);

        $query = Registration::where('email', $request->input('email'))->get();
        if(count($query) != 0)
        {
            return response()->json(['message' => 'Email already exist!'],401);
        }
        $registration = new Registration;
        $registration->name = $request->name;
        $registration->email = $request->email;
        $registration->password = Hash::make($request->password);
        // $registration->role = "Normal";
        $registration->role = "Admin";
        $registration->verify_status = "No";
        
        $token = rtrim(base64_encode(md5(microtime())),"=");
        $registration->token = $token;
        $registration->save();
        
        // Mail::to($request->email)->send(new Signupverify($registration));
        $this->dispatch(new SignupJob($request->email, $registration));
        return response()->json(['message' => 'Verify your email!'],200);
    }

    public function verify($token)
    {        
        $value = DB::table('registration')->where([
            ['token', $token],
        ])->get();
        
        if(count($value) > 0)
        {
            if($value[0]->verify_status !== 'Yes')
            {
                DB::table('registration')
                ->where('token', $token)
                ->update(['verify_status' => 'Yes', 'email_verified_at' => Carbon::now()->toDateTimeString()]);
                
                return response()->json(['message' => 'Verification done!'],200);
            }
            else
            {
                return response()->json(['message' => 'Already Verified!'],403);
            }
        }
        else
        {
            return response()->json(['message' => 'Please Login!'],401);
        }
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email|max:255',
            'password' => 'required|string',
        ]);
        
        $query = Registration::where('email', $request->input('email'))->get();
        
        if(count($query) > 0)
        {
            if( $query[0]->deleted_by != NULL)
            {
                return response()->json(['message' => 'Please signup to continue'], 401);
                // return response()->json(['Deleted by: ' => $query[0]->deleted_by]);//soft delete
            }
            
            elseif($query[0]->verify_status == "No")
            {
                return response()->json(['message' => 'Please verify email'], 403);
            }
            
            // elseif (Hash::check($request->password, $query[0]->password))//hash check
            // {
            //     echo "Success \n";
            //     $tok = Auth::attempt($request->only(['email', 'password']));
            //     echo($tok);
            //     // return $this->respondWithToken($tok);//
            // }
            elseif($tok = Auth::attempt($request->only(['email', 'password'])))//check order
            {
                return $this->respondWithToken($tok, $query[0]);
            }
            
            else
            {
                return response()->json(['message' => 'Incorrect password'], 401);
            }
        }       
        else
        {
            return response()->json(['message' => 'Incorrect Email. Enter correct email or signup first to continue.'],401);
        }
    }

    public function forgotpassword(Request $request)
    {
        $this->validate($request, ['email' => 'required',]);

        $registration = Registration::where('email', $request->input('email'))->first();
        if($registration != null)
        {
            $token = rtrim(base64_encode(md5(microtime())),"=");
            $query = new Forgotpassword;
            $query->token = $token;
            $query->email = $request->input('email');
            $query->reset_status = "No";
            $query->save();
            // Mail::to($request->email)->send(new Resetpassword($query));
            $this->dispatch(new ResetpasswordJob($request->email, $query));
        }
        
        return response()->json(['message','Check your email to reset your password'], 200);
    }

    public function resetpassword(Request $request)
    {
        $this->validate($request, [
            'token' => 'required',
            'new_password' => 'required',
        ]);
        
        $token = $request->input('token');
        $query = DB::table('forgotpassword')->where('token', $token)->get();
        
        if(count($query) > 0)
        {
            if($query[0]->reset_status != "Yes")
            {
                DB::table('forgotpassword')
                ->where('token',$token)
                ->update(['reset_status' => 'Yes']);

                $newpass = Hash::make($request->new_password);
            
                DB::table('registration')
                ->where('email', $query[0]->email)
                ->update(['password' => $newpass,]);

            }
            else
            {
                return response()->json(['message','Password reset already'],403);
            }
            // DB::table('registration')
            // ->where([
            //     ['email', $value[0]->email],
            //     ['token','<>', $token],
            // ])
            // ->delete();
            return response()->json(['message','Password reset successful'],200);

        }
        else
        {
            return response()->json(['message','Please login or signup'],403);
        }
    }



}    

?>
