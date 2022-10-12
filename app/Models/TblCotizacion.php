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

    public function tbldominioestado() {
        return $this->belongsTo(TblDominio::class, 'estado');
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

    public function getValorCotizacionAttribute() {
        return number_format($this->attributes['valor'], 2);
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
        return isset($this->tblestacion->nombre)
            ? 'Cotización: '.$this->id_cotizacion.'. '.(!empty($this->ot_trabajo) ? 'OT: '.$this->ot_trabajo.'. ' : '').$this->tblestacion->nombre.". Solicitud: ".$this->fecha_solicitud.
                '. Manto.: '.$this->tblTipoTrabajo->nombre.'. Valor: $'.number_format($this->valor, 2).'. Alcance: '.$this->descripcion
            : null;
    }

    public static function getRules() {
        return [
            '0' => 'nullable|max:20|unique:tbl_cotizaciones,ot_trabajo',
            '1' => 'required|exists:tbl_terceros,documento',
            '2' => 'required|exists:tbl_puntos_interes,nombre',
            '3' => 'required|max:255',
            '4' => 'required|date',
            '5' => 'nullable|date',
            '6' => 'required|exists:tbl_dominios,nombre',
            '7' => 'required|exists:tbl_dominios,nombre',
            '8' => 'required|exists:tbl_dominios,nombre',
            '9' => 'required|exists:tbl_terceros,documento',
            '10' => 'required',
            '11' => 'required',
        ];
    }

    public static function getProperties() {
        return [
            '0' => 'OT',
            '1' => 'Proveedor',
            '2' => 'Estación',
            '3' => 'Descripción Orden',
            '4' => 'Fecha Solicitud',
            '5' => 'Fecha Envio',
            '6' => 'Tipo Trabajo',
            '7' => 'Prioridad',
            '8' => 'Estado',
            '9' => 'Encargado',
            '10' => 'IVA',
            '11' => 'Valor'
        ];
    }

    public static function createRow(array $row) {
        $ot = trim(mb_strtolower($row[0]));
        $documento_cliente = trim(mb_strtolower($row[1]));
        $nombre_estacion = trim(mb_strtolower($row[2]));
        $descripcion = trim($row[3]);
        $fecha_solicitud = trim($row[4]);
        $fecha_envio = trim(($row[5]));
        $nombre_trabajo = trim(mb_strtolower($row[6]));
        $nombre_prioridad = trim(mb_strtolower($row[7]));
        $nombre_estado = trim(mb_strtolower($row[8]));
        $documento_encargado = trim(mb_strtolower($row[9]));
        $valor_iva = trim(mb_strtolower($row[10]));
        $valor = trim($row[11]);

        $cliente = TblTercero::where(['documento' => $documento_cliente, 'estado' => 1])->first();
        $sitio = TblPuntosInteres::where(['nombre' => $nombre_estacion, 'estado' => 1])->first();
        $tipo_trabajo = TblDominio::where(['nombre' => $nombre_trabajo, 'estado' => 1])->first();
        $prioridad = TblDominio::where(['nombre' => $nombre_prioridad, 'estado' => 1])->first();
        $estado = TblDominio::where(['nombre' => $nombre_estado, 'estado' => 1])->first();
        $encargado = TblTercero::where(['documento' => $documento_encargado, 'estado' => 1])->first();
        $iva = TblDominio::where(['descripcion' => $valor_iva, 'estado' => 1])->first();

        return new TblCotizacion([
            'ot_trabajo' => $ot,
            'id_cliente' => (isset($cliente->id_tercero) ? $cliente->id_tercero : null),
            'id_estacion' => (isset($sitio->id_punto_interes) ? $sitio->id_punto_interes : null),
            'id_tipo_trabajo' => (isset($tipo_trabajo->id_dominio) ? $tipo_trabajo->id_dominio : null),
            'fecha_solicitud' => $fecha_solicitud,
            'fecha_envio' => $fecha_envio,
            'id_prioridad' => (isset($prioridad->id_dominio) ? $prioridad->id_dominio : null),
            'estado' => (isset($estado->id_dominio) ? $estado->id_dominio : null),
            'id_responsable_cliente' => (isset($encargado->id_tercero) ? $encargado->id_tercero : null),
            'valor' => $valor,
            'iva' => (isset($iva->id_dominio) ? $iva->id_dominio : null),
            'descripcion' => $descripcion,
            'id_usuareg' => auth()->id()
        ]);
    }
}
