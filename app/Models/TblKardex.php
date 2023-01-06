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

    public function tblusuario() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }

    public function getFechaKardexAttribute() {
        return date('Y-m-d', strtotime($this->attributes['created_at']));
    }
}
