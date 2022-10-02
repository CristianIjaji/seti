<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblActividad extends Model
{
    use HasFactory;

    protected $table = 'tbl_actividades';
    protected $primaryKey = 'id_actividad';

    protected $fillable = [
        'ot',
        'id_tipo_actividad',
        'id_subsistema',
        'descripcion',
        'id_encargado_cliente',
        'id_resposable_contratista',
        'id_estacion',
        'permiso_acceso',
        'fecha_solicitud',
        'fecha_programacion',
        'fecha_reprogramacion',
        'fecha_ejecucion',
        'id_estado_actividad',
        'id_cotizacion',
        'id_orden_compra',
        'id_informe',
        'fecha_liquidado',
        'liquidado',
        'id_mes_consolidado',
        'valor',
        'observaciones',
        'id_usuareg'
    ];

    public function tbltipoactividad() {
        return $this->belongsTo(TblDominio::class, 'id_tipo_actividad');
    }

    public function tblsubsistema() {
        return $this->belongsTo(TblDominio::class, 'id_subsistema');
    }

    public function tblencargadocliente() {
        return $this->belongsTo(TblTercero::class, 'id_encargado_cliente');
    }

    public function tblresposablecontratista() {
        return $this->belongsTo(TblTercero::class, 'id_resposable_contratista');
    }

    public function tblestacion() {
        return $this->belongsTo(TblPuntosInteres::class, 'id_estacion');
    }

    public function tblestadoactividad() {
        return $this->belongsTo(TblDominio::class, 'id_estado_actividad');
    }

    public function tblcotizacion() {
        return $this->belongsTo(TblCotizacion::class, 'id_cotizacion');
    }
    
    public function tblordencompra() {
        return $this->belongsTo(TblOrdenCompra::class, 'id_orden_compra');
    }

    public function tblinforme() {
        // return $this->belongsTo(tblinforme::class, 'id_informe');
    }

    public function tblmesconsolidado() {
        return $this->belongsTo(TblConsolidado::class, 'id_mes_consolidado');
    }

    public function tblusuario() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }

    public function getEstadoAttribute() {
        return $this->attributes['id_estado_actividad'];
    }

    public function getStatusAttribute() {
        return [
            session('id_dominio_actividad_programado') => '',
            session('id_dominio_actividad_comprando') => '',
            session('id_dominio_actividad_reprogramado') => '',
            session('id_dominio_actividad_ejecutado') => '',
            session('id_dominio_actividad_pausada') => '',
            session('id_dominio_actividad_liquidado') => '',
            session('id_dominio_actividad_conciliado') => '',
        ];
    }
}
