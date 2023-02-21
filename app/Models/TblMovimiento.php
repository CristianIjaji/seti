<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMovimiento extends Model
{
    use HasFactory;

    protected $table = 'tbl_movimientos';
    protected $primaryKey = 'id_movimiento';
    protected $guarded = [];

    protected $fillable = [
        'id_dominio_tipo_movimiento',
        'id_tercero_recibe',
        'id_tercero_entrega',
        'documento',
        'observaciones',
        'id_dominio_iva',
        'total',
        'saldo',
        'id_dominio_estado',
        'id_usuareg'
    ];

    public function tbltipomovimiento() {
        return $this->belongsTo(TblDominio::class, 'id_dominio_tipo_movimiento');
    }

    public function tblmovimientodetalle() {
        return $this->hasMany(TblMovimientoDetalle::class, 'id_movimiento');
    }

    public function tbltercerorecibe() {
        return $this->belongsTo(TblTercero::class, 'id_tercero_recibe');
    }

    public function tblterceroentrega() {
        return $this->belongsTo(TblTercero::class, 'id_tercero_entrega');
    }

    public function tbliva() {
        return $this->belongsTo(TblDominio::class, 'id_dominio_iva');
    }

    public function tblestado() {
        return $this->belongsTo(TblDominio::class, 'id_dominio_estado');
    }

    public function getFechaMovimientoAttribute() {
        return date('Y-m-d', strtotime($this->attributes['created_at']));
    }

    public function getTotalAttribute() {
        return number_format(isset($this->attributes['total']) && $this->attributes['total'] > 0 ? $this->attributes['total'] : 0, 2);
    }
    
    public function getSaldoAttribute() {
        return number_format(isset($this->attributes['saldo']) && $this->attributes['saldo'] > 0 ? $this->attributes['saldo'] : 0, 2);
    }

    public function getEstadoAttribute() {
        return $this->attributes['id_dominio_estado'];
    }

    public function getDetalleMovimiento() {
        $carrito = [];
        $items = TblMovimientoDetalle::with(['tblinventario'])->where(['id_movimiento' => (isset($this->attributes['id_movimiento']) ? $this->attributes['id_movimiento'] : -1)])->get();

        foreach ($items as $item) {
            $carrito[session('id_dominio_tipo_movimiento')][$item->id_inventario] = [
                'item' => $item->id_inventario,
                'descripcion' => $item->tblinventario->descripcion,
                'cantidad' => $item->cantidad,
                // 'unidad' => $item->tblinventario->unidad,
                'valor_unitario' => $item->valor_unitario,
                'valor_total' => $item->valor_total,
            ];
        }

        return $carrito;
    }
}
