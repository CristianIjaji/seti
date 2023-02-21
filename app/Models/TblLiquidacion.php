<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class TblLiquidacion extends Model
{
    use HasFactory;

    protected $table = "tbl_liquidaciones";
    protected $primaryKey = 'id_liquidacion';
    protected $guarded = [];

    protected $fillable = [
        'id_actividad',
        'valor',
        'id_dominio_estado',
        'id_usuareg'
    ];

    public function tblliquidaciondetalle() {
        return $this->hasMany(TblLiquidacionDetalle::class, 'id_liquidacion');
    }

    public function tblactividad() {
        return $this->belongsTo(TblActividad::class, 'id_actividad');
    }

    public function tbldominioestado() {
        return $this->belongsTo(TblDominio::class, 'id_dominio_estado');
    }

    public function tblusereg() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }

    public static function getDetalleLiquidacion($actividad) {
        $carrito = [];
        $items = TblLiquidacion::where(['id_actividad' => $actividad->id_actividad])->first();

        if(!$items) {
            $items = TblCotizacionDetalle::with(['tblListaprecio'])->where(['id_cotizacion' => $actividad->id_cotizacion])->get();
        } else {
            $items = $items->tblliquidaciondetalle;
        }

        foreach($items as $item) {
            $carrito[$item->id_dominio_tipo_item][$item->id_lista_precio] = [
                'item' => $item->tblListaprecio->codigo,
                'descripcion' => $item->descripcion,
                'cantidad' => $item->cantidad,
                'unidad' => $item->unidad,
                'valor_unitario' => $item->valor_unitario,
                'valor_total' => $item->valor_total,
            ];
        }

        return $carrito;
    }
}
