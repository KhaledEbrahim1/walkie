<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
        ]);

        if ($validator->fails()) {
            $res = [
                'Success' => false,
                'Message' => $validator->errors()->first()
            ];
            return response()->json($res, 200);
        }
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $message = $user;
        $message['token'] = $user->createToken('Token')->plainTextToken;

        $res = [
            'Success' => true,
            'data' => $message,
            'Message' => 'user register successfully'
        ];
        return response()->json($res, 201);


    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            $res = [
                'Success' => false,
                'Message' => $validator->errors()->first()
            ];
            return response()->json($res, 200);
        }
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            $user = Auth::user();

            $message = $user;
            $message['token'] = $user->createToken('Token')->plainTextToken;

            $res = [
                'Success' => true,
                'data' => $message,
                'Message' => 'you are now login',
            ];
            return response()->json($res, 200);
        } else {
            return response()->json(['Message' => 'wrong password or email'], 200);
        }

    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();

        return response()->json(['Message' => 'Logged out successfully']);
    }

}
