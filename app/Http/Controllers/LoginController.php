<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Torann\GeoIP\GeoIP;

class LoginController extends Controller
{
    public $successStatus = 200;

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(){

        if(Auth::attempt(['email' => \request('email'), 'password' => \request('password')])){

            $user = Auth::user();

            $userRole = DB::table('role_user')->where('user_id', $user->id)
                ->where('outlet_uuid', request('outlet_uuid') )->first();
            if(!$userRole){
                return response()->json(['error' => 'Unauthorised', 'success' => false], 401);
            }

            $user->outlet_uuid = request('outlet_uuid');
            $success['success'] = true;
            $success['access_token'] = $user->createToken('oauth_clients')->accessToken;
            $success['user'] = $user;

            return response()->json(['response' => $success], $this->successStatus);
        }else{
            return response()->json(['error' => 'Unauthorised', 'success' => false], 401);
        }
    }

    public function user(){

        if (Auth::user()){
            $user = Auth::user();

            $success['success'] = true;
            $success['user'] = $user;

            return response()->json(['response' => $success], $this->successStatus);
        }else{
            return response()->json(['error' => 'Unauthorised', 'success' => false], 401);
        }

    }

    public function logout(Request $request){
        if (Auth::user()){
            $request->user()->token()->revoke();

            $success['success'] = true;
            $success['message'] = 'user logged out';

            return response()->json(['response' => $success], $this->successStatus);
        }
        return response()->json(['error' => 'unsuccessful try again', 'success' => false], 401);
    }
}
