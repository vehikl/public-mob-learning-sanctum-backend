<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->get('/all-users', function (Request $request) {
    return User::all();
})->name('all-users');


// Login (AKA: Get a API token)
Route::post('/me', function( Request $request){
    $credentials = [
        'email' => $request->input('email'),
        'password' =>  $request->input('password'),
    ];

    if(!Auth::attempt($credentials)) {
        return abort(401);
    }

    $access_token = Auth::user()->createToken('login_token');
    return response()->json(['access_token' => $access_token->plainTextToken]);
}) ->name('login');

// Have the route that gives the list of all users
