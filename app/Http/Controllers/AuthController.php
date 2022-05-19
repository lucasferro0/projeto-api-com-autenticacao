<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request){

        try{

            $validado = $request->validate([
                'usuario' => 'required|min:2',
                'senha' => 'required'
            ],
            [
                'usuario.required' => 'O campo [Usuário] é obrigatório.',
                'usuario.min' => 'O campo [Usuário] só pode ter no mínimo 2 caracteres.',
                'senha.required' => 'O campo [Senha] é obrigatório'
            ]);

            $user = Usuario::where('usu_nome', $validado['usuario'])->first();

            if ($user && Hash::check($validado['senha'], $user->usu_senha)){
                
                JWTAuth::factory()->setTTL(5);  // DEFINE O TEMPO DE EXPIRAÇÃO, EM MINUTOS, DO TOKEN
                $token = JWTAuth::fromUser($user); // GERA O TOKEN PARA O USUÁRIO

                return response()->json([
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => 120
                ]);
            }

            //$token = JWTAuth::attempt([ 'email' => $validado['usuario'], 'password' => $validado['senha'] ]);

            return response()->json(['data' => 'Usuário não autenticado']);

        }catch(ValidationException $e){
            return response()->json(['data' => $e->errors()]);

        }catch(Exception $e){
            return response()->json(['data' => $e->getMessage()]);
        }
    }

    public function me() // RETORNA O REGISTRO, QUE ESTÁ NO BD, DO USUÁRIO LOGADO NO MOMENTO COM O TOKEN
    {
        return response()->json(['data' => auth('api')->user()]); // auth('api')->user() RETORNA O REGISTRO, QUE ESTÁ NO BD, DO USUÁRIO LOGADO NO MOMENTO COM O TOKEN

        // auth('api')->user()->usu_nome   PEGA O VALOR DA COLUNA usu_nome DO USUÁRIO LOGADO NO MOMENTO COM O TOKEN
    }

    public function logout() // DESLOGA O USUÁRIO, OU SEJA, INVALIDA O TOKEN
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()  // DÁ UM refresh NO TOKEN, OU SEJA, RENOVA O TOKEN
    {
        JWTAuth::factory()->setTTL(3);  // DEFINE O TEMPO DE EXPIRAÇÃO, EM MINUTOS, DO TOKEN

        return response()->json([
            'access_token' => JWTAuth::parseToken()->refresh(), // JWTAuth::parseToken()->refresh() DÁ UM REFRESH NO TOKEN QUE FOI ENVIADO PELO USUÁRIO NA REQUEST, OU SEJA, INVALIDA O TOKEN ENVIADO NA REQUEST PELO USUÁRIO E GERA UM NOVO TOKEN.
            'token_type' => 'bearer',
            'expires_in' => 120
        ]);
    }

    public function resetPassword(Request $request){
        DB::beginTransaction();

        try{
            $validado = $request->validate([
                'senha_atual' => 'required',
                'senha_nova' => 'required'
            ],
            [
                'senha_atual.required' => 'O campo [Senha atual] é obrigatório.',
                'senha_nova.required' => 'O campo [Nova senha] é obrigatório.'
            ]);

            $senhaAtual = $validado['senha_atual'];
            $senhaNova = $validado['senha_nova'];

            $user = Usuario::where('usu_nome', auth('api')->user()->usu_nome)->firstOrFail();

            if ($user && Hash::check($senhaAtual, $user->usu_senha)){

                $user->update(['usu_senha' => Hash::make($senhaNova)]);

                DB::commit();

                return response()->json(['data' => 'Senha redefinida com sucesso.']);
            }

            return response()->json(['data' => 'Senha atual inválida.']);
        }catch(ValidationException $e){
            DB::rollBack();

            return response()->json(['data' => $e->errors()]);

        }catch(Exception $e){
            DB::rollBack();

            return response()->json(['data' => $e->getMessage()]);
        }
    }
}
