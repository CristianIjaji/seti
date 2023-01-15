<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblKardex extends Model
{
    use HasFactory;

    protected $table = 'tbl_kardex';
    protected $primaryKey = 'id_kardex';
    protected $guarded = [];

    protected $fillable = [
        'id_movimiento_detalle',
        'id_inventario',
        'concepto',
        'documento',
        'cantidad',
        'valor_unitario',
        'valor_total',
        'saldo_cantidad',
        'saldo_valor_unitario',
        'saldo_valor_total',
        'id_usuareg'
    ];

    public function tblmovimientodetalle() {
        return $this->belongsTo(TblMovimientoDetalle::class, 'id_movimiento_detalle');
    }

    public function tblinventario() {
        return $this->belongsTo(TblInventario::class, 'id_inventario');
    }

    public function tblusuario() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }

    public function getFechaKardexAttribute() {
        return date('Y-m-d', strtotime($this->attributes['created_at']));
    }

    public function getValorUnitarioAttribute() {
        return (isset($this->attributes['valor_unitario'])) ? number_format($this->attributes['valor_unitario'], 2) : 0;
    }

    public function getIvaAttribute() {
        return (isset($this->tblmovimientodetalle->iva)
            ? $this->tblmovimientodetalle->tbliva->descripcion
            : 0
        );
    }

    public function getSubTotalAttribute() {
        $porcentaje = intval(str_replace('%', '', $this->attributes['iva'])) / 100;
        $valor = $this->attributes['valor_total'];

        return $valor * $porcentaje;
    }

    public function getValorTotalAttribute() {
        return (isset($this->attributes['valor_total'])) ? number_format($this->attributes['valor_total'], 2) : 0;
    }

    public function getSaldoIvaAttribute() {
        $porcentaje = intval(str_replace('%', '', $this->attributes['iva'])) / 100;
        $valor = $this->attributes['valor_total'];

        return $valor * $porcentaje;
    }

    public function getSaldoValorUnitarioAttribute() {
        return (isset($this->attributes['saldo_valor_unitario'])) ? number_format($this->attributes['saldo_valor_unitario'], 2) : 0;
    }

    public function getSaldoValorTotalAttribute() {
        return (isset($this->attributes['saldo_valor_total'])) ? number_format($this->attributes['saldo_valor_total'], 2) : 0;
    }
}
