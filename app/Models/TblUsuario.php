<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class TblUsuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = "tbl_usuarios";
    protected $primaryKey = "id_usuario";
    protected $guarded = [];
    
    public function getNombreAttribute() {
        return (isset($this->tbltercero)
            ? $this->tbltercero->nombres.' '.$this->tbltercero->apellidos
            : null
        );
    }

    public function getRoleAttribute() {
        return (isset($this->tbltercero)
            ? $this->tbltercero->id_dominio_tipo_tercero
            : null
        );
    }

    public function getTitleAttribute() {
        return $this->attributes['usuario'];
    }

    public function getCorreoAttribute() {
        return session('id_dominio_plantilla_correo_default');
    }

    public function tbltercero() {
        return $this->belongsTo(TblTercero::class, 'id_tercero');
    }

    public function tblusuario() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }

    public function setPasswordAttribute($value) {
        $this->attributes['password'] = Hash::make($value);
    }

    public function getEstadoFormAttribute() {
        return $this->attributes['estado'];
    }

    public function getEstadoAttribute() {
        $status = $this->attributes['estado'] == 1 ? '<i class="fa-solid fa-check fw-bolder fs-4 text-success"></i>' : '<i class="fa-solid fa-xmark fw-bolder fs-4 text-danger"></i>';
        return $status;
    }

    public function getMimesTypeAttribute() {
        return ['.jpe', '.jpg', '.jpeg', '.png'];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'usuario',
        'email',
        'id_tercero',
        'password',
        'estado',
        'id_usuareg'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
