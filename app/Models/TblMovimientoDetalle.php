<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMovimientoDetalle extends Model
{
    use HasFactory;

    protected $table = 'tbl_movimientos_detalle';
    protected $primaryKey = 'id_movimiento_detalle';
    protected $guarded = [];

    protected $fillable = [
        'id_movimiento',
        'id_inventario',
        'cantidad',
        'valor_unitario',
        'valor_total',
        'id_usuareg'
    ];

    public function tblmovimiento() {
        return $this->belongsTo(TblMovimiento::class, 'id_movimiento');
    }

    public function tblinventario() {
        return $this->belongsTo(TblInventario::class, 'id_inventario');
    }
}
