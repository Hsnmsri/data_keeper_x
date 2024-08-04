<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Classes\ApiResponse\ApiResponse;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function createRole(Request $request)
    {
        // validation
        $validatorArray = [
            "name" => "required",
            "description" => "required",
            "permissions" => "required",
        ];

        $validation = Validator::make($request->all(), $validatorArray);
        if ($validation->fails()) {
            return ApiResponse::failure()->errors($validation->errors()->toArray())->toArray();
        }

        // check permission list
        if (!is_array($request->permissions)) {
            return ApiResponse::failure()->message("Permissions not array!")->toArray();
        }

        // create role
        try {
            $role = new Role();
            $role->name = trim($request->name);
            $role->description = trim($request->description);
            $role->permissions()->attach($request->permissions);
            if (!$role->save()) {
                return ApiResponse::failure()->toArray();
            }

            return ApiResponse::success()->toArray();
        } catch (\Exception $errors) {
            return ApiResponse::failure()->errors([$errors->getMessage()])->toArray();
        }
    }

    public function updateRoleDetail(Request $request)
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

        // update role
        try {
            $role = Role::find($request->id);
            $role->name = trim($request->name);
            $role->description = trim($request->description);
            if (!$role->save()) {
                return ApiResponse::failure()->toArray();
            }

            return ApiResponse::success()->toArray();
        } catch (\Exception $errors) {
            return ApiResponse::failure()->errors([$errors->getMessage()])->toArray();
        }
    }

    public function updateRolePermissions(Request $request)
    {
        // validation
        $validatorArray = [
            "permissions" => "required",
        ];

        $validation = Validator::make($request->all(), $validatorArray);
        if ($validation->fails()) {
            return ApiResponse::failure()->errors($validation->errors()->toArray())->toArray();
        }

        // check permission list
        if (!is_array($request->permissions)) {
            return ApiResponse::failure()->message("Permissions not array!")->toArray();
        }

        // create role
        try {
            $role = Role::find($request->id);
            $role->permissions()->detach();
            $role->permissions()->attach($request->permissions);
            if (!$role->save()) {
                return ApiResponse::failure()->toArray();
            }

            return ApiResponse::success()->toArray();
        } catch (\Exception $errors) {
            return ApiResponse::failure()->errors([$errors->getMessage()])->toArray();
        }
    }

    public function deleteRole(Request $request)
    {
        // delete role
        try {
            $role = Role::find($request->id);
            $role->permissions()->detach();
            if (!$role->delete()) {
                return ApiResponse::failure()->toArray();
            }

            return ApiResponse::success()->toArray();
        } catch (\Exception $errors) {
            return ApiResponse::failure()->errors([$errors->getMessage()])->toArray();
        }
    }

    public function getRoleList(Request $request)
    {
        return ApiResponse::success()->data(Role::with("permissions")->get() ?? [])->toArray();
    }

    public function getRoleById(Request $request)
    {
        return ApiResponse::success()->data(Role::with("permissions")->find($request->id))->toArray();
    }
}
