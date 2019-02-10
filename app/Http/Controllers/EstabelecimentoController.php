<?php

namespace App\Http\Controllers;

use App\Estabelecimento;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use TCG\Voyager\Http\Controllers\VoyagerBaseController as BaseVoyagerBaseController;


class EstabelecimentoController extends BaseVoyagerBaseController
{
    public function new_estabelecimento(Request $request){

        $fields = $request->all();

        $validator = Validator::make($fields, [
            'nome' => 'required|string|max:255',
            'comprador' => 'required|string|max:255',
            'telefone' => 'required|string|max:11',
            'cep'=> 'required|integer',
            'logradouro' => 'required|string|max:255',
            'bairro' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|string|max:255',
        ]);

        $validate = $validator->errors()->toJson();

        if($validator->fails()){
            $return = config('status.7');
            $return['validate'] = \GuzzleHttp\json_decode($validate);
            return response()->json($return, 200);
        }

        $estabelecimento = new Estabelecimento();

        $estabelecimento->fill($fields);

        if($estabelecimento->save()){
            return response()->json(config('status.0'));
        }




    }
}
