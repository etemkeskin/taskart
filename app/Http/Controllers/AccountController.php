<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Account;
use Illuminate\Support\Str;

class AccountController extends Controller
{

    public function register(Request $request){

        $params = $request->all();

        $validator = Validator::make($params, [
            'name' => 'required',
            'password' => 'required',
            'email' => 'required|email',
            'phone' => 'required'
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

        try{
            $params['password'] = md5($params['password']);
            $account = new Account($params);
            $account->api_key = $this->generateToken();
            $account->api_secret = $this->generateToken();
            $account->save();
        }catch(\Exception $e){
            \Log::error('Error when registering new account : ' . $e->getMessage());
            $response['status'] = false;
            $response['error'] = 'An error occured';
            return $response;
        }
        $response['status'] = true;
        $response['message'] = 'Account created';
        return response()->json($response, 201);
    }



    public function login(Request $request){
        
        $params = $request->all();

        $validator = Validator::make($params, [
            'email' => 'required',
            'password' => 'required'
        ]);

        $response = [
            'status' => false,
            'error' => null,
            'errorType' => null,
            'data' => null
        ];

        
        //if validator fails, return response with false status
        if($validator->fails()){
           
            $response['error'] = $validator->errors()->first();
            $response['errorType'] = 'required';
            return $response;
        }
        
        try{
            $account = Account::where('email', $params['email'])->first();
            $response['status'] = true;

            return response()->json($response, 200)
            ->header('api-key' ,$account->api_key )
            ->header('api-secret' , $account->api_secret);
            

        }catch(\Exception $e){
            \Log::error('login error : ' . $e->getMessage());
            $response['error'] = 'Girilen şifre kullanıcı şifresi ile uyuşmamaktadır.';
            $response['errorType'] = 'password';
            return $response;
        }

    }


    private function generateToken()
    {
        $apiKey = Str::random(40);

        while (Account::where('api_key', $apiKey)->count() > 0) {
            $apiKey = str_random(40);
        }

        return $apiKey;
    }


}
