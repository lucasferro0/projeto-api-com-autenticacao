<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Artigo extends Model
{
    protected $table = "artigo";
    
    protected $primaryKey = "art_id";

    protected $fillable = [
        "art_titulo",
        "art_conteudo",
        "art_autor"
    ];
}
