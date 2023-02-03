<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblEstado extends Model
{
    use HasFactory;

    protected $table = 'tbl_estados';
    protected $primaryKey = 'id_estado';
    protected $guarded = [];

    protected $filable = [
        'id_tabla',
        'tabla',
        'id_dominio_estado',
        'comentario',
        'id_usuareg'
    ];

    public function tblestado() {
        return $this->belongsTo(TblDominio::class, 'id_dominio_estado');
    }

    public function tblusuario() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }

    public function getFullNameAttribute() {
        return $this->tblusuario->tbltercero->full_name;
    }
}
