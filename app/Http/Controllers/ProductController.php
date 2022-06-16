<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Product;
use App\Models\Account;

class ProductController extends Controller
{
    public function create(Request $request){

        $params = $request->all();

        $validator = Validator::make($params['product'], [
            'title' => 'required',
            'description' => 'required',
            'price' => 'required'
        ]);

        $response = [
            'status' => false,
            'error' => null,
            'errorType' => null,
            'data' => null
        ];

        if($validator->fails()){

            $response['error'] = $validator->errors()->first();
            $response['errorType'] = 'required';
            return $response;
        }

        $api_key = $request->header('api-key');

        $account = Account::where('api_key', $api_key)->first();

        if ($account == null ) {
            $response['error'] = 'Kullanıcı bulunamadı.';
            $response['errorType'] = 'notfound';
            return $response;
        }
          
        $product_json = $request->getContent();
        $hash = hash_hmac('sha256', $product_json, $account->api_secret);

        if ($request->header('hash') == $hash ) {
            try{
                $product = new Product($params['product']);
                $product->account_id = $account->id;
                $product->save();
            }catch(\Exception $e){
                \Log::error('Error when creating product : ' . $e->getMessage());
                $response['status'] = false;
                $response['error'] = 'An error occured'. $e->getMessage();
                return $response;
            }
        }else{
            $response['status'] = false;
            $response['error'] = 'Invalid data';
            return $response;
        }

        $response['status'] = true;
        $response['error'] = 'product created';
        return response()->json($response, 201);

    }
}
