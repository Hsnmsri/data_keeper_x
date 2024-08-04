<?php

namespace App\Classes\AccessToken;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

class AccessToken
{
    /**
     * Generate a JWT token with an optional expiration time for a given user ID.
     *
     * This function generates a JSON Web Token (JWT) for the given user ID with an optional
     * expiration time. If no expiration time is provided, it defaults to the value specified
     * in the environment variable `ACCESS_TOKEN_EXPIRE`. The token is signed using the HS256
     * algorithm and includes the user ID as the subject (sub), the issued at time (iat),
     * and the expiration time (exp).
     *
     * @param int $user_id The ID of the user for whom the JWT is being generated.
     * @param int|null $expire_time_ms Optional expiration time of the token in milliseconds.
     *                                Defaults to `ACCESS_TOKEN_EXPIRE` if not provided.
     * @return string The generated JWT token.
     */
    public static function make(int $user_id, $expire_time = null): string
    {
        // Calculate expiration time
        $expiry = time() + (isset($expire_time_ms) ? $expire_time : env("ACCESS_TOKEN_EXPIRE"));

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

    /**
     * Check if a given JWT token is expired.
     *
     * @param string $token The JWT token to be checked.
     * @return bool True if the token is expired, false otherwise.
     */
    public static function isExpired(string $token): bool
    {
        try {
            // Get the secret key from the environment variable
            $secretKey = env('JWT_SECRET');

            // Decode the token
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));

            // Check the expiration time
            if (isset($decoded->exp) && $decoded->exp < time()) {
                return true;
            }

            return false;
        } catch (ExpiredException $e) {
            // Token has expired
            return true;
        } catch (Exception $e) {
            // Token is invalid for some other reason
            return true;
        }
    }

    /**
     * Extract the user ID from a given JWT token.
     *
     * @param string $token The JWT token to be decoded.
     * @return int The user ID extracted from the token.
     * @throws Exception if the token is invalid.
     */
    public static function getUserIdFromToken(string $token): int
    {
        try {
            // Get the secret key from the environment variable
            $secretKey = env('JWT_SECRET');

            // Decode the token
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));

            // Return the user ID (sub) from the decoded token
            return $decoded->sub;
        } catch (Exception $e) {
            // Handle exceptions (e.g., invalid token, expired token, etc.)
            throw new Exception('Invalid token: ' . $e->getMessage());
        }
    }
}
