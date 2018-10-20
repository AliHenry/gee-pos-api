<?php

namespace App\Http\Controllers;

use App\Http\Resources\Product;
use Illuminate\Http\Request;

class AllProductController extends Controller
{
    public $successStatus = 200;

    public function all(Request $request)
    {
        $search = $request->get('search');

        $products = Product::all()
            ->with('category');

        if ($search) {
            $products->where('name', 'like', "%" . $search . "%")
                ->orwhere('price', 'like', "%" . $search . "%");
        }
        $success['success'] = true;
        $success['products'] = $products;

        return response()->json(['response' => $success], $this->successStatus);
    }
}
