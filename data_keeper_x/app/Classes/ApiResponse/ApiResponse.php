<?php

namespace App\Classes\ApiResponse;

/**
 * Class ApiResponse
 *
 * This class provides static methods to create API response instances.
 */
class ApiResponse
{

    /**
     * Create a successful API response instance.
     *
     * @param int $status_code The HTTP status code (default: 200).
     * @param string $status_title The title of the status (default: "OK").
     * @return ApiResponseBody Returns an instance of ApiResponseBody for a successful response.
     */
    public static function success(int $status_code = 200, string $status_title = "OK")
    {
        return new ApiResponseBody(true, $status_code, $status_title);
    }

    /**
     * Create a failure API response instance.
     *
     * @param int $status_code The HTTP status code for failure (default: 500).
     * @param string $status_title The title of the status for failure (default: "Internal Server Error").
     * @return ApiResponseBody Returns an instance of ApiResponseBody for a failure response.
     */
    public static function failure(int $status_code = 500, string $status_title = "Internal Server Error")
    {
        return new ApiResponseBody(false, $status_code, $status_title);
    }
}
