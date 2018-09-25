<?php

namespace App\Http\Controllers;

use App\Role;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Webpatser\Uuid\Uuid;

class UserController extends Controller
{
    public $successStatus = 200;

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'outlet_uuid' => 'required|string',
            'name' => 'required|string',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|min:6',
            'confirm_password' => 'required|same:password',
            'role_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $input = $request->all();
        $user = Auth::user();
        $input['password'] = bcrypt($input['password']);

        $userRole = DB::table('role_user')->where('user_id', $user->id)
            ->where('outlet_uuid', request('outlet_uuid'))->first();
        if (!$userRole) {
            return response()->json(['error' => 'Unauthorised', 'success' => false], 401);
        }

        $newUser = User::create($input);

        $this->addRole($newUser, $input['role_id'], $input['outlet_uuid']);

        $newUser->role;
        $success['success'] = true;
        $success['user'] = $newUser;

        return response()->json(['response' => $success], $this->successStatus);

    }

    public function all(Request $request)
    {

        $outlet = $request->outlet_uuid;
        $search = $request->get('search');

        $users = [];

        if ($search) {
            $users = DB::table('users')
                ->join('role_user', 'role_user.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'role_user.role_id')
                ->select('users.id', 'users.name', 'users.email', 'roles.name AS role_name', 'role_user.outlet_uuid')
                ->where('role_user.outlet_uuid', '=', $outlet)
                ->where('users.name', 'like', "%" . $search . "%")
                ->orWhere('roles.name', 'like', "%" . $search . "%")
                ->orWhere('users.email', 'like', "%" . $search . "%")
                ->get();
        } else {
            $users = DB::table('users')
                ->join('role_user', 'role_user.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'role_user.role_id')
                ->select('users.id', 'users.name', 'users.email', 'roles.name AS role_name', 'role_user.outlet_uuid')
                ->where('role_user.outlet_uuid', '=', $outlet)
                ->get();
        }


        $success['success'] = true;
        $success['users'] = $users;

        return response()->json(['response' => $success], $this->successStatus);
    }

    public function get(Request $request, $uuid)
    {
        $validator = Validator::make($request->all(), [
            'outlet_uuid' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $user = DB::table('users')
            ->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('users.id', 'users.name', 'users.email', 'roles.id AS role_id', 'roles.name AS role_name', 'role_user.outlet_uuid')
            ->where('role_user.outlet_uuid', '=', $request->outlet_uuid)
            ->where('users.id', '=', $uuid)
            ->first();

        if (!$user){
            return response()->json(['error' => ['message' => ['User not found']]], 404);
        }

        $user->transaction_count = $this->user_transaction_count($user->id);
        $user->transaction_amount = $this->user_transaction_amount($user->id);
        $user->transaction_items = $this->user_transaction_items($user->id);

        $success['success'] = true;
        $success['user'] = $user;

        return response()->json(['response' => $success], $this->successStatus);
    }

    public function addRole(User $user, $role_id, $outlet_uuid)
    {
        $role = Role::where('id', $role_id)->first();

        return $user->role()->attach($role->id, ['user_type' => 'App\User', 'outlet_uuid' => $outlet_uuid]);
    }

    public function user_transaction_count($uuid){
        $count = Transaction::where('user_id', $uuid)
            ->count();
        return $count;
    }

    public function user_transaction_amount($uuid){
        $count = Transaction::where('user_id', $uuid)
            ->sum('total');
        return $count;
    }

    public function user_transaction_items($uuid){
        $count = Transaction::where('user_id', $uuid)
            ->sum('quantity');
        return $count;
    }
}
