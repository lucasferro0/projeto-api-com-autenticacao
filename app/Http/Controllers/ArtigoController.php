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
        try{
            $artigos = Artigo::all();

            return response()->json(['data' => $artigos]);
        }catch(Exception $e){
            return response()->json(['succes' => false, 'message' => $e->getMessage()]);
        }
    }

    public function salvar(Request $request){
        DB::beginTransaction();

        try{
            $validado = $request->validate([
                'titulo' => ['required', 'min:2'],
                'conteudo' => ['required']
            ],
            [
                'titulo.required' => 'O campo [Título] é obrigatório.',
                'titulo.min' => 'O campo [Título] só pode ter no mínimo 2 caracteres.',
                'conteudo.required' => 'O campo [Conteúdo] é obrigatório'
            ]);

            $artigoCriado = Artigo::create([
                'art_titulo' => $validado['titulo'], 
                'art_conteudo' => $validado['conteudo']
            ]);
            
            DB::commit();

            return response()->json(['succes' => true, 'message' => 'Dados armazenados com sucesso.', 'data' => $artigoCriado]);
        }catch (ValidationException $e ) {
            DB::rollBack();
        
            return response()->json(['succes' => false, 'message' => $e->errors()]);
        }catch(Exception $e){
            DB::rollBack();

            return response()->json(['succes' => false, 'message' => $e->getMessage()]);
        }
    }

    public function atualizar(Request $request, int $id){
        DB::beginTransaction();

        try{
            $validado = $request->validate([
                'titulo' => ['required', 'min:2'],
                'conteudo' => ['required']
            ],
            [
                'titulo.required' => 'O campo [Título] é obrigatório.',
                'titulo.min' => 'O campo [Título] só pode ter no mínimo 2 caracteres.',
                'conteudo.required' => 'O campo [Conteúdo] é obrigatório'
            ]);

            Artigo::findOrFail($id)
            ->update(['art_titulo' => $validado['titulo'], 'art_conteudo' => $validado['conteudo']]);

            DB::commit();

            $artigoAlterado = Artigo::findOrFail($id);

            return response()->json(['succes' => true, 'message' => 'Dados atualizados com sucesso.', 'data' => $artigoAlterado]);
        }catch (ValidationException $e ) {
            DB::rollBack();

            return response()->json(['succes' => false, 'message' => $e->errors()]);
        }catch(Exception $e){
            DB::rollBack();

            return response()->json(['succes' => false, 'message' => $e->getMessage()]);
        }
    }

    public function deletar(int $id){
        DB::beginTransaction();

        try{
            Artigo::findOrFail($id)->delete();

            DB::commit();

            return response()->json(['succes' => true, 'message' => 'Dados excluídos com sucesso.']);
        }catch(Exception $e){
            DB::rollBack();

            return response()->json(['succes' => false, 'message' => $e->getMessage()]);
        }
    }
}
