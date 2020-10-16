<?php

namespace App\Http\Controllers;

use App\Token;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ]);

        if(!$validator->fails()){
            $user = User::where('username', $request->username)->first();
            $password = md5($request->password);
            if($user->password === $password){
                $token = new Token;
                $token->user_id = $user->id;
                $token->token = $password;
                if($token->save()){
                    return response()->json([
                        'token' => $password
                    ], 200);
                }
            }
        }

        return response()->json([
            'message' => 'invalid login'
        ], 422);
    }

    public function logout(Request $request){
        if(Token::where('token', $request->token)->delete()){
            return response()->json([
                'message' => 'logout success'
            ], 200);
        }else{
            return response()->json([
                'message' => 'unauthorized user'
            ], 401);
        }
    }
}
