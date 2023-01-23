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
        $fecha = isset($this->attributes['created_at']) ? $this->attributes['created_at'] : date('Y-m-d');
        return date('Y-m-d', strtotime($fecha));
    }

    public function getValorUnitarioAttribute() {
        return (isset($this->attributes['valor_unitario']) && $this->attributes['valor_unitario'] > 0) ? number_format($this->attributes['valor_unitario'], 2) : 0;
    }

    // public function getIvaAttribute() {
    //     return (isset($this->tblmovimientodetalle->iva)
    //         ? $this->tblmovimientodetalle->tbliva->descripcion
    //         : '0%'
    //     );
    // }

    public function getValorIvaAttribute() {
        $porcentaje = intval(str_replace('%', '', $this->getIvaAttribute())) / 100;
        return number_format(($this->attributes['valor_unitario'] * $this->attributes['cantidad']) * $porcentaje, 2);
    }

    public function getSubTotalAttribute() {
        $valor_total = str_replace(',', '', $this->getValorTotalAttribute());
        $valor_iva = str_replace(',', '', $this->getValorIvaAttribute());
        return number_format($valor_total - $valor_iva, 2);
    }

    public function getValorTotalAttribute() {
        return (isset($this->attributes['valor_total']) && $this->attributes['valor_total'] > 0) ? number_format($this->attributes['valor_total'], 2) : 0;
    }

    public function getSaldoValorUnitarioAttribute() {
        return (isset($this->attributes['saldo_valor_unitario'])) ? number_format($this->attributes['saldo_valor_unitario'], 2) : 0;
    }

    public function getSaldoValorIvaAttribute() {
        $porcentaje = intval(str_replace('%', '', $this->getIvaAttribute())) / 100;
        return number_format(($this->attributes['saldo_valor_unitario'] * $this->attributes['saldo_cantidad']) * $porcentaje, 2);
    }

    public function getSaldoSubTotalAttribute() {
        $valor_total = str_replace(',', '', $this->getSaldoValorTotalAttribute());
        $valor_iva = str_replace(',', '', $this->getSaldoValorIvaAttribute());
        return number_format($valor_total - $valor_iva, 2);
    }

    public function getSaldoValorTotalAttribute() {
        return (isset($this->attributes['saldo_valor_total'])) ? number_format($this->attributes['saldo_valor_total'], 2) : 0;
    }

    public function getTipoMovimientoAttribute() {
        return $this->tblmovimientodetalle->tblmovimiento->tbltipomovimiento->tbldominio->id_dominio;
    }
}
