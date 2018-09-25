<?php

namespace App\Http\Controllers;

use App\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Webpatser\Uuid\Uuid;

class CustomerController extends Controller
{
    public $successStatus = 200;

    public function create(Request $request){

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|email|max:255|unique:customers',
            'password' => 'required|min:6',
            'confirm_password' => 'required|same:password'
        ]);

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 401);
        }

        $input = $request->all();
        $input['cus_uuid'] = Uuid::generate()->string;
        $input['password'] = bcrypt($input['password']);

        $customer = Customer::create($input);

        $success['success'] = true;
        $success['access_token'] = $customer->createToken('customer')->accessToken;
        $success['customer'] = $customer;

        return response()->json(['response' => $success], $this->successStatus);
    }

    public function all(){
        return Customer::all();
    }
}
