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
    $router->get('login',  ['uses' => 'RegistrationController@Login']);
    
    $router->get('signup', ['uses' => 'RegistrationController@Signup']);

    $router->get('verify', ['uses' => 'RegistrationController@Verify']);

    $router->get('forgotpassword', ['uses' => 'RegistrationController@Forgotpassword']);

    $router->get('resetpassword', ['uses' => 'RegistrationController@Resetpassword']);

    $router->get('allusers', 'UserController@allUsers');

    $router->get('profile', 'UserController@profile');

    $router->get('adduser', 'UserController@addUser');

    $router->get('deleteuser', 'UserController@deleteUser');

    $router->get('search', 'UserController@search');

});



