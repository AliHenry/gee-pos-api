<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Outlet;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Webpatser\Uuid\Uuid;

class CustomerController extends Controller
{
    public function __construct()
    {
        Config::set( 'jwt.user', 'App\Customer' );
        Config::set( 'auth.providers.users.model', Customer::class );
    }
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
        if ($customer){
            $credentials = $request->only('email', 'password');
            $success['access_token'] = JWTAuth::attempt($credentials);
            //$success['access_token'] = $this->fromUser($customer);
        }

        $success['success'] = true;
        //$success['access_token'] = JWTAuth::fromUser($customer);;
        $success['customer'] = $customer;

        return response()->json(['response' => $success], $this->successStatus);
    }


    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if ( ! $token = JWTAuth::attempt($credentials)) {
            return response([
                'status' => 'error',
                'error' => 'invalid.credentials',
                'msg' => 'Invalid Credentials.'
            ], 400);
        }
        return response([
            'status' => 'success',
            'token' => $token
        ]);
    }

    public function all(){
        return Customer::all();
    }

    public function likeProduct(Request $request){
        $user = JWTAuth::parseToken()->toUser();
        $validator = Validator::make($request->all(), [
            'prod_uuid' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 401);
        }

        $product = Product::find($request->prod_uuid);
        if (!$product){
            return response()->json(['message' => 'Product not found'], 404);
        }

        $liked = $user->product_likes->contains($product);

        if ($liked){
            $user->product_likes()->detach($product->prod_uuid);

            $user->product_likes;
            $success['success'] = true;
            $success['user'] = $user;

            return response()->json(['response' => $success], $this->successStatus);
        }

        $user->product_likes()->attach($product->prod_uuid);

        $user->product_likes;
        $success['success'] = true;
        $success['user'] = $user;

        return response()->json(['response' => $success], $this->successStatus);

    }

    public function followOutlet(Request $request){
        $user = JWTAuth::parseToken()->toUser();
        $validator = Validator::make($request->all(), [
            'outlet_uuid' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 401);
        }

        $outlet = Outlet::find($request->outlet_uuid);
        if (!$outlet){
            return response()->json(['message' => 'Outlet not found'], 404);
        }

        $followed = $user->follow_outlet->contains($outlet);

        if ($followed){
            $user->follow_outlet()->detach($outlet->outlet_uuid);

            $user->follow_outlet;
            $success['success'] = true;
            $success['user'] = $user;

            return response()->json(['response' => $success], $this->successStatus);
        }

        $user->follow_outlet()->attach($outlet->outlet_uuid);

        $user->follow_outlet;
        $success['success'] = true;
        $success['user'] = $user;

        return response()->json(['response' => $success], $this->successStatus);

    }
}
