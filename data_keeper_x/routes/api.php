<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Application Routes
Route::prefix("/")->group(function () {

    // authentication
    Route::post("login", [AuthController::class, "Login"]);
    Route::delete("logout/{id}", [AuthController::class, "Logout"]);

    // reset password
    Route::post("reset_password/request", [AuthController::class, "requestResetPassword"]);
    Route::put('reset_password', [AuthController::class, 'resetPassword']);

    // role permissions
});
