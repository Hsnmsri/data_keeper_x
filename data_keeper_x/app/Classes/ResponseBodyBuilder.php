<?php

namespace App\Classes;

class ResponseBodyBuilder
{
    /**
     * Build a success response array.
     *
     * This static method constructs a success response array, which typically includes
     * a status of "success," an optional message, and an optional data payload.
     *
     * @param string|null $message Optional message to include in the response.
     * @param mixed $data Optional data to include in the response.
     *
     * @return array The success response array containing status, message, and data.
     */
    static public function buildSuccessResponse($messages = null, $data = null, int $status_code = 200): array
    {
        return [
            "status" => "success",
            "status_code" => $status_code,
            "message" => $messages,
            "data" => $data
        ];
    }

    /**
     * Build a failure response array.
     *
     * This static method constructs a failure response array, which typically includes
     * a status of "failure," an optional message, and an optional data payload.
     *
     * @param string|null $message Optional message to include in the response.
     * @param mixed $data Optional data to include in the response.
     *
     * @return array The failure response array containing status, message, and data.
     */
    static public function buildFailureResponse($messages = null, $data = null, int $status_code = 400): array
    {
        return [
            "status" => "failure",
            "status_code" => $status_code,
            "message" => $messages,
            "data" => $data
        ];
    }
}
