<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pedido extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'id_estabelecimento',
        'id_usuario',
        'descricao_pedido',
        'valor_pedido',
        'data_emissao_pedido',
        'data_entrega_pedido',
        'status_pedido',
        'comprador',
        'telefone',
    ];

    protected $dates = ['deleted_at'];
}
