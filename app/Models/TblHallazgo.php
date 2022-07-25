<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblHallazgo extends Model
{
    use HasFactory;

    protected $table = 'tbl_hallazgos';
    protected $primaryKey = 'id_hallazgo';

    protected $fillable = [
        'id_actividad',
        'id_supervisor',
        'hallazgo',
        'id_estado_hallazgo',
        'id_usuareg'
    ];

    public function tblactividad() {
        return $this->belongsTo(TblActividad::class, 'id_actividad');
    }

    public function tblsupervisor() {
        return $this->belongsTo(TblTercero::class, 'id_supervisor');
    }

    public function tblestadohallazgo() {
        return $this->belongsTo(TblDominio::class, 'id_estado_hallazgo');
    }

    public function tblusuario() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }
}
