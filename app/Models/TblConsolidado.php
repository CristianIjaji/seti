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
        'id_mes',
        'observacion',
        'id_estado_consolidado',
        'id_usuareg'
    ];

    public function tblusuario() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }

    public function tblmes() {
        return $this->belongsTo(TblDominio::class, 'id_mes');
    }

    public function tblestadoconsolidado() {
        return $this->belongsTo(TblDominio::class, 'id_estado_consolidado');
    }

}
