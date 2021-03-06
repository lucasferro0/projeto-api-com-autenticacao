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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::get("artigos", "ArtigoController@mostrar")->middleware('jwt');
Route::get("artigos/{id}", "ArtigoController@mostrarUm")->middleware('jwt');
Route::post("artigos", "ArtigoController@salvar")->middleware('jwt');
Route::put("artigos/{id}", "ArtigoController@atualizar")->middleware('jwt');
Route::delete("artigos/{id}", "ArtigoController@deletar")->middleware('jwt');
Route::delete("artigos/deletes/{ids}", "ArtigoController@deletarMany")->middleware('jwt');

Route::get("usuarios", "UsuarioController@mostrar")->middleware('jwt');
Route::get("usuarios/{id}", "UsuarioController@mostrarUm")->middleware('jwt');
Route::post("usuarios", "UsuarioController@salvar");
Route::put("usuarios/{id}", "UsuarioController@atualizar")->middleware('jwt');
Route::delete("usuarios/{id}", "UsuarioController@deletar")->middleware('jwt');
Route::delete("usuarios/deletes/{ids}", "UsuarioController@deletarMany")->middleware('jwt');

Route::post("auth/login", "AuthController@login");
Route::post("auth/me", "AuthController@me")->middleware('jwt');
Route::post("auth/logout", "AuthController@logout")->middleware('jwt');
Route::post("auth/refresh", "AuthController@refresh")->middleware('jwt');
Route::post("auth/reset-password", "AuthController@resetPassword")->middleware('jwt');
Route::post("auth/recuperar-senha", "AuthController@recuperarSenha");
Route::post("auth/redefinir-senha/{code}", "AuthController@redefinirSenha")->name('redefinir_senha');
