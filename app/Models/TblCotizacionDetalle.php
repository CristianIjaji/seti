<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblCotizacionDetalle extends Model
{
    use HasFactory;
    
    protected $table = 'tbl_cotizacion_detalles';
    protected $primaryKey = 'id_cotizacion_detalle';
    protected $guarded = [];

    protected $fillable = [
        'id_tipo_item',
        'id_lista_precio',
        'descripcion',
        'unidad',
        'valor_unitario',
        'valor_total',
    ];

    public function tbldominioitem(){
        return $this->belongsTo(TblDominio::class, 'id_tipo_item');
    }

    public function tblListaprecio(){
        return $this->belongsTo(TblListaPrecio::class, 'id_lista_precio');
    }
}
