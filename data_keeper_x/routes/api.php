<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AccessTokenCheck;
use App\Http\Middleware\ClientRequestCheck;

// Application Routes
Route::prefix("/")->group(function () {

    // Authentication
    Route::post("login", [AuthController::class, "Login"]);
    Route::get("verify_token", [AuthController::class, "verifyAccessToken"])->middleware([AccessTokenCheck::class]);

    // Reset password
    Route::prefix("reset_password")->group(function () {
        Route::post("request", [AuthController::class, "requestResetPassword"]);
        Route::put('/', [AuthController::class, 'resetPassword']);
    });

    // User
    Route::prefix("users")->middleware([AccessTokenCheck::class])->group(function () {
        Route::get("/", [UserController::class, "getUserList"]);
        Route::post("/", [UserController::class, "createUser"]);
        Route::get("{id}", [UserController::class, "getUserById"]);
        Route::put("{id}", [UserController::class, "updateUser"]);
        Route::put("{id}/email", [UserController::class, "updateUserEmail"]);
        Route::put("{id}/password", [UserController::class, "updateUserPassword"]);
        Route::put("{id}/role", [UserController::class, "updateUserRole"]);
        Route::post("{id}/api_secret", [UserController::class, "renewUserApiSecret"]);
        Route::delete("{id}", [UserController::class, "deleteUser"]);
    });

    // Role
    Route::prefix("role")->middleware([AccessTokenCheck::class])->group(function () {
        Route::post("/", [RoleController::class, "createRole"]);
        Route::put("/{id}", [RoleController::class, "updateRoleDetail"]);
        Route::put("/{id}/permissions", [RoleController::class, "updateRolePermissions"]);
        Route::delete("/{id}", [RoleController::class, "deleteRole"]);
        Route::get("/", [RoleController::class, "getRoleList"]);
        Route::get("/{id}", [RoleController::class, "getRoleById"]);
    });

    // Category
    Route::prefix("category")->middleware([AccessTokenCheck::class])->group(function () {
        Route::post("/", [CategoryController::class, "createCategory"]);
        Route::put("/{id}", [CategoryController::class, "updateCategory"]);
        Route::delete("/{id}", [CategoryController::class, "deleteCategory"]);
        Route::get("/{user_id}", [CategoryController::class, "getCategoryByUserId"]);
    });

    // Data
    Route::prefix("data")->middleware([AccessTokenCheck::class])->group(function () {
        Route::post("/", [DataController::class, "createData"]);
        Route::put("/{id}", [DataController::class, "updateDataBody"]);
        Route::put("/{id}/category", [DataController::class, "updateDataCategory"]);
        Route::delete("/{id}", [DataController::class, "deleteData"]);
        Route::get("/", [DataController::class, "getDataList"]);
        Route::get("/{id}", [DataController::class, "getDataById"]);
    });

    // Client routes
    Route::prefix("client")->middleware([ClientRequestCheck::class])->group(function () {
        Route::post("data", [ClientController::class, "createDataRecord"]);
        Route::put("data/{id}", [ClientController::class, "updateDataRecord"]);
        Route::delete("data/{id}", [ClientController::class, "deleteDataRecord"]);
        Route::get("data", [ClientController::class, "getDataByCategory"]);
        Route::get("data/{id}", [ClientController::class, "getDataById"]);
    });
});
