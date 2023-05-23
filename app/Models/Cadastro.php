<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cadastro extends Model
{
    protected $table = 'cadastros';
    protected $fillable = ['nome', 'email', 'senha','telefone'];
}
