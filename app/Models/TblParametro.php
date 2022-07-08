<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblParametro extends Model
{
    use HasFactory;

    protected $table = "tbl_parametros_aplicacion";
    protected $primaryKey = "id_parametro_aplicacion";
    protected $guarded = [];

    protected $fillable = [
        'llave',
        'valor',
        'descripcion',
        'id_parametro_padre',
        'estado',
        'id_usuareg'
    ];

    public function tblusuario() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }

    public function tbldominio() {
        return $this->belongsTo(TblDominio::class, 'valor');
    }

    public function setValorAttribute($value) {
        $value = str_replace(['[', ']'], ['', ''], $value);
        $this->attributes['valor'] = $value;
    }

    public function getEstadoFormAttribute() {
        return $this->attributes['estado'];
    }

    public function getEstadoAttribute() {
        $status = $this->attributes['estado'] == 1 ? '<i class="fa-solid fa-check fw-bolder fs-4 text-success"></i>' : '<i class="fa-solid fa-xmark fw-bolder fs-4 text-danger"></i>';
        return $status;
    }
}
