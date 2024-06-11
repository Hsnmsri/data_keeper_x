<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataController;

Route::post('create', [DataController::class, "Create"]);
Route::patch('update', [DataController::class, "Update"]);
Route::delete('/delete/{id}', [DataController::class, "Delete"]);
Route::get('/data/{id}', [DataController::class, "getDataById"]);
Route::get('list', [DataController::class, "getAllData"]);
