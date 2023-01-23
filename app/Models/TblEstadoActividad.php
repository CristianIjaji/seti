<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblEstadoActividad extends Model
{
    use HasFactory;

    protected $table = 'tbl_estado_actividad';
    protected $primaryKey ='id_dominio_estado';
    protected $guarded =[];

    protected $filable = [
        'id_actividad',
        'estado',
        'comentario',
        'id_usuareg',
    ];

    public function tblActividad(){
        return $this->belongsTo(TblActividad::class, 'id_actividad');
    }
    public function tblestado(){
        return $this->belongsTo(TblDominio::class, 'estado');
    }
    public function tblusuario(){
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }
    public function getFullNameAttribute(){
        return $this->tblusuario->tbltercero->full_name;
    }
}
