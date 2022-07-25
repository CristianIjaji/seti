<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblFactura extends Model
{
    use HasFactory;

    protected $table = 'tbl_facturas';
    protected $primaryKey = 'id_factura';

    protected $fillable = [
        'numero_factura',
        'id_estado_factura',
        'id_proveedor',
        'dias_pago',
        'id_usuareg'
    ];

    public function tblestadofactura() {
        return $this->belongsTo(TblDominio::class, 'id_estado_factura');
    }

    public function tblproveedor() {
        return $this->belongsTo(TblTercero::class, 'id_proveedor');
    }

    public function tblusuario() {
        return $this->belongsTo(tblusuario::class, 'id_usuareg');
    }
}
