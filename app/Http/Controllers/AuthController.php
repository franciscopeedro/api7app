<?php

namespace App\Http\Controllers;

use App\Models\Cadastro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'senha' => 'required',
        ]);

        $credentials = $request->only('email', 'senha');

        $user = Cadastro::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['senha'], $user->senha)) {
            return response()->json(['message' => 'Invalid email or password'], 401);
        }

        $token = Str::random(60);
        $user->api_token = $token;
        $user->save();

        return response()->json(['token' => $token]);
    }
}
