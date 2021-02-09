<?php

use \Illuminate\Support\Facades\Auth;
use \App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (Auth::user()) {
        return redirect()->intended('/dashboard');
    }

    $error = session()->pull('error');
    $creds = session()->pull('creds');

    return view('login', ['error'=> $error, 'creds' => $creds]);
});

Route::get('/auth/step2', function () {
    $error = session()->pull('error');
    $creds = session()->pull('creds');

    return view('login2', ['error'=> $error, 'creds' => $creds]);
});


Route::get('/dashboard', function () {
    return "Нет доступных модулей";
});

Route::get('/logout', function () {
    session()->pull('creds');

    Auth::logout();

    return redirect()->intended('/');
});

Route::post('/auth/login', [LoginController::class, 'index']);

Route::post('/auth/login2', [LoginController::class, 'login2']);
