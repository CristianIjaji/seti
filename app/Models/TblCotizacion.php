<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblCotizacion extends Model
{
    use HasFactory;

    protected $table = 'tbl_cotizaciones';
    protected $primaryKey = 'id_cotizacion';
    protected $guarded = [];

    protected $fillable = [
        'ot_trabajo',
        'id_cliente',
        'id_estacion',
        'id_tipo_trabajo',
        'fecha_solicitud',
        'fecha_envio',
        'id_prioridad',
        'estado',
        'id_proceso',
        'id_responsable_cliente',
        'valor',
        'iva',
        'descripcion',
        'valor_reasignado',
        'id_usuareg',
    ];
}
