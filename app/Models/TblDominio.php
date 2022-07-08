<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblDominio extends Model
{
    use HasFactory;

    protected $table = "tbl_dominios";
    protected $primaryKey = "id_dominio";
    protected $guarded = [];

    protected $fillable = [
        'nombre',
        'descripcion',
        'id_dominio_padre',
        'estado',
        'id_usuareg'
    ];

    public function tblusuario() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }

    public function tbldominio() {
        return $this->belongsTo(TblDominio::class, 'id_dominio_padre');
    }

    public function getEstadoFormAttribute() {
        return $this->attributes['estado'];
    }

    public function getEstadoAttribute() {
        $status = $this->attributes['estado'] == 1 ? '<i class="fa-solid fa-check fw-bolder fs-4 text-success"></i>' : '<i class="fa-solid fa-xmark fw-bolder fs-4 text-danger"></i>';
        return $status;
    }
}
