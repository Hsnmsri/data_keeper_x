<?php

namespace App\Http\Controllers;

use App\Models\Data;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DataController extends Controller
{
    public function Create(Request $request)
    {
        // validating data
        $validation = Validator::make($request->all(), [
            "relation_id" => "numeric",
            "data" => "required",
        ]);
        if ($validation->fails()) {
            return response($validation->messages(), 400);
        }

        //create records
        try {
            $dataRecord = new Data();
            $dataRecord->data = $request->data;
            if ($request->has("relation_id")){

            }
        } catch (Exception $error) {
            return response($error, 500);
        }
    }
}
