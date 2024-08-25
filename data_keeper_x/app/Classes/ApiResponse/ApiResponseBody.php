<?php

namespace App\Classes\ApiResponse;

/**
 * Class ApiResponseBody
 *
 * This class represents the structure of an API response body. It uses method chaining
 * to allow fluent setting of response properties.
 */
class ApiResponseBody
{
    private bool $success;
    private $status_code;
    private $status_title;
    private $message;
    private $data;
    private $errors;

    /**
     * ApiResponseBody constructor.
     *
     * @param string $status The status of the response, e.g., "success" or "error".
     * @param int $status_code The HTTP status code, e.g., 200 for success.
     * @param string $status_title The title of the status, e.g., "OK" or "Error".
     */
    public function __construct(bool $success, int $status_code, string $status_title)
    {
        $this->success = $success;
        $this->status_code = $status_code;
        $this->status_title = $status_title;
    }

    /**
     * Set the message for the API response.
     *
     * @param string|null $message The message to set.
     * @return ApiResponseBody Returns the instance to allow method chaining.
     */
    public function message(string $message = null): ApiResponseBody
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Set the data for the API response.
     *
     * @param array|null $data The data to set.
     * @return ApiResponseBody Returns the instance to allow method chaining.
     */
    public function data(array $data = null): ApiResponseBody
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Set the errors for the API response.
     *
     * @param array|null $errors The errors to set.
     * @return ApiResponseBody Returns the instance to allow method chaining.
     */
    public function errors(array $errors = null): ApiResponseBody
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * Convert the API response body to an array.
     *
     * @return array The response body as an associative array.
     */
    public function toArray(): array
    {
        return [
            "success" => $this->success,
            "status_code" => $this->status_code,
            "status_title" => $this->status_title,
            "message" => $this->message,
            "data" => $this->data,
            "errors" => $this->errors,
        ];
    }
}
