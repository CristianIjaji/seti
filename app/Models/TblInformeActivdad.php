<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblInformeActivdad extends Model
{
    use HasFactory;

    protected $table = 'tbl_informes_actividades';
    protected $primaryKey = 'id_informe_actividad';

    protected $fillable = [
        'id_actividad',
        'id_encargado',
        'id_estado_informe',
        'id_usuareg'
    ];

    public function tblactividad() {
        return $this->belongsTo(TblActividad::class, 'id_actividad');
    }

    public function tblencargado() {
        return $this->belongsTo(TblTercero::class, 'id_encargado');
    }

    public function tblestadoinforme() {
        return $this->belongsTo(TblDominio::class, 'id_estado_informe');
    }

    public function tblusuario() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }
}
