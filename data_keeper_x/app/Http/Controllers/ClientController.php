<?php

namespace App\Http\Controllers;

use App\Classes\ApiSecretToken\ApiSecretToken;
use App\Models\Category;
use App\Models\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    public function createDataRecord(Request $request)
    {
        // validation
        $validatorArray = [
            "category_id" => "required|numeric",
            "data" => "required",
        ];

        $validation = Validator::make($request->all(), $validatorArray);
        if ($validation->fails()) {
            return $this->response(false, "Please fill the required fields.", $validation->errors()->toArray());
        }

        // check category id
        if (Category::where("id", $request->category_id)->where("user_id", ApiSecretToken::getUserIdFromToken($request->api_secret))->count() === 0) {
            return $this->response(false, "Category id dont have relation with the user.", []);
        }

        // create client data
        try {
            $data = new Data();
            $data->user_id = ApiSecretToken::getUserIdFromToken($request->api_secret);
            $data->category_id = $request->category_id;
            $data->data_body = serialize($request->data_body);
            if (!$data->save()) {
                return $this->response(false, "Save Data Failed.", []);
            }

            return $this->response(true, null, []);
        } catch (\Exception $errors) {
            return $this->response(false, "Server Error.", [$errors->getMessage()]);
        }
    }

    public function updateDataRecord(Request $request)
    {
        // check category id
        if ($request->has("category_id")) {
            if (Category::where("id", $request->category_id)->where("user_id", ApiSecretToken::getUserIdFromToken($request->api_secret))->count() === 0) {
                return $this->response(false, "Category id dont have relation with the user.", []);
            }
        }

        // update client data
        try {
            $data = Data::find($request->id);

            // update category id
            if (
                $request->has("category_id") &&
                !is_null($request->category_id) &&
                !empty($request->category_id)
            ) {
                $data->category_id = $request->category_id;
            }

            // check data_body
            if (
                $request->has("data_body") &&
                !is_null($request->data_body) &&
                !empty($request->data_body)
            ) {
                $data->data_body = serialize($request->data_body);
            }

            if (!$data->save()) {
                return $this->response(false, "Update Data Failed.", []);
            }

            return $this->response(true, null, []);
        } catch (\Exception $errors) {
            return $this->response(false, "Server Error.", [$errors->getMessage()]);
        }
    }

    public function deleteDataRecord(Request $request)
    {
        // check data id
        if (Data::where("id", $request->id)->where("user_id", ApiSecretToken::getUserIdFromToken($request->api_secret))->count() === 0) {
            return $this->response(false, "Data id dont have relation with the user.", []);
        }

        // delete data record
        try {
            $data = Data::find($request->id);
            if (!$data->delete()) {
                return $this->response(false, "Delete Data Failed.", []);
            }

            return $this->response(true, null, []);
        } catch (\Exception $errors) {
            return $this->response(false, "Server Error.", [$errors->getMessage()]);
        }
    }

    public function getDataByCategory(Request $request)
    {
        // validation
        $validatorArray = [
            "category_id" => "required|numeric",
        ];

        $validation = Validator::make($request->all(), $validatorArray);
        if ($validation->fails()) {
            return $this->response(false, "Please fill the required fields.", $validation->errors()->toArray());
        }

        // check category id
        if (Category::where("id", $request->category_id)->where("user_id", ApiSecretToken::getUserIdFromToken($request->api_secret))->count() === 0) {
            return $this->response(false, "Category id dont have relation with the user.", []);
        }

        // create client data
        try {
            $data = Data::where("category_id", $request->category_id)->where("user_id", ApiSecretToken::getUserIdFromToken($request->api_secret))->first();

            foreach ($data as $key => $value) {
                $data[$key]['data_body'] = unserialize($data[$key]['data_body']);
            }

            return $this->response(true, null, $data);
        } catch (\Exception $errors) {
            return $this->response(false, "Server Error.", [$errors->getMessage()]);
        }
    }

    public function getDataById(Request $request)
    {
        // check data id
        if (Data::where("id", $request->id)->where("user_id", ApiSecretToken::getUserIdFromToken($request->api_secret))->count() === 0) {
            return $this->response(false, "Data not found or Category id dont have relation with the user.", []);
        }

        // create client data
        try {
            $data = Data::find($request->id);

            $data['data_body'] = unserialize($data['data_body']);

            return $this->response(true, null, $data);
        } catch (\Exception $errors) {
            return $this->response(false, "Server Error.", [$errors->getMessage()]);
        }
    }

    /**
     * Create a standardized response array.
     *
     * This private function generates a standardized response array with a given success status,
     * message, and result data. The response array includes the following keys:
     * - "success": a boolean indicating the success status of the response.
     * - "message": an optional string containing a message (default is null).
     * - "result": an array containing the result data.
     *
     * @param bool $success Indicates whether the operation was successful or not.
     * @param string|null $message An optional message providing additional information (default is null).
     * @param array $result The result data to include in the response.
     * @return array The standardized response array.
     */
    private function response(bool $success, string $message = null, array $result): array
    {
        return [
            "success" => $success,
            "message" => $message,
            "result" => $result
        ];
    }
}
