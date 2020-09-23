<?php

use Illuminate\Http\Request;
/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->group(['prefix' => 'api', /*'middleware' => "auth"*/], function () use ($router) {
    $router->get('login',  ['uses' => 'RegistrationController@Authenticate']);
    
    $router->get('signup', ['uses' => 'RegistrationController@Signup']);

    $router->get('forgotpassword', ['uses' => 'RegistrationController@Forgotpassword']);

    $router->get('allusers', ['uses' => 'UserController@allUsers']);

    $router->get('profile', 'UserController@profile');

    $router->get('users/{id}', 'UserController@singleUser');


    // $router->get('verify/{token}');

    // $router->delete('registration/{id}', ['uses' => 'RegistrationController@delete']);

    // $router->put('registration/{id}', ['uses' => 'RegistrationController@update']);
});

// $router->group(['prefix' => 'api'], function () use ($router) {

//     $router->post('signup', 'RegistrationController@register');

//     $router->post('login', 'RegistrationController@login');

//     $router->get('forgotpassword', 'RegistrationController@Forgotpassword');

    
// });


// $router->get('/post/{id}', ['middleware' => 'auth', function (Request $request, $id) {
//     $user = Auth::registration();

//     $user = $request->name();

//     //
// }]);
//['middleware' => "auth"],

// $router->group(['middleware'=>"auth"],function($router) {

// });

