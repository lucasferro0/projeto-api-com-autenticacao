<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;

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

            $user = Usuario::where('usu_nome', $validado['usuario'])->firstOrFail();
            if ($user && Hash::check($validado['senha'], $user->usu_senha)){
                $token = JWTAuth::fromUser($user);

                return response()->json([
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => 120
                ]);
            }

            //$token = JWTAuth::attempt([ 'email' => $validado['usuario'], 'password' => $validado['senha'] ]);

            return response()->json(['data' => 'Usuário não autenticado']);

        }catch(Exception $e){

            return response()->json(['data' => $e->getMessage()]);
        }
    }
}
