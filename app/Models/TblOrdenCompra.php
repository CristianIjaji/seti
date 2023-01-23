<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblOrdenCompra extends Model
{
    use HasFactory;

    protected $table = 'tbl_ordenes_compra';
    protected $primaryKey = 'id_orden_compra';
    protected $guarded = [];

    protected $fillable = [
        'id_tercero_almacen',
        'id_tercero_proveedor',
        'id_dominio_tipo',
        'descripcion',
        'id_dominio_modalidad_pago',
        'id_dominio_iva',
        'id_dominio_estado',
        'id_tercero_asesor',
        'vencimiento',
        'cupo_actual',
        'id_usuareg'
    ];

    public function tblalmacen() {
        return $this->belongsTo(TblTercero::class, 'id_tercero_almacen');
    }

    public function tbltipo() {
        return $this->belongsTo(TblDominio::class, 'id_dominio_tipo');
    }

    public function tblproveedor() {
        return $this->belongsTo(TblTercero::class, 'id_tercero_proveedor');
    }

    public function tblmodalidadpago() {
        return $this->belongsTo(TblDominio::class, 'id_dominio_modalidad_pago');
    }

    public function tbliva() {
        return $this->belongsTo(TblDominio::class, 'id_dominio_iva');
    }

    public function tblestado() {
        return $this->belongsTo(TblDominio::class, 'id_dominio_estado');
    }

    public function tblasesor() {
        return $this->belongsTo(TblTercero::class, 'id_tercero_asesor');
    }

    public function tblusuario() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }

    public function tbldetalleorden() {
        return $this->hasMany(TblOrdenCompraDetalle::class, 'id_orden_compra');
    }
}
