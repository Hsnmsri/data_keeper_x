<?php

namespace App\Http\Controllers;

use App\Classes\ResponseBodyBuilder;
use App\Models\Data;
use Exception;
use Hamcrest\Type\IsNumeric;
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
            return ResponseBodyBuilder::buildFailureResponse(null, $validation->messages());
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
                return ResponseBodyBuilder::buildFailureResponse("save failed");
            }
        } catch (Exception $error) {
            return ResponseBodyBuilder::buildFailureResponse(null, $error, 500);
        }

        return ResponseBodyBuilder::buildSuccessResponse();
    }

    public function getDataById($id)
    {
        // validating data
        if (!is_numeric($id) || is_null($id) || empty($id)) {
            return ResponseBodyBuilder::buildFailureResponse("id not valid!");
        }

        // get data from db
        $dataRecord = Data::find($id);

        // if record not exist
        if (!$dataRecord) {
            return ResponseBodyBuilder::buildFailureResponse("record id not found!");
        }

        // unserialize data
        $dataRecord->data = unserialize($dataRecord->data);

        return ResponseBodyBuilder::buildSuccessResponse(null, $dataRecord);
    }

    public function getAllData()
    {
        // get all records
        $allRecords = Data::all();

        // unserialize all records
        foreach ($allRecords as $key => $value) {
            $allRecords[$key]->data = unserialize($value->data);
        }

        return ResponseBodyBuilder::buildSuccessResponse(null, $allRecords);
    }

    public function Delete($id)
    {
        // validating data
        if (!is_numeric($id) || is_null($id) || empty($id)) {
            return ResponseBodyBuilder::buildFailureResponse("id not valid!");
        }

        // delete record
        try {
            $dataRecord = Data::find($id);

            // save record
            if (!$dataRecord->delete()) {
                return ResponseBodyBuilder::buildFailureResponse("delete failed");
            }
        } catch (Exception $error) {
            return ResponseBodyBuilder::buildFailureResponse(null, $error, 500);
        }

        return ResponseBodyBuilder::buildSuccessResponse();
    }

    public function Update(Request $request)
    {
        // validating data
        $validation = Validator::make($request->all(), [
            "data_id" => "numeric|required",
            "relation_id" => "numeric",
        ]);
        if ($validation->fails()) {
            return ResponseBodyBuilder::buildFailureResponse(null, $validation->messages());
        }

        // update record
        try {
            $record = Data::find($request->id);
            $record->data = serialize($request->data);
            // check data
            if ($request->has("data")) {
                $record->data = $request->data;
            }
            // check relation id
            if ($request->has("relation_id")) {
                $record->relation_id = $request->relation_id;
            }
            // check name
            if ($request->has("name")) {
                $record->name = $request->name;
            }
            // check signature
            if ($request->has("signature")) {
                $record->signature = $request->signature;
            }

            // save record
            if (!$record->save()) {
                return ResponseBodyBuilder::buildFailureResponse("save failed");
            }
        } catch (Exception $error) {
            return ResponseBodyBuilder::buildFailureResponse(null, $error, 500);
        }
    }
}
