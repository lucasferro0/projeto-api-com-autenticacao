<?php

namespace App\Http\Controllers;

use Exception;
use App\Rules\Unique;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UsuarioController extends Controller
{
    public function mostrar(){
        try{
            $usuarios = Usuario::all();

            return response()->json(['data' => $usuarios]);
        }catch(Exception $e){
            return response()->json(['succes' => false, 'message' => $e->getMessage()]);
        }
    }


    public function salvar(Request $request){
        DB::beginTransaction();

        try{
            $validado = $request->validate([
                'usuario' => ['required', 'min:2', new Unique('usuario')],
                'senha' => ['required'],
                'email' => ['required', new Unique('email')]
            ],
            [
                'usuario.required' => 'O campo [Usuário] é obrigatório.',
                'usuario.min' => 'O campo [Usuário] só pode ter no mínimo 2 caracteres.',
                'senha.required' => 'O campo [Senha] é obrigatório',
                'email.required' => 'O campo [Email] é obrigatório.'
            ]);

            $usuarioCriado = Usuario::create([
                'usu_nome' => $validado['usuario'], 
                'usu_senha' => Hash::make($validado['senha']),
                'usu_email' => $validado['email']
            ]);
            
            DB::commit();

            return response()->json(['succes' => true, 'message' => 'Dados armazenados com sucesso.', 'data' => $usuarioCriado]);
        }catch (ValidationException $e ) {
            DB::rollBack();
            // FORMA DE DEIXAR A RESPOSTA EM JSON MELHOR PARA O FRONT-END CONSUMIR
            $lista = [];
            $mensagem = array_values($e->errors());
            foreach($mensagem as $listaMensagem){
                foreach($listaMensagem as $msg){
                    $lista[] = $msg;
                }
            }
        
            return response()->json(['succes' => false, 'message' => $lista]);
        }catch(Exception $e){
            DB::rollBack();

            return response()->json(['succes' => false, 'message' => $e->getMessage()]);
        }    
    }


    public function atualizar(Request $request, int $id){
        DB::beginTransaction();

        try{
            $validado = $request->validate([
                'usuario' => ['required', 'min:2', new Unique('usuario')],
                'senha' => ['required'],
                'email' => ['required', new Unique('email')]
            ],
            [
                'usuario.required' => 'O campo [Usuário] é obrigatório.',
                'usuario.min' => 'O campo [Usuário] só pode ter no mínimo 2 caracteres.',
                'senha.required' => 'O campo [Senha] é obrigatório',
                'email.required' => 'O campo [Email] é obrigatório.'
            ]);

            Usuario::findOrFail($id)
            ->update([ 'usu_nome' => $validado['usuario'], 'usu_senha' => Hash::make($validado['senha']), 'usu_email' => $validado['email'] ]);

            DB::commit();

            $usuarioAlterado = Usuario::findOrFail($id);

            return response()->json(['succes' => true, 'message' => 'Dados atualizados com sucesso.', 'data' => $usuarioAlterado]);
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
            Usuario::findOrFail($id)->delete();

            DB::commit();

            return response()->json(['succes' => true, 'message' => 'Dados excluídos com sucesso.']);
        }catch(Exception $e){
            DB::rollBack();

            return response()->json(['succes' => false, 'message' => $e->getMessage()]);
        }
    }


    public function mostrarUm(int $id){
        try{
            $user = Usuario::findOrFail($id);

            return response()->json(['data' => $user]);
        }catch(Exception $e){
            return response()->json(['succes' => false, 'message' => $e->getMessage()]);
        }
    }


    public function deletarMany(string $ids){
        $listaMessages = [];
        $listaIds = explode(',', $ids);   // explode() é a mesma coisa do .split() do Python
        foreach($listaIds as $e){
            $e = (int) $e;  // Transforma $e para o tipo int. Não precisa nesse caso, mas coloquei só para aprender
            $message = $this->deletar($e); // A função retorna um dado do tipo response em json
            $listaMessages[] = collect($message)->get('original');  // A função collect() transforma em um tipo/objeto Collection

        }

        return response()->json($listaMessages);
    }
}