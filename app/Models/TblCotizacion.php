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
        'id_tercero_cliente',
        'id_estacion',
        'id_dominio_tipo_trabajo',
        'fecha_solicitud',
        'fecha_envio',
        'id_dominio_prioridad',
        'id_dominio_estado',
        'id_tercero_responsable',
        'valor',
        'id_dominio_iva',
        'descripcion',
        'valor_reasignado',
        'id_usuareg',
    ];

    public function tblCliente() {
        return $this->belongsTo(TblTercero::class, 'id_tercero_cliente');
    }

    public function tblEstacion() {
        return $this->belongsTo(TblPuntosInteres::class, 'id_estacion');
    }

    public function tblTipoTrabajo() {
        return $this->belongsTo(TblDominio::class, 'id_dominio_tipo_trabajo');
    }

    public function tblPrioridad() {
        return $this->belongsTo(TblDominio::class, 'id_dominio_prioridad');
    }

    public function tblIva() {
        return $this->belongsTo(TblDominio::class, 'id_dominio_iva');
    }

    public function tblContratista() {
        return $this->belongsTo(TblTercero::class, 'id_tercero_responsable');
    }

    public function tblusereg() {
        return $this->hasOne(TblUsuario::class, 'id_usuareg');
    }

    public function tbldominioestado() {
        return $this->belongsTo(TblDominio::class, 'id_dominio_estado');
    }

    public function tblcotizaciondetalle() {
        return $this->hasMany(TblCotizacionDetalle::class, 'id_cotizacion');
    }

    public function getmaterialescotizacion($id_cotizacion) {
        return TblCotizacionDetalle::where(['id_cotizacion' => $id_cotizacion, 'id_dominio_tipo_item' => session('id_dominio_materiales')])->get();
    }

    public function getmanoobracotizacion($id_cotizacion) {
        return TblCotizacionDetalle::where(['id_cotizacion' => $id_cotizacion, 'id_dominio_tipo_item' => session('id_dominio_mano_obra')])->get();
    }

    public function gettransportecotizacion($id_cotizacion) {
        return TblCotizacionDetalle::where(['id_cotizacion' => $id_cotizacion, 'id_dominio_tipo_item' => session('id_dominio_transporte')])->get();
    }

    public function getTotalMaterialAttribute() {
        return TblCotizacionDetalle::where([
            'id_cotizacion' => $this->attributes['id_cotizacion'],
            'id_dominio_tipo_item' => session('id_dominio_materiales')
        ])->sum('valor_total');
    }

    public function getTotalManoObraAttribute() {
        return TblCotizacionDetalle::where([
            'id_cotizacion' => $this->attributes['id_cotizacion'],
            'id_dominio_tipo_item' => session('id_dominio_mano_obra')
        ])->sum('valor_total');
    }

    public function getTotalTransporteAttribute() {
        return TblCotizacionDetalle::where([
            'id_cotizacion' => $this->attributes['id_cotizacion'],
            'id_dominio_tipo_item' => session('id_dominio_transporte')
        ])->sum('valor_total');
    }

    public function getTotalSinIvaAttribute() {
        $total = isset($this->attributes['id_cotizacion'])
            ? TblCotizacionDetalle::where([
            'id_cotizacion' => $this->attributes['id_cotizacion']
        ])->sum('valor_total') : 0;
        return ($total > 0 ? $total : 0);
    }

    public function getTotalIvaAttribute() {
        $totalsiniva = $this->getTotalSinIvaAttribute();
        $modeloiva = TblDominio::where(['id_dominio' => $this->attributes['id_dominio_iva']])->first();
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
            session('id_dominio_cotizacion_creada') => '',
            session('id_dominio_cotizacion_devuelta') => 'bg-warning bg-opacity-50 text-dark fw-normal',
            session('id_dominio_cotizacion_revisada') => 'bg-info bg-opacity-50 text-dark fw-normal',
            // session('id_dominio_cotizacion_enviada') => 'bg-table-success ',
            session('id_dominio_cotizacion_pendiente_aprobacion') => 'bg-success bg-opacity-75',
            session('id_dominio_cotizacion_rechazada') => 'text-danger',
            session('id_dominio_cotizacion_cancelada') => 'text-danger',
            session('id_dominio_cotizacion_aprobada') => 'text-success',
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
            ? 'Cotizaci??n: '.$this->id_cotizacion.'. '.(!empty($this->ot_trabajo) ? 'OT: '.$this->ot_trabajo.'. ' : '').$this->tblestacion->nombre.". Solicitud: ".$this->fecha_solicitud.
                '. Manto.: '.$this->tblTipoTrabajo->nombre.'. Valor: $'.number_format($this->valor, 2).'. Alcance: '.$this->descripcion
            : null;
    }

    public function getEstadoAttribute() {
        return isset($this->attributes['id_dominio_estado'])
            ? $this->attributes['id_dominio_estado']
            : (isset($this->attributes['estado']) ? $this->attributes['estado'] : 0);
    }

    public function getDetalleCotizacion() {
        $carrito = [];
        $items = TblCotizacionDetalle::with(['tblListaprecio'])->where(['id_cotizacion' => (isset($this->attributes['id_cotizacion']) ? $this->attributes['id_cotizacion'] : -1)])->get();

        foreach ($items as $item) {
            $carrito[$item->id_dominio_tipo_item][$item->id_lista_precio] = [
                'item' => $item->tblListaprecio->codigo,
                'descripcion' => $item->descripcion,
                'cantidad' => $item->cantidad,
                'unidad' => $item->unidad,
                'valor_unitario' => $item->valor_unitario,
                'valor_total' => $item->valor_total,
            ];
        }

        return $carrito;
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
            '2' => 'Estaci??n',
            '3' => 'Descripci??n Orden',
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
        $documento_cliente = trim(($row[1]));
        $nombre_estacion = trim(($row[2]));
        $descripcion = trim($row[3]);
        $fecha_solicitud = trim($row[4]);
        $fecha_envio = trim(($row[5]));
        $nombre_trabajo = trim(($row[6]));
        $nombre_prioridad = trim(($row[7]));
        $nombre_estado = trim(($row[8]));
        $documento_encargado = trim(($row[9]));
        $valor_iva = trim(($row[10]));
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
            'id_tercero_cliente' => (isset($cliente->id_tercero) ? $cliente->id_tercero : null),
            'id_estacion' => (isset($sitio->id_punto_interes) ? $sitio->id_punto_interes : null),
            'id_dominio_tipo_trabajo' => (isset($tipo_trabajo->id_dominio) ? $tipo_trabajo->id_dominio : null),
            'fecha_solicitud' => $fecha_solicitud,
            'fecha_envio' => $fecha_envio,
            'id_dominio_prioridad' => (isset($prioridad->id_dominio) ? $prioridad->id_dominio : null),
            'id_dominio_estado' => (isset($estado->id_dominio) ? $estado->id_dominio : null),
            'id_tercero_responsable' => (isset($encargado->id_tercero) ? $encargado->id_tercero : null),
            'valor' => $valor,
            'id_dominio_iva' => (isset($iva->id_dominio) ? $iva->id_dominio : null),
            'descripcion' => $descripcion,
            'id_usuareg' => auth()->id()
        ]);
    }
}
