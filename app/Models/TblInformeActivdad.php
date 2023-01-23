<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblInformeActivdad extends Model
{
    use HasFactory;

    protected $table = 'tbl_informes_actividades';
    protected $primaryKey = 'id_informe_actividad';
    protected $guarded = [];

    protected $fillable = [
        'id_actividad',
        'id_tercero_encargado',
        'id_dominio_estado',
        'id_usuareg'
    ];

    public function tblactividad() {
        return $this->belongsTo(TblActividad::class, 'id_actividad');
    }

    public function tblencargado() {
        return $this->belongsTo(TblTercero::class, 'id_tercero_encargado');
    }

    public function tblestadoinforme() {
        return $this->belongsTo(TblDominio::class, 'id_dominio_estado');
    }

    public function tblusuario() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }
}
