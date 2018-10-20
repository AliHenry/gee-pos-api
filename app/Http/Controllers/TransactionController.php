<?php

namespace App\Http\Controllers;

use App\Business;
use App\Outlet;
use App\Product;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Webpatser\Uuid\Uuid;

class TransactionController extends Controller
{
    public $successStatus = 200;

    public function create(Request $request)
    {
        $input = $request->all();
        $user = Auth::user();
        $input['trans_uuid'] = Uuid::generate()->string;
        $input['user_id'] = $user->id;

        $validator = Validator::make($request->all(), [
            'biz_uuid' => 'required|string',
            'outlet_uuid' => 'required|string',
            'total' => 'required|numeric',
            'quantity' => 'required|integer',
            'items' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $userRole = DB::table('role_user')->where('user_id', $user->id)
            ->where('outlet_uuid', request('outlet_uuid'))->first();
        if (!$userRole) {
            return response()->json(['error' => 'Unauthorised', 'success' => false], 401);
        }

        $biz = Business::find(request('biz_uuid'));
        if (!$biz) {
            return response()->json(['error' => 'Business not found', 'success' => false], 401);
        }

        $outlet = Outlet::where('biz_uuid', request('biz_uuid'))
            ->where('outlet_uuid', request('outlet_uuid'))
            ->first();

        if (!$outlet) {
            return response()->json(['error' => 'Outlet not found', 'success' => false], 401);
        }

        $transaction = Transaction::create($input);

        $transaction->trans_uuid = $input['trans_uuid'];
        if ($transaction) {
            $this->insert_trans_productions($transaction, $input['items']);
        }
        $transaction->products;
        $success['success'] = true;
        $success['transaction'] = $transaction;

        return response()->json(['response' => $success], $this->successStatus);

    }

    public function all(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'outlet_uuid' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $search = $request->get('search');
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');

        $transactions = Transaction::where('outlet_uuid', $request->outlet_uuid)
            ->with('products')
            ->with('sales_person');

        if ($search) {
            $transactions->where('trans_uuid', 'like', "%" . $search . "%")
                ->orwhere('total', 'like', "%" . $search . "%");
        }
        if ($startDate && $endDate) {
            $transactions->whereBetween('created_at', [$startDate, $endDate]);
        }

        $success['success'] = true;
        $success['success'] = $transactions->count();
        $success['transactions'] = $transactions->get();

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

        $transaction = Transaction::where('outlet_uuid', $request->outlet_uuid)
            ->where('trans_uuid', $uuid)
            ->with('products')
            ->with('sales_person')
            ->first();

        if (!$transaction){
            return response()->json(['error' => ['message' => ['Transaction not found']]], 404);
        }

        $success['success'] = true;
        $success['transaction'] = $transaction;

        return response()->json(['response' => $success], $this->successStatus);
    }

    private function insert_trans_productions(Transaction $transaction, $items){
        foreach ($items as $item) {

            $products = Product::where('prod_uuid', $item['prod_uuid'])->first();
            if($products->quantity >= $item['quantity']){
                $products->quantity = $products->quantity - $item['quantity'];
                $products->save();
                $transaction->products()->attach($item['prod_uuid'],
                    [
                        'outlet_uuid' => $products->outlet_uuid,
                        'total' => $item['price'],
                        'quantity' => $item['quantity']
                    ]
                );
            }
        }
    }
}
