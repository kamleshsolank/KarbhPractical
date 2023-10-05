<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'mobile_number' => 'required|digits:10',
            'password' => ['required', 'min:8', 'regex:/^[a-zA-Z0-9]+$/'],
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);


        if ($validator->fails()) {
            return redirect('/register')
                ->withErrors($validator)
                ->withInput();
        }

        $password = bcrypt($request->input('password'));
        $token = Str::random(60);
        $user = new User();
        $user->name = $request->input('name');
        $user->mobile_number = $request->input('mobile_number');
        $user->password = $password;
        $user->token = $token;
        $user->image = $request->file('image')->store('images');

        $user->save();

        return redirect('/login')->with('success', 'Registration successful.');
    }


    public function showLoginForm()
{
    return view('auth.login');
}

public function login(Request $request)
{
    $credentials = $request->validate([
        'mobile_number' => 'required|digits:10',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        return view('auth.dashboard');
    }

    return back()->withErrors(['mobile_number' => 'Invalid login credentials']);
}

public function apilogin(Request $request)
{

    $credentials = $request->only('mobile_number', 'password');

    try {
        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
    } catch (JWTException $e) {
        return response()->json(['error' => 'Could not create token'], 500);
    }

    return response()->json(['token' => $token]);
}

public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Logged out']);
    }
}
