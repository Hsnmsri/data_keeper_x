<?php

namespace App\Classes\ApiSecretToken;

use Firebase\JWT\JWT;

class ApiSecretToken
{
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
    public static function make(int $user_id): string
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
}
