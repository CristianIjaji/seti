<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblOrdenCompra extends Model
{
    use HasFactory;

    protected $table = 'tbl_ordenes_compra';
    protected $primaryKey = 'id_orden_compra';

    protected $fillable = [
        'codigo_orden',
        'id_tipo',
        'descripcion',
        'id_proveedor',
        'id_modalidad_pago',
        'id_estado',
        'id_asesor',
        'vencimiento',
        'cupo',
        'id_usuareg'
    ];

    public function tbltipo() {
        return $this->belongsTo(TblDominio::class, 'id_tipo');
    }

    public function tblproveedor() {
        return $this->belongsTo(TblTercero::class, 'id_proveedor');
    }

    public function tblmodalidadpago() {
        return $this->belongsTo(TblDominio::class, 'id_modalidad_pago');
    }

    public function tblestado() {
        return $this->belongsTo(TblDominio::class, 'id_estado');
    }

    public function tblasesor() {
        return $this->belongsTo(TblTercero::class, 'id_asesor');
    }

    public function tblusuario() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }
}
