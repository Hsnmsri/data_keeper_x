<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Classes\ApiResponse\ApiResponse;
use Illuminate\Support\Facades\Validator;
use App\Classes\ApiSecretToken\ApiSecretToken;

class UserController extends Controller
{
    public function getUserList(Request $request)
    {
        return ApiResponse::success()->data(User::all()->makeHidden("password")->toArray())->toArray();
    }

    public function createUser(Request $request)
    {
        // validation
        $validatorArray = [
            "role_id" => "required|numeric",
            "first_name" => "required",
            "last_name" => "required",
            "email" => "email|required",
            "password" => "required"
        ];

        $validation = Validator::make($request->all(), $validatorArray);
        if ($validation->fails()) {
            return ApiResponse::failure()->errors($validation->errors()->toArray())->toArray();
        }

        // check email
        if (User::where("email", $request->email)->count() !== 0) {
            return ApiResponse::failure(409, "Conflict")->message("Email already exist!")->toArray();
        }

        // create user
        try {
            // init user data
            $user = new User();
            $user->role_id = $request->role_id;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->password = "Password";
            $user->api_secret = "Secret";

            // create user
            if (!$user->save()) {
                return ApiResponse::failure()->message("Save User Failed!")->toArray();
            }

            // update password and token
            $user->password = Hash::make($user->id . $request->password . $user->id);
            $user->api_secret = ApiSecretToken::make($user->id);

            // update user
            if (!$user->update()) {
                return ApiResponse::failure()->message("Update User Failed!")->toArray();
            }

            return ApiResponse::success()->toArray();
        } catch (\Exception $errors) {
            return ApiResponse::failure()->errors([$errors->getMessage()])->toArray();
        }
    }

    public function getUserById(Request $request)
    {
        // get user data
        $user = User::where("id", $request->id);

        // if user not exist
        if ($user->count() == 0) {
            return ApiResponse::failure()->message("User Not Found!")->toArray();
        }

        return ApiResponse::success()->data($user->first()->makeHidden(["password"])->toArray())->toArray();
    }

    public function updateUser(Request $request)
    {
        // validation
        $validatorArray = [
            "first_name" => "required",
            "last_name" => "required",
        ];

        $validation = Validator::make($request->all(), $validatorArray);
        if ($validation->fails()) {
            return ApiResponse::failure()->errors($validation->errors()->toArray())->toArray();
        }

        try {
            // get user
            $user = User::find($request->id);
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;

            // update user
            if (!$user->save()) {
                return ApiResponse::failure()->toArray();
            }

            return ApiResponse::success()->toArray();
        } catch (\Exception $errors) {
            return ApiResponse::failure()->data($errors->getMessage() ?? [])->toArray();
        }

        return "updateUser worked!";
    }

    public function updateUserEmail(Request $request)
    {
        // validation
        $validatorArray = [
            "email" => "email|required",
        ];

        $validation = Validator::make($request->all(), $validatorArray);
        if ($validation->fails()) {
            return ApiResponse::failure()->errors($validation->errors()->toArray())->toArray();
        }

        // check email exist
        if (User::where("email", $request->email)->count() !== 0) {
            return ApiResponse::failure(409, "Conflict")->toArray();
        }

        try {
            // update user
            $user = User::find($request->id);
            $user->email = $request->email;

            // update user
            if (!$user->save()) {
                return ApiResponse::failure()->toArray();
            }

            return ApiResponse::success()->toArray();
        } catch (\Exception $errors) {
            return ApiResponse::failure()->data($errors->getMessage() ?? [])->toArray();
        }
    }

    public function updateUserPassword(Request $request)
    {
        // validation
        $validatorArray = [
            "new_password" => "required",
        ];

        $validation = Validator::make($request->all(), $validatorArray);
        if ($validation->fails()) {
            return ApiResponse::failure()->errors($validation->errors()->toArray())->toArray();
        }

        try {
            // update user
            $user = User::find($request->id);
            $user->password = $user->id . Hash::make($request->password) . $user->id;

            // update user
            if (!$user->save()) {
                return ApiResponse::failure()->toArray();
            }

            return ApiResponse::success()->toArray();
        } catch (\Exception $errors) {
            return ApiResponse::failure()->data($errors->getMessage() ?? [])->toArray();
        }
    }

    public function updateUserRole(Request $request)
    {
        // validation
        $validatorArray = [
            "role_id" => "required",
        ];

        $validation = Validator::make($request->all(), $validatorArray);
        if ($validation->fails()) {
            return ApiResponse::failure()->errors($validation->errors()->toArray())->toArray();
        }

        try {
            // update user
            $user = User::find($request->id);
            $user->role_id = $request->role_id;

            // update user
            if (!$user->save()) {
                return ApiResponse::failure()->toArray();
            }

            return ApiResponse::success()->toArray();
        } catch (\Exception $errors) {
            return ApiResponse::failure()->data($errors->getMessage() ?? [])->toArray();
        }
    }

    public function renewUserApiSecret(Request $request)
    {
        try {
            // update user
            $user = User::find($request->id);
            $user->api_secret = ApiSecretToken::make($user->id);

            // update user
            if (!$user->save()) {
                return ApiResponse::failure()->toArray();
            }

            return ApiResponse::success()->toArray();
        } catch (\Exception $errors) {
            return ApiResponse::failure()->data($errors->getMessage() ?? [])->toArray();
        }
    }

    public function deleteUser(Request $request)
    {
        try {
            // delete user
            if (!User::find($request->id)->delete()) {
                return ApiResponse::failure()->toArray();
            }

            return ApiResponse::success()->toArray();
        } catch (\Exception $errors) {
            return ApiResponse::failure()->data($errors->getMessage() ?? [])->toArray();
        }
    }
}
