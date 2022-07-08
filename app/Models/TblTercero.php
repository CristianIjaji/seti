<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblTercero extends Model
{
    use HasFactory;

    protected $table = 'tbl_terceros';
    protected $primaryKey = 'id_tercero';
    protected $guarded = [];

    protected $fillable = [
        'id_dominio_tipo_documento',
        'documento',
        'dv',
        'razon_social',
        'nombres',
        'apellidos',
        'ciudad',
        'direccion',
        'correo',
        'telefono',
        'id_dominio_tipo_tercero',
        'estado',
        'id_usuareg'
    ];

    public function tbldominiodocumento() {
        return $this->belongsTo(TblDominio::class, 'id_dominio_tipo_documento');
    }

    public function tbldominiotercero() {
        return $this->belongsTo(TblDominio::class, 'id_dominio_tipo_tercero');
    }

    public function tbluser() {
        return $this->belongsTo(TblUsuario::class, 'id_tercero');
    }

    public function tblusuario() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }

    public function tblconfiguracion() {
        return $this->belongsTo(TblConfiguracion::class, 'id_tercero');
    }

    public function getFullNameAttribute(){
        return "{$this->attributes['nombres']} {$this->attributes['apellidos']}";
    }

    public function getEstadoFormAttribute() {
        return $this->attributes['estado'];
    }

    public function getEstadoAttribute() {
        $status = $this->attributes['estado'] == 1 ? '<i class="fa-solid fa-check fw-bolder fs-4 text-success"></i>' : '<i class="fa-solid fa-xmark fw-bolder fs-4 text-danger"></i>';
        return $status;
    }

    public function getServiciosAttribute() {
        return (isset($this->tbluser)
            ? $this->tbluser->servicios
            : ""
        );
    }
}
