<?php

namespace App\Http\Controllers;

use App\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Webpatser\Uuid\Uuid;

class OutletController extends Controller
{
    public $successStatus = 200;

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'sometimes|required|string',
            'address_components' => 'sometimes|required|array',
        ]);

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 401);
        }

        $input = $request->all();
        $input['outlet_uuid'] = Uuid::generate()->string;

        $outlet = new Outlet();
        $outlet->outlet_uuid = $input['outlet_uuid'];
        $outlet->biz_uuid = $input['biz_uuid'];
        $outlet->name = $input['name'];
        $outlet->description = $input['description'] ? $input['description'] : '';
        $outlet->save();

        return $outlet;
    }

    public function checkCode(Request $request){
        $validator = Validator::make($request->all(), [
            'code' => 'required|integer|min:8',
        ]);

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 401);
        }

        $outlet = Outlet::where('code', $request->code)->first();
        if (!$outlet){
            return response()->json(['error' =>['code' => ['Outlet not found']]], 404);
        }

        $success['success'] = true;
        $success['outlet'] = $outlet;

        return response()->json(['response' => $success], $this->successStatus);

    }
}
