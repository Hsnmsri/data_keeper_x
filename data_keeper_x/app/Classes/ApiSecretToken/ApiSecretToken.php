<?php

namespace App\Classes\ApiSecretToken;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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

    /**
     * Decode a JWT token to get the user ID.
     *
     * This function decodes a JSON Web Token (JWT) and extracts the user ID
     * from the payload.
     *
     * @param string $token The JWT token to be decoded.
     * @return int The user ID extracted from the token.
     * @throws \Exception if the token is invalid or expired.
     */
    public static function getUserIdFromToken(string $token): int
    {
        try {
            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
            return $decoded->sub;
        } catch (\Exception $e) {
            // Handle exceptions (e.g., invalid token, expired token, etc.)
            throw new \Exception('Invalid token: ' . $e->getMessage());
        }
    }
}
