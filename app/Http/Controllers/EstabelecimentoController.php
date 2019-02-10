<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use TCG\Voyager\Http\Controllers\VoyagerBaseController as BaseVoyagerBaseController;

class EstabelecimentoController extends BaseVoyagerBaseController
{
    public function new_estabelecimento(){
        return response()->json(['code' => 0 ,'message' => 'rota ok'], 200);
    }
}
