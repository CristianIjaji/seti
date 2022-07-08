<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class TblOrdenTrack extends Model
{
    use HasFactory;

    protected $table = 'tbl_orden_track';
    protected $primaryKey = 'id_orden_track';
    protected $guarded = [];

    protected $filable = [
        'id_orden',
        'id_dominio_accion',
        'id_usuareg'
    ];

    public function tbldominio() {
        return $this->belongsTo(TblDominio::class, 'id_dominio_accion');
    }
    
    public function getOrdenColaAttribute() {
        return [
            'title' => 'Orden en cola',
            'time' => null,
            'completed' => '',
            'icon' => '<i class="fa-solid fa-hourglass-end"></i>',
        ];
    }

    public function getOrdenAceptadaAttribute() {
        return [
            'title' => 'Orden aceptada',
            'time' => null,
            'completed' => '',
            'icon' => '<i class="fa-solid fa-check"></i>',
        ];
    }

    public function getOrdenRechazadaAttribute() {
        return [
            'title' => 'Orden rechazada por restaurante',
            'time' => null,
            'completed' => '',
            'icon' => '<i class="fa-solid fa-xmark"></i>',
            'fail' => false,
        ];
    }

    public function getOrdenCaminoAttribute() {
        return [
            'title' => 'Orden en camino',
            'time' => null,
            'completed' => '',
            'icon' => '<i class="fa-solid fa-motorcycle"></i>',
        ];
    }

    public function getOrdenEntregadaAttribute() {
        return [
            'title' => 'Orden entregada',
            'time' => null,
            'completed' => '',
            'icon' => '<i class="fa-regular fa-thumbs-up"></i>',
            'finish' => true,
        ];
    }

    public function getOrdenDevueltaAttribute() {
        return [
            'title' => 'Orden rechazada por cliente',
            'time' => null,
            'completed' => '',
            'icon' => '<i class="fa-regular fa-thumbs-down"></i>',
            'fail' => false
        ];
    }

    public function getOrdenAceptadaDomiciliarioAttribute() {
        return [
            'title' => 'Solicita domiciliario',
            'time' => null,
            'completed' => '',
            'icon' => '<i class="fa-solid fa-truck-arrow-right" data-fa-transform="shrink-8"></i>',
            'fail' => false
        ];
    }

    public function getOrdenCompletadaAttribute() {
        return [
            'title' => 'Orden completada',
            'time' => null,
            'completed' => '',
            'icon' => '<i class="fa-regular fa-thumbs-up"></i>',
            'finish' => true,
        ];
    }
}
