<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblConsolidadoDetalle extends Model
{
    use HasFactory;

    protected $table = 'tbl_consolidados_detalle';
    protected $primaryKey = 'id_consolidado_detalle';
    protected $guarded = [];

    protected $fillable = [
        'id_consolidado',
        'id_actividad',
        'observacion',
        'id_usuareg'
    ];

    public function tblconsolidado() {
        return $this->belongsTo(TblConsolidado::class, 'id_consolidado');
    }

    public function tblactividad() {
        return $this->belongsTo(TblActividad::class, 'id_actividad');
    }

    public function tblusuareg() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }

    // public function getObservacionAttribute() {
    //     return "<textarea class='form-control' name='observacion[]' id='observacion' rows='2' style='resize: none;' required >{{ old('observacion[]', $this->attributes[observacion]) }}</textarea>";
    // }

    public function getObservacionConsolidadoAttribute() {
        $observacion = isset($this->attributes['observacion']) ? $this->attributes['observacion'] : '';
        return "<textarea class='form-control' style='resize: none;'>$observacion</textarea>";
    }
}
