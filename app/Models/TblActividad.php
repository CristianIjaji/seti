<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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
        'id_liquidacion',
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
        return $this->belongsTo(TblInformeActivdad::class, 'id_informe_actividad');
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

    public function getMovimientoInventario() {
        return TblMovimiento::where([
            'id_dominio_tipo_movimiento' => session('id_dominio_movimiento_salida_actividad'),
            'id_tercero_recibe' => isset($this->attributes['id_tercero_resposable_contratista']) ? $this->attributes['id_tercero_resposable_contratista'] : -1,
            'documento' => isset($this->attributes['id_actividad']) ? $this->attributes['id_actividad'] : -1,
        ])->first();
    }

    public static function getCarritoActividad($id_actividad, $getAll = false) {
        $carrito[session('id_dominio_tipo_movimiento')] = [];
        $where = (!$getAll
            ? "WHERE (detalle_orden.cantidad - COALESCE(entradas.cantidad, 0)) > 0"
            : ""
        );

        $items = DB::select("WITH movimientos AS
            (
                SELECT
                    m.id_movimiento,
                    m.id_tercero_recibe,
                    m.id_tercero_entrega,
                    m.documento,
                    id_dominio_tipo_movimiento

                FROM tbl_movimientos AS m
                WHERE m.id_dominio_estado = :id_estado_movimiento
                AND documento > 0
                AND id_dominio_tipo_movimiento IN(:id_tipo_movimientos)
                AND m.documento = :id_actividad
            ),
            salidas AS (
                SELECT
                    m.id_tercero_recibe,
                    det.id_inventario,
                    det.cantidad,
                    det.valor_unitario,
                    det.valor_total,
                    m.documento AS id_actividad
                FROM movimientos AS m
                INNER JOIN tbl_movimientos_detalle AS det ON(det.id_movimiento = m.id_movimiento)
                WHERE m.id_dominio_tipo_movimiento = :id_tipo_salida
            ),
            entradas AS (
                SELECT
                    m.id_tercero_entrega,
                    det.id_inventario,
                    det.cantidad,
                    det.valor_unitario,
                    det.valor_total,
                    m.documento AS id_actividad
                FROM tbl_movimientos AS m
                INNER JOIN tbl_movimientos_detalle AS det ON(det.id_movimiento = m.id_movimiento)
                WHERE id_dominio_tipo_movimiento = :id_tipo_entrada
            )
            
            SELECT
                salidas.id_inventario,
                i.descripcion,
                salidas.cantidad,
                salidas.valor_unitario,
                salidas.valor_total
            FROM salidas
            INNER JOIN tbl_inventario as i ON(i.id_inventario = salidas.id_inventario)
            LEFT JOIN entradas ON(
                entradas.id_tercero_entrega = salidas.id_tercero_recibe
                AND entradas.id_actividad = salidas.id_actividad
                AND entradas.id_inventario = salidas.id_inventario
            )

            WHERE entradas.id_inventario IS NULL
        ", [
            'id_estado_movimiento' => session('id_dominio_movimiento_completado'),
            'id_tipo_movimientos' => session('id_dominio_movimiento_salida_actividad').','.session('id_dominio_movimiento_entrada_devolucion'),
            'id_actividad' => $id_actividad,
            'id_tipo_salida' => session('id_dominio_movimiento_salida_actividad'),
            'id_tipo_entrada' => session('id_dominio_movimiento_entrada_devolucion')
        ]);

        foreach ($items as $item) {
            $carrito[session('id_dominio_tipo_movimiento')][$item->id_inventario] = [
                'item' => $item->id_inventario,
                'descripcion' => $item->descripcion,
                'cantidad' => $item->cantidad,
                'valor_unitario' => $item->valor_unitario,
                'valor_total' => $item->valor_total,
            ];
        }

        return $carrito;
    }
}
