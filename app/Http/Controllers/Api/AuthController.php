<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class AuthController extends Controller
{
    /**
     * Picture upload path
     *
     * @var string
     */
    protected $uploadPath = 'uploads/';

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

    public function update(User $user,Request $request) {

        //Update password
        if($request->has('old_password') && $request->has('password')) {
            if(!Hash::check($request->input('old_password'), $user->password)) {
                return response([
                    'message' => 'The provided old password was incorrect.'
                ], 403);
            }
            $user->password = $request->input('password');
        }

        //Update email
        if($request->has('email')) {
            $email = $request->input('email');

            if($user->email != $email && User::whereEmail($email)->exists()) {
                return response([
                    'message' => "The email '$email' has already been taken."
                ], 409);
            }

            $user->email = $email;
        }

        //Update
        if($request->hasFile('avatar')) {
            if($user->avatar != null) { File::delete($user->getOriginal('avatar')); }
            $user->avatar = $this->uploadAvatar($request->file('avatar'));
        }

        $user->save();

        return response([
            'data' => $user
        ]);
    }

    /**
     * Upload avatar
     *
     * @param $avatar
     * @return string
     */
    private function uploadAvatar($avatar) {
        if(!File::exists($this->uploadPath)) {
            File::makeDirectory($this->uploadPath, $mode = 0777, true, true);
        }

        $imageName = time() . rand() . '.' . $avatar->getClientOriginalExtension();

        Image::make($avatar->getRealPath())->fit(300, 300)->save($this->uploadPath . $imageName);

        return $this->uploadPath . $imageName;
    }

}
