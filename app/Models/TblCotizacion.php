<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class TblCotizacion extends Model
{
    use HasFactory;

    protected $table = 'tbl_cotizaciones';
    protected $primaryKey = 'id_cotizacion';
    protected $guarded = [];

    protected $fillable = [
        'ot_trabajo',
        'id_cliente',
        'id_estacion',
        'id_tipo_trabajo',
        'fecha_solicitud',
        'fecha_envio',
        'id_prioridad',
        'estado',
        'id_responsable_cliente',
        'valor',
        'iva',
        'descripcion',
        'valor_reasignado',
        'id_usuareg',
    ];

    public function tblCliente() {
        return $this->belongsTo(TblTercero::class, 'id_cliente');
    }

    public function tblEstacion() {
        return $this->belongsTo(TblPuntosInteres::class, 'id_estacion');
    }

    public function tblTipoTrabajo() {
        return $this->belongsTo(TblDominio::class, 'id_tipo_trabajo');
    }

    public function tblPrioridad() {
        return $this->belongsTo(TblDominio::class, 'id_prioridad');
    }

    public function tblIva() {
        return $this->belongsTo(TblDominio::class, 'iva');
    }

    public function tblContratista() {
        return $this->belongsTo(TblTercero::class, 'id_responsable_cliente');
    }

    public function tblusereg() {
        return $this->hasOne(TblUsuario::class, 'id_usuareg');
    }

    public function getmaterialescotizacion($id_cotizacion) {
        return TblCotizacionDetalle::where(['id_cotizacion' => $id_cotizacion, 'id_tipo_item' => session('id_dominio_materiales')])->get();
    }

    public function getmanoobracotizacion($id_cotizacion) {
        return TblCotizacionDetalle::where(['id_cotizacion' => $id_cotizacion, 'id_tipo_item' => session('id_dominio_mano_obra')])->get();
    }

    public function gettransportecotizacion($id_cotizacion) {
        return TblCotizacionDetalle::where(['id_cotizacion' => $id_cotizacion, 'id_tipo_item' => session('id_dominio_transporte')])->get();
    }

    public function getTotalMaterialAttribute() {
        return TblCotizacionDetalle::where([
            'id_cotizacion' => $this->attributes['id_cotizacion'],
            'id_tipo_item' => session('id_dominio_materiales')
        ])->sum('valor_total');
    }

    public function getTotalManoObraAttribute() {
        return TblCotizacionDetalle::where([
            'id_cotizacion' => $this->attributes['id_cotizacion'],
            'id_tipo_item' => session('id_dominio_mano_obra')
        ])->sum('valor_total');
    }

    public function getTotalTransporteAttribute() {
        return TblCotizacionDetalle::where([
            'id_cotizacion' => $this->attributes['id_cotizacion'],
            'id_tipo_item' => session('id_dominio_transporte')
        ])->sum('valor_total');
    }

    public function getTotalSinIvaAttribute() {
        $total = TblCotizacionDetalle::where([
            'id_cotizacion' => $this->attributes['id_cotizacion']
        ])->sum('valor_total');
        return ($total > 0 ? $total : 0);
    }

    public function getTotalIvaAttribute() {
        $totalsiniva = $this->getTotalSinIvaAttribute();
        $modeloiva = TblDominio::where(['id_dominio' => $this->attributes['iva']])->first();
        $iva = str_replace(['IVA ', '%'], ['', ''], $modeloiva->nombre);

        $valorIva = ($totalsiniva * $iva) / 100;

        return $valorIva;
    }

    public function getTotalConIvaAttribute() {
        $totalsiniva = $this->getTotalSinIvaAttribute();
        $totalIva = $this->getTotalIvaAttribute();

        return $totalsiniva + $totalIva;
    }

    public function getStatusAttribute() {
        return [
            session('id_dominio_cotizacion_creada') => 'bg-gradient',
            session('id_dominio_cotizacion_devuelta') => 'bg-table-warning bg-gradient text-dark fw-normal',
            session('id_dominio_cotizacion_revisada') => 'bg-table-info bg-gradient text-dark fw-normal',
            // session('id_dominio_cotizacion_enviada') => 'bg-table-success bg-gradient',
            session('id_dominio_cotizacion_pendiente_aprobacion') => 'bg-table-success bg-gradient',
            session('id_dominio_cotizacion_rechazada') => 'bg-gradient text-danger',
            session('id_dominio_cotizacion_cancelada') => 'bg-gradient text-danger',
            session('id_dominio_cotizacion_aprobada') => 'bg-gradient text-success',
        ];
    }

    public function getMesesAttribute() {
        return [
            '1' => 'Enero',
            '2' => 'Febrero',
            '3' => 'Marzo',
            '4' => 'Abril',
            '5' => 'Mayo',
            '6' => 'Junio',
            '7' => 'Julio',
            '8' => 'Agosto',
            '9' => 'Septiembre',
            '10' => 'Octubre',
            '11' => 'Noviembre',
            '12' => 'Diciembre'
        ];
    }

    public function getFechaCotizacionAttribute() {
        $fecha_solicitud = $this->attributes['fecha_solicitud'];
        $months = $this->getMesesAttribute();
        $day = date('d', strtotime($fecha_solicitud));
        $month = substr($months[intval(date('m', strtotime($fecha_solicitud)))], 0, 3);
        $year = date('Y', strtotime($fecha_solicitud));

        return "$day/$month/$year";
    }

    public function getCotizacionAttribute() {
        return "Sitio: ".$this->tblestacion->nombre.". F. Solcitud: ".$this->fecha_solicitud.'. Manto.: '.$this->tblTipoTrabajo->nombre.'. Alcance: '.$this->descripcion;
    }
}
