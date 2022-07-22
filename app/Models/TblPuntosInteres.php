<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblPuntosInteres extends Model
{
    use HasFactory;

    protected $table = 'tbl_puntos_interes';
    protected $primaryKey = 'id_punto_interes';
    protected $guarded = [];

    protected $filable = [
        'id_cliente',
        'id_zona',
        'nombre',
        'latitud',
        'longitud',
        'estado',
        'descripcion',
        'id_tipo_transporte',
        'id_tipo_accesso',
        'id_usuareg'
    ];

    public function tblcliente() {
        return $this->belongsTo(TblTercero::class, 'id_cliente');
    }

    public function tbldominiozona() {
        return $this->belongsTo(TblDominio::class, 'id_zona');
    }

    public function tbldominiotransporte() {
        return $this->belongsTo(TblDominio::class, 'id_tipo_transporte');
    }

    public function tbldominioacceso() {
        return $this->belongsTo(TblDominio::class, 'id_tipo_accesso');
    }

    public function tblusuario() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }

    public function getEstadoFormAttribute() {
        return $this->attributes['estado'];
    }

    public function getEstadoAttribute() {
        $status = $this->attributes['estado'] == 1 ? '<i class="fa-solid fa-check fw-bolder fs-4 text-success"></i>' : '<i class="fa-solid fa-xmark fw-bolder fs-4 text-danger"></i>';
        return $status;
    }
}
