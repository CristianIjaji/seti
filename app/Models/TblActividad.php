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
        'codigo_actividad',
        'id_encargado',
        'id_cliente',
        'id_tipo_actividad',
        'id_mes',
        'id_estacion',
        'descripcion',
        'id_permiso',
        'fecha_solicitud',
        'fecha_ejecucion',
        'fecha_finalizacion',
        'id_estado_actividad',
        'id_cotizacion',
        'id_orden_compra',
        'id_informe',
        'liquidado',
        'id_responsable_cliente',
        'id_mes_consolidado',
        'valor',
        'observaciones',
        'inf_financiera',
        'id_factura',
        'id_usuareg'
    ];

    public function tblencargado() {
        return $this->belongsTo(TblTercero::class, 'id_encargado');
    }

    public function tblcliente() {
        return $this->belongsTo(TblTercero::class, 'id_cliente');
    }

    public function tbltipoactividad() {
        return $this->belongsTo(TblDominio::class, 'id_tipo_actividad');
    }

    public function tblmes() {
        return $this->belongsTo(TblDominio::class, 'id_mes');
    }

    public function tblestacion() {
        return $this->belongsTo(TblPuntosInteres::class, 'id_estacion');
    }

    public function tblpermiso() {
        return $this->belongsTo(TblPermiso::class, 'id_permiso');
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
        return $this->belongsTo(tblinforme::class, 'id_informe');
    }

    public function tblreponsablecliente() {
        return $this->belongsTo(TblTercero::class, 'id_responsable_cliente');
    }

    public function tblmesconsolidado() {
        return $this->belongsTo(TblDominio::class, 'id_mes_consolidado');
    }

    public function tblfactura() {
        return $this->belongsTo(TblFactura::class, 'id_factura');
    }

    public function tblusuario() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }
}
