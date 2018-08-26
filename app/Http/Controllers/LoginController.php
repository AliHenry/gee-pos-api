<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public $successStatus = 200;

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(){
        if(Auth::attempt(['email' => \request('email'), 'password' => \request('password')])){

            $user = Auth::user();

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
}
