<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Models\User;


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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm(){
        return view('login');
    }

    public function login(Request $request){
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ],[
            'username.required' => 'The Username Field is required',
            'password.required' => 'The Password Field is required',
        ]);

        
    
        $user = User::where(['username' => $request->username, 'password' => md5($request->password)])->first();
        if ($user) {
            if (Auth::login($user)) {
                return redirect('/')->with(['success' => 'Successfully logged in']);
            }
        }
        return redirect("login")->with(['error' => 'Please enter correct username and password']);
    }

    public function username(){
        return "username";
    }

    public function logout() {
        Session::flush();
        Auth::logout();
        return Redirect('login');
    }

    
}
