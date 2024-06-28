<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Classes\ApiResponse\ApiResponse;
use Firebase\JWT\JWT;
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
                return ApiResponse::failure(401, "Unauthorized")->toArray();
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
                $user->api_secret = $this->generateApiSecretToken($user->id);
                if (!$user->update()) {
                    return ApiResponse::failure()->errors(['line' => __LINE__])->toArray();
                }
            } catch (Exception $error) {
                return ApiResponse::failure()->errors($error ?? [])->toArray();
            }
        }

        // generate access token and login
        return ApiResponse::success()->data(['token' => $this->generateAccessToken($user->id)])->toArray();
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

    /**
     * Generate a JWT token for a given user ID.
     *
     * This function generates a JSON Web Token (JWT) for the given user ID.
     * The token is signed using the HS256 algorithm and includes the user ID
     * as the subject (sub) and the issued at time (iat).
     *
     * @param int $user_id The ID of the user for whom the JWT is being generated.
     * @return string The generated JWT token.
     */
    private function generateApiSecretToken($user_id): string
    {

        return JWT::encode(
            [
                'sub' => $user_id,
                'iat' => time(),
            ],
            env('JWT_SECRET'),
            'HS256'
        );
    }

    /**
     * Generate an access token with a custom expiration time for a given user ID.
     *
     * This function generates an access token (JWT) for the given user ID with a
     * custom expiration time. The token is signed using the HS256 algorithm and
     * includes the user ID as the subject (sub) and the issued at time (iat).
     *
     * @param int $user_id The ID of the user for whom the access token is being generated.
     * @param int $expiration_time Expiration time of the token in seconds from the current time.
     * @return string The generated access token (JWT).
     */
    private function generateAccessToken($user_id): string
    {
        // Calculate expiration time
        $expiry = time() + env("ACCESS_TOKEN_EXPIRE");

        // Define the payload
        $payload = [
            'sub' => $user_id,
            'iat' => time(),
            'exp' => $expiry,
        ];

        // Get the secret key from the environment variable
        $secretKey = env('JWT_SECRET');

        // Encode the payload to generate the access token (JWT)
        $accessToken = JWT::encode($payload, $secretKey, 'HS256');

        // Return the generated access token
        return $accessToken;
    }
}
