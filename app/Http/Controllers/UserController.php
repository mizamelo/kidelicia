<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;

use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['code' => 1 ,'error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['code' => 2 ,'error' => 'could_not_create_token'], 500);
        }

        $User = Auth::user();

        if($User->status == "inativo") {
            return response()->json(config('status.8'), 200);
        }

        $code = 0;

        $User->avatar = "/storage/" . $User->avatar;
        $User->role_id = $User->role_id == 2 ? "Vendedor" : ($User->role_id == 3 ? "Gestor" : "Desconhecido");

        return response()->json(compact('code','token', 'User'));
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user','token'),201);
    }

    public static function getAuthenticatedUser()
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(config('status.6'), 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(config('status.3'), 200);

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(config('status.2'), 200);

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(config('status.5'), 200);

        }

        return $user;
    }
}
