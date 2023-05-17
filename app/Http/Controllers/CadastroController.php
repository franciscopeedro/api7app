<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cadastro;

class CadastroController extends Controller
{
    public function create(Request $request)
    {
        $this->validate($request, [
            'nome' => 'required',
            'email' => 'required|email|unique:cadastros',
            'senha' => 'required|min:6',
        ]);

        $cadastro = new Cadastro();
        $cadastro->nome = $request->nome;
        $cadastro->email = $request->email;
        $cadastro->senha = password_hash($request->senha, PASSWORD_DEFAULT);
        $cadastro->save();

        return response()->json(['message' => 'Cadastro realizado com sucesso'], 201);
    }
}
