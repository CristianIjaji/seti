<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblConfiguracion extends Model
{
    use HasFactory;

    protected $table = 'tbl_configuracion_cliente';
    protected $primaryKey = 'id_configuracion_cliente';
    protected $guarded = [];

    protected $filable = [
        'id_tercero_cliente',
        'impresora',
        'id_dominio_recibo',
        'servicios',
        'logo',
        'estado',
        'id_usuareg'
    ];

    public function tbltercero() {
        return $this->belongsTo(TblTercero::class, 'id_tercero_cliente');
    }

    public function tbldominiorecibo() {
        return $this->belongsTo(TblDominio::class, 'id_dominio_recibo');
    }

    public function tbluser() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }
}
