<?php

namespace App\Http\Controllers\Auth;
use App\User;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function index(Request $request) {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        if ($user) {

            $isAccept = Hash::check($credentials['password'], $user->password);

            session(['creds' => $credentials]);

            if ($isAccept) {
                $user->confirm_code = rand(100000, 999999);
                $user->confirm_attempts = 0;

                $user->save();

                Mail::send('vendor.mail.code', ['code' => $user->confirm_code], function ($m) use ($user) {
                    $m->from('hello@app.com', 'Admin');

                    $m->to($user->email, $user->email)->subject('Auth code!');
                });


                return redirect('/auth/step2');
            }
        }

        session(['error' => 'Ошибка авторизации']);
        return redirect()->intended('/');
    }

    public function login2(Request $request) {
        $credentials = session('creds');

        $user = User::where('email', $credentials['email'])->first();

        if ($user) {

            $isValidCode = $user->confirm_code === +$request->get('code') && $user->confirm_attempts < 3;

            if (!$isValidCode) {
                $user->confirm_attempts++;

                $user->save();

                session(['error' => 'Неправильный код подтверждения']);

                return redirect('/auth/step2');
            }

            if ($isValidCode && Auth::attempt($credentials)) {
                session()->pull('error');

                return redirect()->intended('/dashboard');
            }
        }

        session(['error' => 'Ошибка авторизации']);
        return redirect()->intended('/');
    }
}
