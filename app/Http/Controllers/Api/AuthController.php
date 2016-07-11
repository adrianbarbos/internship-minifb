<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validator->fails()) {
            return response([
                'errors' => $validator->messages()
            ], 400);
        }

        if(Auth::attempt($request->all())) {
            return Auth::user();
        }

        return response([
            'messages' => 'Incorrect email or password.'
        ], 422);
    }

    public function register(Request $request) {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'surname' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        if($validator->fails()) {
            return response([
                'errors' => $validator->messages()
            ], 400);
        }

        if(User::whereEmail($request->input('email'))->exists()) {
            return response([
                'message' => 'The email has already been taken.'
            ], 409);
        }

        $user = User::create($request->all());

        return response([
            'data' => $user
        ], 201);
    }

}
