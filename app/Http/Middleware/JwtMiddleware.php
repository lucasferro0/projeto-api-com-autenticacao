<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {                                       // JWTAuth::parseToken()->authenticate() VAI VERIFICAR SE O TOKEN DO USUÁRIO QUE MANDOU A REQUEST É VÁLIDO
            JWTAuth::parseToken()->authenticate();  // JWTAuth::parseToken() RETORNA O TOKEN QUE FOI ENVIADO PELO USUÁRIO NA REQUEST, OU SEJA, RETORNA O TOKEN DO USUÁRIO QUE MANDOU A REQUEST

        }catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json(['status' => 'O token está inválido.']);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json(['status' => 'O token está expirado.']);
            }else{
                return response()->json(['status' => 'Token de autorização não encontrado.']);
            }
        }

        return $next($request);
    }
}
