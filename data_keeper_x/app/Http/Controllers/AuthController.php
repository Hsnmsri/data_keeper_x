<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function Login(Request $request)
    {
        return "Login Worked!";
    }

    public function Logout(Request $request)
    {
        return "Logout Worked!";
    }

    public function requestResetPassword(Request $request)
    {
        return "requestResetPassword Worked!";
    }

    public function resetPassword(Request $request)
    {
        return "resetPassword Worked!";
    }
}
