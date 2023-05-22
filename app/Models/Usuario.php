<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cadastros';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['nome', 'email', 'senha', 'telefone'];
}