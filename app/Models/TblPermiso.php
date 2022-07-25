<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblPermiso extends Model
{
    use HasFactory;

    protected $table = 'tbl_permisos';
    protected $primaryKey = 'id_permiso';

    protected $fillable = [
        'descripcion',
        'id_estado_permiso',
        'id_usuareg'
    ];

    public function tblestadopermiso() {
        return $this->belongsTo(TblDominio::class, 'id_estado_permiso');
    }

    public function tblusuario() {
        return $this->belongsTo(tblusuario::class, 'id_usuareg');
    }
}
