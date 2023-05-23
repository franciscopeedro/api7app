<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
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
            'senha' => 'required'
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

    public function getOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'telefone' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $telefone = $request->input('telefone');

        // Verificar se o número de telefone já está cadastrado
        $cadastro = DB::table('cadastros')->where('telefone', $telefone)->first();

        if ($cadastro) {
            // O número de telefone já está cadastrado, redirecionar para a tela de login
            return response()->json(['message' => 'Número de telefone já cadastrado. Faça login.']);
        }

        // Gerar código OTP aleatório
        $otpCode = mt_rand(100000, 999999);

        // Salvar o código OTP no banco de dados
        DB::table('otp_codes')->insert([
            'telefone' => $telefone,
            'otp_code' => $otpCode,
        ]);

        // Aqui você pode enviar o código OTP para o usuário via SMS ou outro método de envio

        return response()->json(['message' => 'Código OTP gerado com sucesso']);
    }

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

        $otp = DB::table('otp_codes')->where('telefone', $telefone)->first();

        if (!$otp) {
            return response()->json(['error' => 'Número não cadastrado'], 404);
        }

        if ($otp->otp_code == $otpCode) {
            // Código OTP válido
            // Execute qualquer ação adicional necessária aqui

            return response()->json(['message' => 'Código OTP validado com sucesso'], 200);
        }

        return response()->json(['error' => 'Código OTP inválido'], 400);
    }
}
