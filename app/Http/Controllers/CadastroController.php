<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Cadastro;
use App\Models\Usuario;
use Illuminate\Support\Facades\Validator;


class CadastroController extends Controller
{
    public function create(Request $request)
{
    $validator = Validator::make($request->all(), [
        'nome' => 'required',
        'email' => 'required|email|unique:cadastros',
        'senha' => 'required|min:6',
        'telefone' => 'required|numeric|unique:cadastros',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 400);
    }

    $cadastro = Cadastro::create([
        'nome' => $request->input('nome'),
        'email' => $request->input('email'),
        'senha' => password_hash($request->input('senha'), PASSWORD_DEFAULT),
        'telefone' => $request->input('telefone'),
    ]);

    return response()->json(['message' => 'Cadastro realizado com sucesso'], 201);
}

    public function getOTP(Request $request){
    $telefone = $request->input('telefone');

    // Verificar se o número de telefone já está cadastrado
    $usuario = Usuario::where('telefone', $telefone)->first();

    if ($usuario) {
        // O número de telefone já está cadastrado, redirecionar para a tela de login
        return response()->json(['message' => 'Número de telefone já cadastrado. Faça login.']);
    }

    // Gerar código OTP aleatório
    $otpCode = mt_rand(100000, 999999);

    // Salvar o código OTP no banco de dados
    DB::table('otps')->insert([
        'phone_number' => $telefone,
        'otp_code' => $otpCode,
    ]);

    // Aqui você pode enviar o código OTP para o usuário via SMS ou outro método de envio

    return response()->json(['message' => 'Código OTP gerado com sucesso']);
}
}
