<?php

namespace App\Http\Controllers;

use App\Estabelecimento;
use App\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use TCG\Voyager\Http\Controllers\VoyagerBaseController as BaseVoyagerBaseController;

class PedidoController extends BaseVoyagerBaseController
{
    public function new_pedido(Request $request){

        $fields = $request->all();

        $validator = Validator::make($fields, [
            'comprador' => 'string|max:255',
            'telefone' => 'required|string|max:11',
            'id_usuario' => 'required|integer|max:255',
            'id_estabelecimento' => 'required|integer|max:255',
            'descricao_pedido' => 'required|string|max:1000',
            'valor_pedido' => 'required|numeric|max:10',
            'data_emissao_pedido' => 'required|date',
            'data_entrega_pedido'=> 'required|date',
            'status_pedido' => 'required|string|max:255',
        ]);

        $validate = $validator->errors()->toJson();

        if($validator->fails()){
            $return = config('status.7');
            $return['validate'] = \GuzzleHttp\json_decode($validate);
            return response()->json($return, 200);
        }

        $estabelecimento = new Pedido();

        $estabelecimento->fill($fields);

        if($estabelecimento->save()){
            return response()->json(config('status.0'));
        }
    }


}
