<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RegistrationController;
use App\Mail\Adduserverify;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Registration;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{

    /**
     * Instantiate a new UserController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get the authenticated User.
     *
     * @return Response
     */
    public function profile()
    {
        return response()->json(['user' => Auth::user()], 200);
    }

    /**
     * Get all User.
     *
     * @return Response
     */
    public function allUsers()
    {
        // echo "akjhd";
        // die;
        $query = Registration::where('deleted_by', null)->get();
        return response()->json(['users' =>  $query, ], 200);
    }

    public function addUser(Request $request)
    {

        $this->validate($request, [
            'name'     => 'required|string',
            'email'    => 'required|email|max:255',
        ]);
        
        if(Auth::user()->role != "Admin")
        {
            return response()->json(['message' => 'Only admin can add users!']);
        }

        $query = Registration::where('email', $request->input('email'))->get();
        if(count($query) != 0)
        {
            return response()->json(['message' => 'Email already exist!']);
        }

        $adduser = new Registration;
        
        $adduser->name = $request->name;
        $adduser->email = $request->email;
        $adduser->password = rtrim(base64_encode(md5(microtime())),"=");
        $adduser->role = "Normal";
        $adduser->created_by = Auth::user()->id;
        $adduser->verify_status = "Yes";        
        $adduser->deleted_by = null;
        
        $token = rtrim(base64_encode(md5(microtime())),"=");
        $adduser->token = $token;
        $adduser->save();

        Mail::to($request->email)->send(new Adduserverify($adduser));
        
        return response()->json(['message' => 'Password sent to the email']);

    }

    public function deleteUser($userid)
    {
        if(Auth::user()->role != "Admin")
        {
            return response()->json(['message' => 'Only admin can delete users!'],401);
        }
        
        $deluser = Registration::where('id', $userid)->first();
        if($deluser == null)
        {
            return response()->json(['message' => 'Enter a registered user to delete!'],403);
        }
        if($deluser->role == "Admin")
        {
            return response()->json(['message' => 'Admins can not be deleted'],403);
        }
        
        DB::table('registration')
            ->where('token', $deluser->token)
            ->update(['deleted_by' => Auth::user()->id]);
        
        return response()->json(['message' => 'User deleted Successfully','user' => $deluser],200);
    }

    public function search(Request $request)
    {
        $users = Registration::where('deleted_by', null);
        
        if ($request->has('email')) 
        {
            $users->where('email', $request->email);
        }
        
        if ($request->has('name')) 
        {
            $users->where('name', $request->name);
        }
        
        if ($request->has('created_by')) 
        {
            $users->where('created_by', $request->created_by);
        }
        
        if ($request->has('role')) 
        {
            $users->where('role', $request->role);
        }
        
        if($users == null)
        {
            return response()->json(['message' => 'Nothing to display']);
        }
        return $users->get()->toArray();
        // return response()->json(['message' => 'Successful','users' => $users->get()->toArray()],200);

    }

}