<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AccessTokenCheck;

// Application Routes
Route::prefix("/")->group(function () {

    // authentication
    Route::post("login", [AuthController::class, "Login"]);
    Route::get("verify_token", [AuthController::class, "verifyAccessToken"]);

    // reset password
    Route::prefix("reset_password")->group(function () {
        Route::post("request", [AuthController::class, "requestResetPassword"]);
        Route::put('/', [AuthController::class, 'resetPassword']);
    });

    // user
    Route::prefix("users")->group(function () {
        Route::get("/", [UserController::class, "getUserList"]);
        Route::post("/", [UserController::class, "createUser"]);
        Route::get("{id}", [UserController::class, "getUserById"]);
        Route::put("{id}", [UserController::class, "updateUser"]);
        Route::put("{id}/email", [UserController::class, "updateUserEmail"]);
        Route::put("{id}/password", [UserController::class, "updateUserPassword"]);
        Route::put("{id}/role", [UserController::class, "updateUserRole"]);
        Route::post("{id}/api_secret", [UserController::class, "renewUserApiSecret"]);
        Route::delete("{id}", [UserController::class, "deleteUser"]);
    })->middleware(AccessTokenCheck::class);
});
