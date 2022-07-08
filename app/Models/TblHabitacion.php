<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblHabitacion extends Model
{
    use HasFactory;

    protected $table = 'tbl_habitaciones';
    protected $primaryKey = 'id_habitacion';
    protected $guarded = [];

    protected $filable = [
        'id_tercero_cliente',
        'nombre',
        'cantidad',
        'estado',
        'id_usuareg'
    ];

    public function tbltercero() {
        return $this->belongsTo(TblTercero::class, 'id_tercero_cliente');
    }

    public function tblUsuario() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }

    public function getEstadoFormAttribute() {
        return $this->attributes['estado'];
    }

    public function getEstadoAttribute() {
        $status = $this->attributes['estado'] == 1 ? '<i class="fa-solid fa-check fw-bolder fs-4 text-success"></i>' : '<i class="fa-solid fa-xmark fw-bolder fs-4 text-danger"></i>';
        return $status;
    }
}
