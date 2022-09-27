<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TblDominio extends Model
{
    use HasFactory;

    protected $table = "tbl_dominios";
    protected $primaryKey = "id_dominio";
    protected $guarded = [];

    protected $fillable = [
        'nombre',
        'descripcion',
        'id_dominio_padre',
        'estado',
        'id_usuareg'
    ];

    public static function getAdminRoles() {
        return Auth::user()->role == session('id_dominio_super_administrador')
            ? [0]
            : (Auth::user()->role == session('id_dominio_administrador')
                ? [session('id_dominio_super_administrador')]
                : [session('id_dominio_super_administrador'), session('id_dominio_administrador')]
        );
    }

    public function tblusuario() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }

    public function tbldominio() {
        return $this->belongsTo(TblDominio::class, 'id_dominio_padre');
    }

    public function getEstadoFormAttribute() {
        return $this->attributes['estado'];
    }

    public function getEstadoAttribute() {
        $status = $this->attributes['estado'] == 1 ? '<i class="fa-solid fa-check fw-bolder fs-4 text-success"></i>' : '<i class="fa-solid fa-xmark fw-bolder fs-4 text-danger"></i>';
        return $status;
    }

    public static function getListaDominios($id_domiino_padre, $orderBy = '', $sort = 'ASC') {
        return ($orderBy != ''
            ? TblDominio::where(['estado' => 1, 'id_dominio_padre' => $id_domiino_padre])->orderBy($orderBy, $sort)->pluck('nombre', 'id_dominio')
            : TblDominio::where(['estado' => 1, 'id_dominio_padre' => $id_domiino_padre])->pluck('nombre', 'id_dominio')
        );
    }
}
