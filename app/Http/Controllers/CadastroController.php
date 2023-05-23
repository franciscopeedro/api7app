<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Cadastro;

use Illuminate\Validation\ValidationException;


class CadastroController extends Controller
{
    public function create(Request $request)
    {
        $validator = app('validator')->make($request->all(), [
            'nome' => 'required',
            'email' => 'required|email|unique:cadastros',
            'senha' => 'required|min:6',
            'telefone' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    $exists = DB::table('cadastros')
                        ->where('telefone', $value)
                        ->exists();

                    if ($exists) {
                        $fail('O número de telefone já está em uso.');
                    }
                },
            ],
        ]);

        try {
            $validator->validate();
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 400);
        }

        $cadastro = Cadastro::create([
            'nome' => $request->input('nome'),
            'email' => $request->input('email'),
            'senha' => password_hash($request->input('senha'), PASSWORD_DEFAULT),
            'telefone' => $request->input('telefone'),
        ]);

        return response()->json(['message' => 'Cadastro realizado com sucesso'], 201);
    }
}
