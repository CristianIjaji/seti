<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblEstadoCotizacion extends Model
{
    use HasFactory;

    protected $table = 'tbl_estado_cotizacion';
    protected $primaryKey = 'id_estado_cotizacion';
    protected $guarded = [];

    protected $filable = [
        'id_cotizacion',
        'estado',
        'comentario',
        'id_usuareg'
    ];

    public function tblCotizacion(){
        return $this->belongsTo(tblCotizacion::class, 'id_cotizacion');
    }

    public function tblestado() {
        return $this->belongsTo(TblDominio::class, 'estado');
    }

    public function tblusuario() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }

    public function getFullNameAttribute() {
        return $this->tblusuario->tbltercero->full_name;
    }
}
