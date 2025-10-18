<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Customer;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    protected function redirectTo()
    {
        $user = auth()->user();
        if ($user && strcasecmp((string)$user->role, 'admin') === 0) {
            return '/admin';
        }
        return route('home.index');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user', // mặc định user
        ]);
    }

    /**
     * The user has been registered.
     */
    protected function registered(Request $request, $user)
    {
        // Tạo hồ sơ Customer rỗng cho user 
        if ($user && strcasecmp((string)$user->role, 'admin') !== 0) {
            Customer::firstOrCreate(['user_id' => $user->id], 
    ['phone'   => null,
            'address' => null,]
        );
        }
        // Sau đăng ký: admin -> /admin, user -> trang chủ
        if ($user && strcasecmp((string)$user->role, 'admin') === 0) {
            return redirect('/admin');
        }
        return redirect()->route('home.index');
    }
}
