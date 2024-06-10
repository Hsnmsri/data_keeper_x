<?php

namespace App\Http\Controllers;

use App\Models\Data;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isJson;

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
            $dataRecord->data = serialize($request->data);
            // check relation id
            if ($request->has("relation_id")) {
                $dataRecord->relation_id = $request->relation_id;
            }
            // check name
            if ($request->has("name")) {
                $dataRecord->name = $request->name;
            }
            // check signature
            if ($request->has("signature")) {
                $dataRecord->signature = $request->signature;
            }

            // save record
            if (!$dataRecord->save()) {
                return response("save failed!");
            }
        } catch (Exception $error) {
            return response($error, 500);
        }

        return response(true);
    }
}
