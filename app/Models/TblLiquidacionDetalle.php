<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblLiquidacionDetalle extends Model
{
    use HasFactory;

    protected $table = 'tbl_liquidaciones_detalle';
    protected $primaryKey = 'id_liquidacion_detalle';
    protected $guarded = [];

    protected $fillable = [
        'id_liquidacion',
        'id_dominio_tipo_item',
        'id_lista_precio',
        'descripcion',
        'unidad',
        'cantidad',
        'valor_unitario',
        'valor_total',
    ];

    public function tblliquidacion() {
        return $this->belongsTo(TblLiquidacion::class, 'id_liquidacion');
    }

    public function tbldominioitem() {
        return $this->belongsTo(TblDominio::class, 'id_dominio_tipo_item');
    }

    public function tblListaprecio() {
        return $this->belongsTo(TblListaPrecio::class, 'id_lista_precio');
    }
}
