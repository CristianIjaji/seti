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
        'codio',
        'descripcion',
        'unidad',
        'cantidad',
        'valor_unitario',
        'estado',
        'id_usuareg',
    ];

    public function tbltercerocliente() {
        return $this->belongsTo(TblTecero::class, 'id_cliente');
    }
   
    public function tbldominioitem() {
        return $this->belongsTo(TblDominio::class, 'id_tipo_item');
    }
   
    public function tblusuario() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }

}
