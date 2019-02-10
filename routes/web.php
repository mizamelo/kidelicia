<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('admin/pedidos');
});


Route::group(['prefix' => 'admin'], function () {

    Route::get('/', function () {
        return redirect()->route('/admin/pedidos');
    });

    Route::get('/admin', function () {
        return redirect()->route('/admin/pedidos');
    });

    Route::get('gerar_comanda', 'PedidoController@gerar_comanda')->name('gerar_comanda');

    Voyager::routes();


});

