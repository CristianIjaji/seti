<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblListaPrecio extends Model
{
    use HasFactory;

    protected $table = 'tbl_lista_precios';
    protected $primaryKey = 'id_lista_precio';
    protected $guarded = [];

    protected $fillable = [
        'id_cliente',
        'id_tipo_item',
        'codigo',
        'descripcion',
        'unidad',
        'cantidad',
        'valor_unitario',
        'estado',
        'id_usuareg',
    ];

    public function tbltercerocliente() {
        return $this->belongsTo(TblTercero::class, 'id_cliente');
    }
   
    public function tbldominioitem() {
        return $this->belongsTo(TblDominio::class, 'id_tipo_item');
    }
   
    public function tblusuario() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }

    public function getValorUnitarioAttribute() {
        return (isset($this->attributes['valor_unitario'])) ? number_format($this->attributes['valor_unitario'], 2) : 0;
    }

    public function getValorUnitarioFormAttribute() {
        return (isset($this->attributes['valor_unitario'])) ? $this->attributes['valor_unitario'] : 0;
    }

    public function getEstadoFormAttribute() {
        return $this->attributes['estado'];
    }

    public function getEstadoAttribute() {
        $status = $this->attributes['estado'] == 1 ? '<i class="fa-solid fa-check fw-bolder fs-4 text-success"></i>' : '<i class="fa-solid fa-xmark fw-bolder fs-4 text-danger"></i>';
        return $status;
    }
}
