<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Validation\ValidationException;
use App\Models\Artigo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArtigoController extends Controller
{
    public function mostrar(){
        $artigos = Artigo::all();

        return response()->json(['data' => $artigos]);
    }

    public function salvar(Request $request){
        DB::beginTransaction();

        try{
            $validado = $request->validate([
                'titulo' => 'required|min:2',
                'conteudo' => 'required'
            ],
            [
                'titulo.required' => 'O campo [Título] é obrigatório.',
                'titulo.min' => 'O campo [Título] só pode ter no mínimo 2 caracteres.',
                'conteudo.required' => 'O campo [Conteúdo] é obrigatório'
            ]);

            Artigo::create([
                'art_titulo' => $validado['titulo'], 
                'art_conteudo' => $validado['conteudo']
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
                'titulo' => 'required|min:2',
                'conteudo' => 'required'
            ],
            [
                'titulo.required' => 'O campo [Título] é obrigatório.',
                'titulo.min' => 'O campo [Título] só pode ter no mínimo 2 caracteres.',
                'conteudo.required' => 'O campo [Conteúdo] é obrigatório'
            ]);

            Artigo::find($id)
            ->update(['art_titulo' => $validado['titulo'], 'art_conteudo' => $validado['conteudo']]);

            DB::commit();

            $artigoAlterado = Artigo::findOrfail($id);

            return response()->json(['data' => $artigoAlterado]);
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
            Artigo::findOrfail($id)->delete();

            DB::commit();

            return response()->json(['data' => 'Dado excluído com sucesso.']);
        }catch(Exception $e){
            DB::rollBack();

            return response()->json(['data' => $e->getMessage()]);
        }
    }
}
