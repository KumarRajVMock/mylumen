<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;


use Illuminate\Support\Facades\Auth;



class Controller extends BaseController
{
    protected function respondWithToken($tok, $query)
    {
        return response()->json([
            'user' => $query,
            'token' => $tok,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ], 200);
    }
}
