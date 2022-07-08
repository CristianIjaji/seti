<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class TblOrden extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tbl_ordenes';
    protected $primaryKey = 'id_orden';
    protected $guarded = [];

    protected $filable = [
        'id_tercero_cliente',
        'id_dominio_tipo_orden',
        'fecha_inicio',
        'fecha_fin',
        'id_habitacion',
        'habitaciones',
        'descripcion',
        'datos_cliente',
        'pedir_domiciliario',
        'id_dominio_tiempo_llegada',
        'metodo_pago',
        'valor',
        'estado',
        'id_usuareg'
    ];

    public function tbltercero() {
        return $this->belongsTo(TblTercero::class, 'id_tercero_cliente');
    }

    public function tbldominio() {
        return $this->belongsTo(TblDominio::class, 'id_dominio_tipo_orden');
    }

    public function tblusuario() {
        return $this->belongsTo(tblusuario::class, 'id_usuareg');
    }

    public function tblhabitacion() {
        return $this->belongsTo(TblHabitacion::class, 'id_habitacion');
    }

    public function getValorAttribute() {
        return (isset($this->attributes['valor'])) ? number_format($this->attributes['valor'], 2) : 0;
    }

    public function getAsociadoAttibute() {
        return $this->tbltercero->razon_social;
    }

    public function getDatosClienteFormAttribute() {
        return (isset($this->attributes['datos_cliente']) ? explode("\n", $this->attributes['datos_cliente']) : '');
    }

    protected function tblordentrack() {
        return $this->belongsTo(TblOrdenTrack::class, 'id_orden');
    }

    public function getFullNameAttribute() {
        return $this->tblusuario->tbltercero->full_name;
    }

    public function getFechaInicioAttribute() {
        return (isset($this->attributes['fecha_inicio'])
            ? ((isset($this->attributes['id_dominio_tipo_orden']) ? in_array($this->attributes['id_dominio_tipo_orden'], [session('id_dominio_reserva_hotel')]) : "")
                ? date('Y/m/d', strtotime($this->attributes['fecha_inicio']))
                : date('Y/m/d H:i:s', strtotime($this->attributes['fecha_inicio']))
            )
            : ""
        );
    }

    public function getFechaFinAttribute() {
        return (isset($this->attributes['fecha_fin'])
            ? ((isset($this->attributes['id_dominio_tipo_orden']) ? in_array($this->attributes['id_dominio_tipo_orden'], [session('id_dominio_reserva_hotel')]) : "")
                ? date('Y/m/d', strtotime($this->attributes['fecha_fin']))
                : date('Y/m/d H:i:s', strtotime($this->attributes['fecha_fin']))
            )
            : ""
        );
    }

    public function flujoOrden() {
        switch ($this->attributes['id_dominio_tipo_orden']) {
            case session('id_dominio_domicilio'):
                return $this->domicilio();
                break;
            case session('id_dominio_reserva_hotel'):
                return $this->hotel();
                break;
            case session('id_dominio_reserva_restaurante'):
                return $this->restaurante();
                break;
            default:
                return [];
                break;
        }
    }

    private function domicilio() {
        $model = new TblOrdenTrack;
        $estados = [
            session('id_dominio_orden_cola') => [
                session('id_dominio_orden_cola') => $model->ordenCola,
                session('id_dominio_orden_aceptada') => $model->ordenAceptada,
                session('id_dominio_orden_devuelta') => $model->ordenDevuelta,
            ],
            session('id_dominio_orden_aceptada') => [
                session('id_dominio_orden_cola') => $model->ordenCola,
                session('id_dominio_orden_aceptada') => $model->ordenAceptada,
                session('id_dominio_orden_aceptada_domiciliario') => $model->ordenAceptadaDomiciliario,
                session('id_dominio_orden_camino') => $model->ordenCamino,
                session('id_dominio_orden_entregada') => $model->ordenEntregada,
                session('id_dominio_orden_devuelta') => $model->ordenDevuelta,
            ],
            session('id_dominio_orden_rechazada') => [
                session('id_dominio_orden_cola') => $model->ordenCola,
                session('id_dominio_orden_rechazada') => $model->ordenRechazada,
            ]
        ];

        return $this->getFlujo($estados);
    }

    private function hotel() {
        $model = new TblOrdenTrack;
        $estados = [
            session('id_dominio_orden_cola') => [
                session('id_dominio_orden_cola') => $model->ordenCola,
                session('id_dominio_orden_aceptada') => $model->ordenAceptada,
                session('id_dominio_orden_devuelta') => $model->ordenDevuelta,
            ],
            session('id_dominio_orden_aceptada') => [
                session('id_dominio_orden_cola') => $model->ordenCola,
                session('id_dominio_orden_aceptada') => $model->ordenAceptada,
                session('id_dominio_orden_rechazada') => $model->ordenRechazada,
                session('id_dominio_orden_completada') => $model->ordenCompletada,
                session('id_dominio_orden_devuelta') => $model->ordenDevuelta,
            ],
            session('id_dominio_orden_rechazada') => [
                session('id_dominio_orden_cola') => $model->ordenCola,
                session('id_dominio_orden_rechazada') => $model->ordenRechazada,
            ]
        ];

        return $this->getFlujo($estados);
    }

    private function restaurante() {
        $model = new TblOrdenTrack;
        $estados = [
            session('id_dominio_orden_cola') => [
                session('id_dominio_orden_cola') => $model->ordenCola,
                session('id_dominio_orden_aceptada') => $model->ordenAceptada,
                session('id_dominio_orden_devuelta') => $model->ordenDevuelta,
            ],
            session('id_dominio_orden_aceptada') => [
                session('id_dominio_orden_cola') => $model->ordenCola,
                session('id_dominio_orden_aceptada') => $model->ordenAceptada,
                session('id_dominio_orden_devuelta') => $model->ordenDevuelta,
                session('id_dominio_orden_completada') => $model->ordenCompletada,
            ],
            session('id_dominio_orden_rechazada') => [
                session('id_dominio_orden_cola') => $model->ordenCola,
                session('id_dominio_orden_rechazada') => $model->ordenRechazada,
            ]
        ];

        return $this->getFlujo($estados);
    }

    private function getFlujo($estados) {
        $id_padre = 0;
        foreach ($estados as $index => $step) {
            if(!isset($step['title']) && $id_padre == 0) {
                $id_padre = $index;
            } else {
                if(isset($step[$this->attributes['estado']])) {
                    $id_padre = $index;
                    break;
                } else {
                    $id_padre = 0;
                }
            }
        }

        return $this->updateFlujo(isset($estados[$this->attributes['estado']])
            ? $estados[$this->attributes['estado']]
            : $estados[$id_padre]
        ); 
    }

    private function updateFlujo($estados) {
        $tracks = TblOrdenTrack::with(['tbldominio'])->where('id_orden', '=', $this->attributes['id_orden'])->get();

        $fail = false;
        foreach ($tracks as $key => $track) {
            if(isset($estados[$track->id_dominio_accion])) {
                $estados[$track->id_dominio_accion]['time'] = date('d/m/Y H:i', strtotime($track->created_at));
                $estados[$track->id_dominio_accion]['completed'] = 'completed';
                if(isset($estados[$track->id_dominio_accion]['fail'])) {
                    $estados[$track->id_dominio_accion]['fail'] = true;
                    $fail = true;
                }
            }
        }

        $key_anterior = 0;
        // Se recorren los estados para quitar los inactivos
        foreach ($estados as $key => $estado) {
            if(!$estado['completed']) {
                $key_anterior = $key;

                if($fail || isset($estado['fail'])) {
                    unset($estados[$key]);
                }
            } else {
                if($key_anterior > 0) {
                    unset($estados[$key_anterior]);
                    $key_anterior = 0;
                }
            }
        }

        return $estados;
    }

    public function getStatusAttribute() {
        return [
            session('id_dominio_orden_cola') => 'bg-gradient',
            session('id_dominio_orden_rechazada') => 'bg-table-danger bg-gradient text-white fw-bold',
            session('id_dominio_orden_aceptada') => 'bg-table-success bg-gradient text-white fw-bold',
            session('id_dominio_orden_aceptada_domiciliario') => 'bg-table-warning bg-gradient',
            session('id_dominio_orden_camino') => 'bg-table-info bg-gradient',
            session('id_dominio_orden_devuelta') => 'bg-gradient text-danger',
            session('id_dominio_orden_entregada') => 'bg-gradient text-success',
            session('id_dominio_orden_completada') => 'bg-gradient text-success',
        ];
    }
}
