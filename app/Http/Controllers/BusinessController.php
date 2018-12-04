<?php

namespace App\Http\Controllers;

use App\Business;
use App\Outlet;
use App\Role;
use function GuzzleHttp\Psr7\build_query;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Webpatser\Uuid\Uuid;

class BusinessController extends Controller
{
    public $successStatus = 200;

    public function create(Request $request){
        dd(Auth::user());
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'sometimes|required|string',
        ]);

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 401);
        }

        $input = $request->all();
        $user = Auth::user();
        $input['biz_uuid'] = Uuid::generate()->string;
        $outlet = null;

        $business = new Business();
        $business->biz_uuid = $input['biz_uuid'];
        $business->user_id = $user['id'];
        $business->name = $input['name'];
        $business->description = $input['description'] ? $input['description'] : '';
        if ($business->save()){
            $outlet = $this->createOutlet($input);
        }

        $result = [
          'business' => $business,
          'outlet' => $outlet
        ];

        $success['success'] = true;
        $success['data'] = $result;

        return response()->json(['response' => $success], $this->successStatus);

    }

    public function createOutlet($data){

        $data['outlet_uuid'] = Uuid::generate()->string;

        $outlet = new Outlet();
        $outlet->outlet_uuid = $data['outlet_uuid'];
        $outlet->biz_uuid = $data['biz_uuid'];
        $outlet->name = $data['name'];
        $outlet->description = $data['description'] ? $data['description'] : '';
        $outlet->address = $data['address'] ? $data['address'] : '';
        $outlet->country = $data['country'] ? $data['country'] : '';
        $outlet->city = $data['city'] ? $data['city'] : '';
        $outlet->district = $data['district'] ? $data['district'] : '';
        $outlet->code = $this->ranNum();
        $outlet->save();

        $outlet->role = $this->addRole($outlet);

        return $outlet;
    }

    public function addRole($outlet){
        $user = Auth::user();
        $role = Role::where('name', 'Admin')->first();

        return $user->role()->attach($role->id, ['user_type' => 'App\User','outlet_uuid' => $outlet->outlet_uuid]);
    }

    public function removeRole($data){
        $user = Auth::user();
        $role = Role::find(1);
        return $user->detachRole($role);
    }

    private function ranNum(){
        return rand(00000000, 99999999);
    }
}