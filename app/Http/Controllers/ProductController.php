<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Webpatser\Uuid\Uuid;

class ProductController extends Controller
{
    public $successStatus = 200;

    public function create(Request $request)
    {
        $input = $request->all();
        $user = Auth::user();
        $input['prod_uuid'] = Uuid::generate()->string;

        $validator = Validator::make($request->all(), [
            'outlet_uuid' => 'required|string',
            'name' => 'required|string',
            'description' => 'sometimes|required|string',
            'price' => 'required|numeric',
            'quantity' => 'sometimes|integer',
            'hide' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $userRole = DB::table('role_user')->where('user_id', $user->id)
            ->where('outlet_uuid', request('outlet_uuid'))->first();
        if (!$userRole) {
            return response()->json(['error' => 'Unauthorised', 'success' => false], 401);
        }

        $product = Product::create($input);

        $success['success'] = true;
        $success['product'] = $product;

        return response()->json(['response' => $success], $this->successStatus);

    }

    public function all(Request $request)
    {
        $search = $request->get('search');

        $products = [];

        if ($search) {
            $products = Product::where('outlet_uuid', $request->outlet_uuid)
                ->where('name', 'like', "%" . $search . "%")
                ->orwhere('price', 'like', "%" . $search . "%")
                ->with('category')
                ->get();
        } else {
            $products = Product::where('outlet_uuid', $request->outlet_uuid)
                ->with('category')
                ->get();
        }

        $success['success'] = true;
        $success['products'] = $products;

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

        $product = Product::where('outlet_uuid', $request->outlet_uuid)
            ->where('prod_uuid', $uuid)
            ->with('category')
            ->first();

        if (!$product){
            return response()->json(['error' => ['message' => ['Product not found']]], 404);
        }

        $product->sold_quantity = $this->product_sold_count($product->prod_uuid);
        $product->sold_amount = $this->product_sold_amount($product->prod_uuid);

        $success['success'] = true;
        $success['product'] = $product;

        return response()->json(['response' => $success], $this->successStatus);
    }

    public function edit(Request $request, $prodId)
    {
        $input = $request->all();
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'outlet_uuid' => 'required|string',
            'name' => 'sometimes|string',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric',
            'quantity' => 'sometimes|integer',
            'hide' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $userRole = DB::table('role_user')->where('user_id', $user->id)
            ->where('outlet_uuid', request('outlet_uuid'))->first();
        if (!$userRole) {
            return response()->json(['error' => 'Unauthorised', 'success' => false], 401);
        }

        $product = Product::where('prod_uuid', $prodId)->first();
        if (!$product){
            return response()->json(['error' => ['message' => ['Product not found']]], 404);
        }

        !isset($input['name']) ? : $product->name = $input['name'];
        !isset($input['description']) ? : $product->description = $input['description'];
        !isset($input['price']) ? : $product->price = $input['price'];
        !isset($input['quantity']) ? : $product->quantity = $input['quantity'];
        !isset($input['hide']) ? : $product->hide = $input['hide'];
        $product->save();

        $success['success'] = true;
        $success['product'] = $product;

        return response()->json(['response' => $success], $this->successStatus);

    }

    public function product_sold_count($prodId){
        $count = DB::table('transaction_product')
            ->where('prod_uuid', $prodId)
            ->count();

        return $count;
    }

    public function product_sold_amount($prodId){
        $total = DB::table('transaction_product')
            ->where('prod_uuid', $prodId)
            ->sum('total');

        return $total;
    }
}
