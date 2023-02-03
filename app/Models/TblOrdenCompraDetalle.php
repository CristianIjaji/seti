<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblOrdenCompraDetalle extends Model
{
    use HasFactory;

    protected $table = 'tbl_ordenes_compra_detalle';
    protected $primaryKey = 'id_orden_compra_detalle';
    protected $guarded = [];

    protected $fillable = [
        'id_orden_compra',
        'id_inventario',
        'descripcion',
        'cantidad',
        'valor_unitario',
        'valor_total'
    ];

    public function tblordencompra() {
        return $this->belongsTo(TblOrdenCompra::class, 'id_orden_compra');
    }
    
    public function tblinventario() {
        return $this->belongsTo(tblinventario::class, 'id_inventario');
    }
}
