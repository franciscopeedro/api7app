<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Cadastro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    /**
     * Faz login do usuário.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'senha' => 'required',
            'telefone' => 'required'
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

    /**
     * Gera e salva o código OTP no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function getOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'telefone' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $telefone = $request->input('telefone');

        // Generate OTP code (6-digit random number)
        $otpCode = mt_rand(100000, 999999);

        // Save OTP code to the database for the user
        $usuario = Usuario::where('telefone', $telefone)->first();
        $usuario->otp_code = $otpCode;
        $usuario->save();

        return response()->json(['message' => 'Código OTP gerado com sucesso'], 200);
    }

    /**
     * Valida o código OTP fornecido.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function validarOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'telefone' => 'required|numeric',
            'otp_code' => 'required|digits:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $telefone = $request->input('telefone');
        $otpCode = $request->input('otp_code');

        $usuario = Usuario::where('telefone', $telefone)->first();

        if (!$usuario) {
            return response()->json(['error' => 'Usuário não encontrado'], 404);
        }

        if ($usuario->otp_code == $otpCode) {
            // OTP code is valid
            // Perform any additional actions you need here

            return response()->json(['message' => 'Código OTP validado com sucesso'], 200);
        }

        return response()->json(['error' => 'Código OTP inválido'], 400);
    }
}
