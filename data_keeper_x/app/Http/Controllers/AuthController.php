<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Classes\AccessToken\AccessToken;
use App\Classes\ApiResponse\ApiResponse;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function Login(Request $request)
    {
        // Define validation rules
        $validation_rules = [
            'email' => 'required|email',
            'password' => 'required|min:8'
        ];

        // Create a validator
        $validator = Validator::make($request->all(), $validation_rules);

        // Check if validation fails
        if ($validator->fails()) {
            return ApiResponse::failure(400, "Bad Request!")
                ->errors($validator->errors()->toArray())
                ->toArray();
        }

        //get user data
        $user = User::where("email", $request->email);

        // user exist
        if ($user->count() > 0) {

            // select user
            $user = $user->first();

            // check user password
            if (!Hash::check($user->id . $request->password . $user->id, $user->password)) {
                return ApiResponse::failure(403, "Pawword Invalid!")->toArray();
            }
        }

        // user not exist
        if ($user->count() == 0) {

            try {
                // create new user
                $user = new User();
                $user->email = $request->email;
                $user->password = Hash::make("x");
                $user->api_secret = Hash::make("x");
                if (!$user->save()) {
                    return ApiResponse::failure()->errors(['line' => __LINE__])->toArray();
                }

                // update password, api_secret
                $user->password = Hash::make($user->id . $request->password . $user->id);
                $user->api_secret = AccessToken::make($user->id);
                if (!$user->update()) {
                    return ApiResponse::failure()->errors(['line' => __LINE__])->toArray();
                }
            } catch (Exception $error) {
                return ApiResponse::failure()->errors([$error->getMessage()])->toArray();
            }
        }

        // generate access token and login
        return ApiResponse::success()->data(['token' => AccessToken::make($user->id)])->toArray();
    }

    public function verifyAccessToken(Request $request)
    {
        return true;
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
