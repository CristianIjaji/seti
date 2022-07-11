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
        'codigo_cotizacion',
        'id_cliente',
        'id_estacion',
        'descripcion',
        'fecha_solicitud',
        'fecha_envio',
        'id_prioridad',
        'id_proceso',
        'id_responsable_cliente',
        'valor',
        'iva',
        'observaciones',
        'valor_reasignado',
        'id_usuareg',
    ];
}
