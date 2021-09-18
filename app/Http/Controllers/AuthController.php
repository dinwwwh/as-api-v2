<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Register and store user to database
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string'],
            'login' => ['required_without:email', 'string'],
            'email' => ['required_without:login', 'email'],
        ]);
        $credentials = $request->only('email', 'password', 'login');

        if (auth()->attempt($credentials, $request->remember)) {
            return auth()->user();
        }

        return response([
            'message' => 'Login failed.',
        ], 401);
    }

    /**
     * Read current authenticated user's infos
     *
     * @return \Illuminate\Http\Response
     */
    public function readProfile()
    {
        return auth()->user();
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        auth()->logout();

        return response([
            'message' => 'Logout success!!'
        ]);
    }
}
