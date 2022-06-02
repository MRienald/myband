<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;   

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validasi = Validator::make($request->all(), [
            'username'     => 'required',
            'password'  => 'required|min:8',
        ]);

        if($validasi->fails()){
            return $this->error($validasi->errors()->first());
        }

        $user = User::where('username', $request->username)
            ->orWhere('email', $request->username)
            ->orWhere('phone', $request->username)
            ->first();
        if($user){
            if(password_verify($request->password, $user->password)){
                return $this->success($user);
            } else {
                return $this->error('Password Salah');
            }

            return $this->success($user);
        }
        return $this->error('User tidak terdaftar');
    }

    public function register(Request $request)
    {
        $validasi = Validator::make($request->all(), [
            'username'  => 'required|unique:users',
            'name'      => 'required',
            'email'     => 'required|unique:users',
            'phone'     => 'required|unique:users',
            'birthday'  => 'required',
            'address'   => 'required',
            'password'  => 'required|min:8',
        ]);

        if($validasi->fails()){
            return $this->error($validasi->errors()->first());
        }

        $user   =   User::create(array_merge($request->all(), [
            'password'      => bcrypt($request->password)
        ]));

        if($user){
            return $this->success($user);
        } else {
            return $this->error('Terjadi Kesalahan');
        }

    }

    public function success($data, $message = 'success')
    {
        return response()->json([
            'code'      =>  200,
            'message'   =>  $message,
            'data'      =>  $data
        ]);
    }

    public function error($message)
    {
        return response()->json([
            'code'      =>  400,
            'message'   =>  $message
        ], 400);
    }
}
