<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Classes\ApiResponse\ApiResponse;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function createCategory(Request $request)
    {
        // validation
        $validatorArray = [
            "user_id" => "required|numeric",
            "name" => "required",
            "description" => "required",
        ];

        $validation = Validator::make($request->all(), $validatorArray);
        if ($validation->fails()) {
            return ApiResponse::failure()->errors($validation->errors()->toArray())->toArray();
        }

        // create category
        try {
            $category = new Category();
            $category->user_id = $request->user_id;
            $category->name = trim($request->name);
            $category->description = trim($request->description);
            if (!$category->save()) {
                return ApiResponse::failure()->toArray();
            }

            return ApiResponse::success()->toArray();
        } catch (\Exception $errors) {
            return ApiResponse::failure()->errors([$errors->getMessage()])->toArray();
        }
    }

    public function updateCategory(Request $request)
    {
        // validation
        $validatorArray = [
            "name" => "required",
            "description" => "required",
        ];

        $validation = Validator::make($request->all(), $validatorArray);
        if ($validation->fails()) {
            return ApiResponse::failure()->errors($validation->errors()->toArray())->toArray();
        }

        // update category
        try {
            $category = Category::find($request->id);
            $category->name = trim($request->name);
            $category->description = trim($request->description);
            if (!$category->save()) {
                return ApiResponse::failure()->toArray();
            }

            return ApiResponse::success()->toArray();
        } catch (\Exception $errors) {
            return ApiResponse::failure()->errors([$errors->getMessage()])->toArray();
        }
    }

    public function deleteCategory(Request $request)
    {
        // delete category
        try {
            $category = Category::find($request->id);
            $category->data()->delete();
            if (!$category->delete()) {
                return ApiResponse::failure()->toArray();
            }

            return ApiResponse::success()->toArray();
        } catch (\Exception $errors) {
            return ApiResponse::failure()->errors([$errors->getMessage()])->toArray();
        }
    }

    public function getCategoryByUserId(Request $request)
    {
        return ApiResponse::success()->data(Category::where("user_id", $request->user_id)->get() ?? [])->toArray();
    }
}
