<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UsuarioController extends Controller
{
    public function mostrar(){
        $usuarios = Usuario::all();

        return response()->json(['data' => $usuarios]);
    }


    public function salvar(Request $request){
        DB::beginTransaction();

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

            Usuario::create([
                'usu_nome' => $validado['usuario'], 
                'usu_senha' => Hash::make($validado['senha'])
            ]);
            
            DB::commit();

            return response()->json(['data' => 'Dados armazenados com sucesso.']);
        }catch (ValidationException $e ) {

            DB::rollBack();
        
            $arrError = $e->errors();


            return response()->json(['data' => $arrError]);
        }catch(Exception $e){

            DB::rollBack();

            return response()->json(['data' => $e->getMessage()]);
        }    
    }

    public function atualizar(Request $request, int $id){
        DB::beginTransaction();

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

            Usuario::findOrFail($id)
            ->update([ 'usu_nome' => $validado['usuario'], 'usu_senha' => Hash::make($validado['senha']) ]);

            DB::commit();

            $usuarioAlterado = Usuario::findOrFail($id);

            return response()->json(['data' => $usuarioAlterado]);
        }catch (ValidationException $e ) {

            DB::rollBack();
        
            $arrError = $e->errors();

            return response()->json(['data' => $arrError]);
        }catch(Exception $e){
            DB::rollBack();

            return response()->json(['data'=> $e->getMessage()]);
        }
    }

    public function deletar(int $id){
        DB::beginTransaction();

        try{
            Usuario::findOrFail($id)->delete();

            DB::commit();

            return response()->json(['data' => 'Dado excluído com sucesso.']);
        }catch(Exception $e){
            DB::rollBack();

            return response()->json(['data' => $e->getMessage()]);
        }
    }
}