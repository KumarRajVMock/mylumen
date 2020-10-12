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

$router->group(['prefix' => 'api', ], function () use ($router) {
    $router->post('login',  ['uses' => 'RegistrationController@Login']);
    
    $router->post('signup', ['uses' => 'RegistrationController@Signup']);

    $router->post('verify/{token}', ['uses' => 'RegistrationController@Verify']);

    $router->post('forgotpassword', ['uses' => 'RegistrationController@Forgotpassword']);

    $router->post('resetpassword', ['uses' => 'RegistrationController@Resetpassword']);

    $router->get('allusers', 'UserController@allUsers');

    $router->get('profile', 'UserController@profile');

    $router->post('adduser', 'UserController@addUser');

    $router->delete('deleteuser/{userid}', 'UserController@deleteUser');

    $router->post('search', 'UserController@search');

});

// make git m="your message"


