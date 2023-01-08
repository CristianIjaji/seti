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
        'id_inventario',
        'id_tercero_entrega',
        'id_tercero_recibe',
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

    public function tblinventario() {
        return $this->belongsTo(TblInventario::class, 'id_inventario');
    }

    public function tblterceroentrega() {
        return $this->belongsTo(TblTercero::class, 'id_tercero_entrega');
    }

    public function tbltercerorecibo() {
        return $this->belongsTo(TblTercero::class, 'id_tercero_recibe');
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

    public function getValorTotalAttribute() {
        return (isset($this->attributes['valor_total'])) ? number_format($this->attributes['valor_total'], 2) : 0;
    }

    public function getSaldoValorUnitarioAttribute() {
        return (isset($this->attributes['saldo_valor_unitario'])) ? number_format($this->attributes['saldo_valor_unitario'], 2) : 0;
    }

    public function getSaldoValorTotalAttribute() {
        return (isset($this->attributes['saldo_valor_total'])) ? number_format($this->attributes['saldo_valor_total'], 2) : 0;
    }
}
