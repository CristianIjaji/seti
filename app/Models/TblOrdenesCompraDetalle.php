<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblOrdenesCompraDetalle extends Model
{
    use HasFactory;

    protected $table = 'tbl_ordenes_compra_detalle';
    protected $primaryKey = 'id_orden_compra_detalle';
    protected $guarded = [];

    protected $fillable = [
        'id_orden_compra',
        'id_cotizacion_detalle',
        'id_lista_precio',
        'descripcion',
        'cantidad',
        'valor_unitario',
        'valor_total'
    ];

    public function tblordencompra() {
        return $this->belongsTo(TblOrdenCompra::class, 'id_orden_compra');
    }

    public function tblcotizaciondetalle() {
        return $this->belongsTo(TblCotizacionDetalle::class, 'id_cotizacion_detalle');
    }

    public function tbllistaprecio() {
        return $this->belongsTo(TblPuntosInteres::class, 'id_lista_precio');
    }
}
