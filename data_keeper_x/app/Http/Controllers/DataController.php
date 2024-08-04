<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Data;
use Illuminate\Http\Request;
use App\Classes\ResponseBodyBuilder;
use App\Classes\ApiResponse\ApiResponse;
use Illuminate\Support\Facades\Validator;

class DataController extends Controller
{
    public function createData(Request $request)
    {
        // validation
        $validatorArray = [
            "user_id" => "required",
            "category_id" => "required",
            "data_body" => "required",
        ];

        $validation = Validator::make($request->all(), $validatorArray);
        if ($validation->fails()) {
            return ApiResponse::failure()->errors($validation->errors()->toArray())->toArray();
        }

        // create data
        try {
            $data = new Data();
            $data->user_id = $request->user_id;
            $data->category_id = $request->category_id;
            $data->data_body = serialize($request->data_body);
            if (!$data->save()) {
                return ApiResponse::failure()->toArray();
            }

            return ApiResponse::success()->toArray();
        } catch (\Exception $errors) {
            return ApiResponse::failure()->errors([$errors->getMessage()])->toArray();
        }
    }

    public function updateDataBody(Request $request)
    {
        // validation
        $validatorArray = [
            "data_body" => "required",
        ];

        $validation = Validator::make($request->all(), $validatorArray);
        if ($validation->fails()) {
            return ApiResponse::failure()->errors($validation->errors()->toArray())->toArray();
        }

        // update data
        try {
            $data = Data::find($request->id);
            $data->data_body = serialize($request->data_body);
            if (!$data->save()) {
                return ApiResponse::failure()->toArray();
            }

            return ApiResponse::success()->toArray();
        } catch (\Exception $errors) {
            return ApiResponse::failure()->errors([$errors->getMessage()])->toArray();
        }
    }

    public function updateDataCategory(Request $request)
    {
        // validation
        $validatorArray = [
            "category_id" => "required",
        ];

        $validation = Validator::make($request->all(), $validatorArray);
        if ($validation->fails()) {
            return ApiResponse::failure()->errors($validation->errors()->toArray())->toArray();
        }

        // update data category
        try {
            $data = Data::where("id", $request->id)->where("category_id", $request->category_id)->first();
            $data->category_id = $request->category_id;
            if (!$data->save()) {
                return ApiResponse::failure()->toArray();
            }

            return ApiResponse::success()->toArray();
        } catch (\Exception $errors) {
            return ApiResponse::failure()->errors([$errors->getMessage()])->toArray();
        }
    }

    public function deleteData(Request $request)
    {
        // delete data
        try {
            $data = Data::find($request->id);
            if (!$data->delete()) {
                return ApiResponse::failure()->toArray();
            }

            return ApiResponse::success()->toArray();
        } catch (\Exception $errors) {
            return ApiResponse::failure()->errors([$errors->getMessage()])->toArray();
        }
    }

    public function getDataList(Request $request)
    {
        $data = Data::query();

        // filter by user id
        if ($request->has("user_id")) {
            $data->where("user_id", $request->user_id);
        }

        // filter by category id
        if ($request->has("category_id")) {
            $data->where("category_id", $request->category_id);
        }

        $data = $data->get();

        foreach ($data as $key => $value) {
            $data[$key]['data_body'] = unserialize($data[$key]['data_body']);
        }

        return ApiResponse::success()->data($data ?? [])->toArray();
    }

    public function getDataById(Request $request)
    {
        $data = Data::find($request->id);
        $data['data_body'] = unserialize($data['data_body']);
        return ApiResponse::success()->data($data)->toArray();
    }
}
