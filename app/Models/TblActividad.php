<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class TblActividad extends Model
{
    use HasFactory;

    protected $table = 'tbl_actividades';
    protected $primaryKey = 'id_actividad';

    protected $fillable = [
        'ot',
        'id_tipo_actividad',
        'id_dominio_subsistema',
        'descripcion',
        'id_tercero_encargado_cliente',
        'id_tercero_resposable_contratista',
        'id_estacion',
        'permiso_acceso',
        'fecha_solicitud',
        'fecha_programacion',
        'fecha_reprogramacion',
        'fecha_ejecucion',
        'id_dominio_estado',
        'id_cotizacion',
        'id_informe_actividad',
        'fecha_liquidado',
        'liquidado',
        'mes_consolidado',
        'valor',
        'observaciones',
        'id_usuareg'
    ];

    public function tbltipoactividad() {
        return $this->belongsTo(TblDominio::class, 'id_tipo_actividad');
    }

    public function tblsubsistema() {
        return $this->belongsTo(TblDominio::class, 'id_dominio_subsistema');
    }

    public function tblencargadocliente() {
        return $this->belongsTo(TblTercero::class, 'id_tercero_encargado_cliente');
    }

    public function tblresposablecontratista() {
        return $this->belongsTo(TblTercero::class, 'id_tercero_resposable_contratista');
    }

    public function tblestacion() {
        return $this->belongsTo(TblPuntosInteres::class, 'id_estacion');
    }

    public function tblestadoactividad() {
        return $this->belongsTo(TblDominio::class, 'id_dominio_estado');
    }

    public function tblcotizacion() {
        return $this->belongsTo(TblCotizacion::class, 'id_cotizacion');
    }

    public function tblinforme() {
        // return $this->belongsTo(tblinforme::class, 'id_informe_actividad');
    }

    public function tblconsolidadodetalle() {
        return $this->belongsTo(TblConsolidadoDetalle::class, 'id_actividad');
    }

    public function tblusuario() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }

    public function getEstadoAttribute() {
        return $this->attributes['id_dominio_estado'];
    }

    public function getStatusAttribute() {
        return [
            session('id_dominio_actividad_programado') => 'bg-danger bg-opacity-25 text-danger',
            session('id_dominio_actividad_comprando') => '',
            session('id_dominio_actividad_reprogramado') => '',
            session('id_dominio_actividad_ejecutado') => 'bg-success bg-opacity-50',
            session('id_dominio_actividad_informe_cargado') => '',
            session('id_dominio_actividad_pausada') => 'bg-danger',
            session('id_dominio_actividad_liquidado') => '',
            session('id_dominio_actividad_conciliado') => '',
        ];
    }

    public function getValorCotizadoAttribute() {
        return number_format(isset($this->attributes['valor_cotizado']) ? $this->attributes['valor_cotizado'] : $this->attributes['valor'], 2);
    }

    public function getInventario() {
        return TblMovimiento::where([
            'id_dominio_tipo_movimiento' => session('id_dominio_movimiento_salida_actividad'),
            'id_tercero_recibe' => $this->attributes['id_tercero_resposable_contratista'],
            'documento' => $this->attributes['id_actividad'],
        ])->first();
    }
}
