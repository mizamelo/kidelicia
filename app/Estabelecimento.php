<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estabelecimento extends Model
{
    use SoftDeletes;

   protected $fillable = [
        'nome',
        'comprador',
        'telefone',
        'cep',
        'logradouro',
        'bairro',
        'cidade',
        'estado',
   ];

   protected $dates = ['deleted_at'];

}
