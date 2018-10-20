<?php

namespace App\Http\Controllers;

use App\Outlet;
use App\OutletSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OutletSettingController extends Controller
{
    public $successStatus = 200;

    public function create(){
        $input = request()->all();

        $validator = Validator::make($input, [
            'outlet_uuid' => 'required|string',
            'facebook' => 'required|string',
            'currency' => 'required|string',
            'email' => 'required|email',
            'tags' => 'required|array',
            'open_hours' => 'required|array'
        ]);

        //$input['tags'] = json_encode($input['tags']);
        //$input['open_hours'] = json_encode($input['open_hours']);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $outlet = Outlet::where('outlet_uuid', $input['outlet_uuid'])->first();
        if (! $outlet){
            return response()->json(['message' => 'Outlet not found'], 404);
        }

        $outletSet = OutletSetting::where('outlet_uuid', $input['outlet_uuid'])->first();
        if ($outletSet){
            $outletSet->delete();
        }

        $setting = OutletSetting::create($input);

        $success['success'] = true;
        $success['setting'] = $setting;

        return response()->json(['response' => $success], $this->successStatus);


    }

    public function upload(Request $request){
        if($request->hasFile('profile_image')) {

            //get filename with extension
            $filenamewithextension = $request->file('profile_image')->getClientOriginalName();

            //get filename without extension
            $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);

            //get file extension
            $extension = $request->file('profile_image')->getClientOriginalExtension();

            //filename to store
            $filenametostore = $filename.'_'.time().'.'.$extension;

            //Upload File to s3
            $result = Storage::disk('s3')->put($filenametostore, fopen($request->file('profile_image'), 'r+'), 'public');

            //Store $filenametostore in the database
            $url = null;
            if($result){
                $url = Storage::disk('s3')->url($filenametostore);
            }
            return $url;
        }
    }
}
