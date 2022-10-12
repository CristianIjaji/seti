<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblConsolidado extends Model
{
    use HasFactory;

    protected $table = 'tbl_consolidados';
    protected $primaryKey = 'id_consolidado';

    protected $fillable = [
        'id_cliente',
        'mes',
        'id_estado_consolidado',
        'id_responsable_cliente',
        'id_usuareg'
    ];

    public function tblcliente() {
        return $this->belongsTo(TblTercero::class, 'id_cliente');
    }

    public function tblestadoconsolidado() {
        return $this->belongsTo(TblDominio::class, 'id_estado_consolidado');
    }

    public function tblresponsablecliente() {
        return $this->belongsTo(TblTercero::class, 'id_responsable_cliente');        
    }

    public function tblusuario() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }
}
