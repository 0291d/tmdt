<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\Customer;

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
    protected $redirectTo = '/home';

    protected function redirectTo()
    {
        $user = auth()->user();
        if ($user && ($user->role === 'admin' || $user->role === 'Admin')) {
            return '/admin';
        }
        return route('home.index');
    }

    /**
     * Ensure customer profile exists for non-admin users on login.
     */
    protected function authenticated(Request $request, $user)
    {
        if (strcasecmp((string)$user->role, 'admin') !== 0) {
            Customer::firstOrCreate(['user_id' => $user->id]);
        }
        return redirect()->intended($this->redirectTo());
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
