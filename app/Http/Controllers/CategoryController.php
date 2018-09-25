<?php

namespace App\Http\Controllers;

use App\Business;
use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Webpatser\Uuid\Uuid;

class CategoryController extends Controller
{
    public $successStatus = 200;

    public function create(Request $request){
        $input = $request->all();
        $user = Auth::user();
        $input['cate_uuid'] = Uuid::generate()->string;

        $validator = Validator::make($request->all(), [
            'biz_uuid' => 'required|string',
            'outlet_uuid' => 'required|string',
            'name' => 'required|string',
            'description' => 'sometimes|required|string',
        ]);

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 401);
        }

        $biz = Business::where('biz_uuid', $input['biz_uuid'])->where('user_id', $user->id);
        if(!$biz){
            return response()->json(['message' => 'Business not found'], 404);
        }

        $category = Category::create($input);

        $success['success'] = true;
        $success['category'] = $category;

        return response()->json(['response' => $success], $this->successStatus);
    }

    public function all(Request $request){
        $search = $request->get('search');

        $categories = [];

        if($search){
            $categories = Category::where('outlet_uuid', $request->outlet_uuid)
                ->where('name', 'like', "%".$search."%")
                ->with('children')
                ->with('parent')
                ->get();
        }else {
            $categories = Category::where('outlet_uuid', $request->outlet_uuid)
                ->with('children')
                ->with('parent')
                ->get();
        }

        $success['success'] = true;
        $success['categories'] = $categories;

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

        $category = Category::where('outlet_uuid', $request->outlet_uuid)
            ->where('cate_uuid', $uuid)
            ->with('children')
            ->with('parent')
            ->first();

        if (!$category){
            return response()->json(['error' => ['message' => ['Category not found']]], 404);
        }

        $category->sold_quantity = $this->category_sold_count($category->cate_uuid);
        $category->sold_amount = $this->category_sold_amount($category->cate_uuid);

        $success['success'] = true;
        $success['category'] = $category;

        return response()->json(['response' => $success], $this->successStatus);
    }

    public function edit(Request $request, $uuid)
    {
        $input = $request->all();
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'outlet_uuid' => 'required|string',
            'name' => 'sometimes|string',
            'description' => 'sometimes|string',
            'parent_id' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $userRole = DB::table('role_user')->where('user_id', $user->id)
            ->where('outlet_uuid', request('outlet_uuid'))->first();
        if (!$userRole) {
            return response()->json(['error' => 'Unauthorised', 'success' => false], 401);
        }

        $category = Category::where('cate_uuid', $uuid)->first();
        if (!$category){
            return response()->json(['error' => ['message' => ['Category not found']]], 404);
        }

        !isset($input['name']) ? : $category->name = $input['name'];
        !isset($input['description']) ? : $category->description = $input['description'];
        !isset($input['parent_id']) ? : $category->parent_id = $input['parent_id'];
        $category->save();

        $success['success'] = true;
        $success['category'] = $category;

        return response()->json(['response' => $success], $this->successStatus);
    }

    public function category_sold_count($uuid){
        $count = DB::table('transaction_product')
            ->join('product', 'product.prod_uuid', '=', 'transaction_product.prod_uuid')
            ->where('product.cate_uuid', $uuid)
            ->count();

        return $count;
    }

    public function category_sold_amount($uuid){
        $total = DB::table('transaction_product')
            ->join('product', 'product.prod_uuid', '=', 'transaction_product.prod_uuid')
            ->where('product.cate_uuid', $uuid)
            ->sum('total');

        return $total;
    }
}
