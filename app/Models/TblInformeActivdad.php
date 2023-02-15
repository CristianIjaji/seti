<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TblInformeActivdad extends Model
{
    use HasFactory;

    protected $table = 'tbl_informes_actividades';
    protected $primaryKey = 'id_informe_actividad';
    protected $guarded = [];

    protected $fillable = [
        'id_actividad',
        'link',
        'id_usuareg'
    ];

    public function tblactividad() {
        return $this->belongsTo(TblActividad::class, 'id_actividad');
    }

    public function tblusuario() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }
}
