<?php

namespace App\Services;

use Exception;
use App\Models\Artigo;
use Illuminate\Support\Facades\DB;

class RemovedorArtigo{

    public function delete(int $id){
        //DB::beginTransaction();
        try{
            $artigo = Artigo::findOrFail($id);
            if ($artigo->art_autor === auth('api')->user()->usu_nome){
                $artigo->delete();

                //DB::commit();

                return ['succes' => true, 'message' => 'Dados excluídos com sucesso.'];
            }

            return ['succes' => false, 'message' => 'O usuário não tem permissão para executar essa ação.'];

        }catch(Exception $e){
            //DB::rollBack();
            return ['succes' => false, 'message' => $e->getMessage()];
        }
    }
}