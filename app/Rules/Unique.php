<?php

namespace App\Rules;

use App\Models\Usuario;
use Illuminate\Contracts\Validation\Rule;

class Unique implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($nomeCampo)
    {
        $this->campo = $nomeCampo;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($attribute === "usuario"){
            $result = Usuario::where("usu_nome", $value)->count();

            return $result == 0;   // SE O RETORNO FOR true, O DADO PASSA NA VALIDAÇÃO. SE O RETORNO FOR false, O DADO NÃO PASSA NA VALIDAÇÃO.
        }else{
            $result = Usuario::where("usu_email", $value)->count();

            return $result == 0;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if ($this->campo === "usuario"){
            return 'O [Usuário] já existe';
        }else{
            return 'O [Email] já existe';
        }
    }
}
