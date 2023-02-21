<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TblOrdenCompra extends Model
{
    use HasFactory;

    protected $table = 'tbl_ordenes_compra';
    protected $primaryKey = 'id_orden_compra';
    protected $guarded = [];

    protected $fillable = [
        'id_tercero_almacen',
        'id_tercero_proveedor',
        'id_dominio_tipo',
        'descripcion',
        'id_dominio_modalidad_pago',
        'id_dominio_iva',
        'id_dominio_estado',
        'id_tercero_asesor',
        'vencimiento',
        'cupo_actual',
        'id_usuareg'
    ];

    public function tblalmacen() {
        return $this->belongsTo(TblTercero::class, 'id_tercero_almacen');
    }

    public function tbltipo() {
        return $this->belongsTo(TblDominio::class, 'id_dominio_tipo');
    }

    public function tblproveedor() {
        return $this->belongsTo(TblTercero::class, 'id_tercero_proveedor');
    }

    public function tblmodalidadpago() {
        return $this->belongsTo(TblDominio::class, 'id_dominio_modalidad_pago');
    }

    public function tbliva() {
        return $this->belongsTo(TblDominio::class, 'id_dominio_iva');
    }

    public function tblestado() {
        return $this->belongsTo(TblDominio::class, 'id_dominio_estado');
    }

    public function tblasesor() {
        return $this->belongsTo(TblTercero::class, 'id_tercero_asesor');
    }

    public function tblusuario() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }

    public function tbldetalleorden() {
        return $this->hasMany(TblOrdenCompraDetalle::class, 'id_orden_compra');
    }

    public function getCupoActualAttribute() {
        return number_format(isset($this->attributes['cupo_actual']) ? $this->attributes['cupo_actual'] : 0, 2);
    }

    public function getDetalleOrden() {
        $carrito = [];
        // $items = TblOrdenCompraDetalle::with(['tblinventario'])->where(['id_orden_compra' => (isset($this->attributes['id_orden_compra']) ? $this->attributes['id_orden_compra'] : -1)])->get();

        // foreach ($items as $item) {
        //     $carrito[session('id_dominio_tipo_orden_compra')][$item->id_inventario] = [
        //         'item' => $item->id_inventario,
        //         'descripcion' => $item->tblinventario->descripcion,
        //         'cantidad' => $item->cantidad,
        //         'valor_unitario' => $item->valor_unitario,
        //         'valor_total' => $item->valor_total,
        //     ];
        // }

        // return $carrito;

        $items = DB::select("
            WITH orden AS (
                SELECT
                    oc.id_orden_compra,
                    oc.id_tercero_almacen,
                    oc.id_tercero_proveedor
                FROM tbl_ordenes_compra AS oc

                WHERE oc.id_orden_compra = :id_orden_compra
            ),
            detalle_orden as (
                SELECT
                    det.id_inventario,
                    det.descripcion,
                    det.cantidad as cantidad,
                    det.valor_unitario,
                    det.valor_total
                FROM orden
                INNER JOIN tbl_ordenes_compra_detalle AS det ON(det.id_orden_compra = orden.id_orden_compra)
            ),
            entradas AS (
                SELECT
                    det.id_inventario,
                    SUM(det.cantidad) AS cantidad
                FROM orden AS o
                INNER JOIN tbl_movimientos AS m ON(
                    m.id_tercero_recibe = o.id_tercero_almacen
                    AND m.id_tercero_entrega = o.id_tercero_proveedor
                    AND m.documento = o.id_orden_compra
                )
                INNER JOIN tbl_movimientos_detalle AS det ON(det.id_movimiento = m.id_movimiento)
                
                GROUP BY 1
            )
            
            SELECT
                detalle_orden.id_inventario,
                detalle_orden.descripcion,
                detalle_orden.cantidad as cantidad,
                COALESCE(entradas.cantidad, 0) as recibido,
                (detalle_orden.cantidad - COALESCE(entradas.cantidad, 0)) AS pendiente,
                detalle_orden.valor_unitario,
                detalle_orden.valor_total
            FROM detalle_orden
            LEFT JOIN entradas ON(entradas.id_inventario = detalle_orden.id_inventario)
        ", ['id_orden_compra' => (isset($this->attributes['id_orden_compra']) ? $this->attributes['id_orden_compra'] : -1)]);

        foreach ($items as $item) {
            $carrito[session('id_dominio_tipo_orden_compra')][$item->id_inventario] = [
                'item' => $item->id_inventario,
                'descripcion' => $item->descripcion,
                'cantidad' => $item->cantidad,
                'recibido' => $item->recibido,
                'pendiente' => $item->pendiente,
                'valor_unitario' => $item->valor_unitario,
                'valor_total' => $item->valor_total,
            ];
        }

        return $carrito;
    }

    public function getIvaAttribute() {
        return (isset($this->attributes['id_dominio_iva'])
            ? $this->tbliva->descripcion
            : '0%'
        );
    }

    public function getValorIvaAttribute() {
        $porcentaje = intval(str_replace('%', '', $this->getIvaAttribute())) / 100;
        return (isset($this->attributes['cupo_actual']) ? $this->attributes['cupo_actual'] : 0) * $porcentaje;
    }

    public function getSubTotalAttribute() {
        return isset($this->attributes['cupo_actual']) ? $this->attributes['cupo_actual'] : 0;
    }

    public function getValorTotalAttribute() {
        return (isset($this->attributes['cupo_actual']) ? $this->attributes['cupo_actual'] : 0);
    }

    public static function getCarritoOrden($id_orden_compra, $getAll = false) {
        $carrito[session('id_dominio_tipo_movimiento')] = [];
        $where = (!$getAll
            ? "WHERE (detalle_orden.cantidad - COALESCE(entradas.cantidad, 0)) > 0"
            : ""
        );

        $items = DB::select("
            WITH orden AS (
                SELECT
                    oc.id_orden_compra,
                    oc.id_tercero_almacen,
                    oc.id_tercero_proveedor
                FROM tbl_ordenes_compra AS oc

                WHERE oc.id_orden_compra = :id_orden_compra
            ),
            detalle_orden as (
                SELECT
                    det.id_inventario,
                    det.descripcion,
                    det.cantidad as cantidad,
                    det.valor_unitario,
                    det.valor_total
                FROM orden
                INNER JOIN tbl_ordenes_compra_detalle AS det ON(det.id_orden_compra = orden.id_orden_compra)
            ),
            entradas AS (
                SELECT
                    det.id_inventario,
                    SUM(det.cantidad) AS cantidad
                FROM orden AS o
                INNER JOIN tbl_movimientos AS m ON(
                    m.id_tercero_recibe = o.id_tercero_almacen
                    AND m.id_tercero_entrega = o.id_tercero_proveedor
                    AND m.documento = o.id_orden_compra
                )
                INNER JOIN tbl_movimientos_detalle AS det ON(det.id_movimiento = m.id_movimiento)
                
                GROUP BY 1
            )
            
            SELECT
                detalle_orden.id_inventario,
                detalle_orden.descripcion,
                detalle_orden.cantidad as solicitado,
                COALESCE(entradas.cantidad, 0) as recibido,
                (detalle_orden.cantidad - COALESCE(entradas.cantidad, 0)) AS cantidad,
                detalle_orden.valor_unitario,
                detalle_orden.valor_total
            FROM detalle_orden
            LEFT JOIN entradas ON(entradas.id_inventario = detalle_orden.id_inventario)
            $where;
        ", ['id_orden_compra' => $id_orden_compra]);

        foreach ($items as $item) {
            $carrito[session('id_dominio_tipo_movimiento')][$item->id_inventario] = [
                'item' => $item->id_inventario,
                'descripcion' => $item->descripcion,
                'solicitado' => $item->solicitado,
                'recibido' => $item->recibido,
                'cantidad' => $item->cantidad,
                'valor_unitario' => $item->valor_unitario,
                'valor_total' => $item->valor_total,
            ];
        }

        return $carrito;
    }
}
