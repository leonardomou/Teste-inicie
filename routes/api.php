<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/service/dados/{state}/{dataInicial}/{dataFinal}', 'App\Http\Controllers\dadosController@getDados')->name('covid.getDados');
Route::post('/service/dados/send', 'App\Http\Controllers\dadosController@sendTop10')->name('covid.sendDados');