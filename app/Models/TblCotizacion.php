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
        'id_responsable_cliente',
        'valor',
        'iva',
        'descripcion',
        'valor_reasignado',
        'id_usuareg',
    ];

    public function tblCliente() {
        return $this->belongsTo(TblTercero::class, 'id_cliente');
    }

    public function tblEstacion() {
        return $this->belongsTo(TblPuntosInteres::class, 'id_estacion');
    }

    public function tblTipoTrabajo() {
        return $this->belongsTo(TblDominio::class, 'id_tipo_trabajo');
    }

    public function tblPrioridad() {
        return $this->belongsTo(TblDominio::class, 'id_prioridad');
    }

    public function tblIva() {
        return $this->belongsTo(TblDominio::class, 'iva');
    }

    public function tblContratista() {
        return $this->belongsTo(TblTercero::class, 'id_responsable_cliente');
    }

    public function getStatusAttribute() {
        return [
            session('id_dominio_cotizacion_creada') => 'bg-gradient',
            session('id_dominio_cotizacion_devuelta') => 'bg-table-warning bg-gradient text-white fw-bold',
            session('id_dominio_cotizacion_revisada') => 'bg-table-info bg-gradient text-white fw-bold',
            session('id_dominio_cotizacion_enviada') => 'bg-table-success bg-gradient',
            session('id_dominio_cotizacion_pendiente_aprobacion') => 'bg-table-success bg-gradient',
            session('id_dominio_cotizacion_rechazada') => 'bg-gradient text-danger',
            session('id_dominio_cotizacion_cancelada') => 'bg-gradient text-danger',
            session('id_dominio_cotizacion_aprobada') => 'bg-gradient text-success',
        ];
    }
}
