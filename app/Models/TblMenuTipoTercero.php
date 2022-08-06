<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TblMenuTipoTercero extends Model
{
    use HasFactory;

    protected $table = "tbl_menu_tipo_tercero";
    protected $primaryKey = "id_menu_tipo_tercero";
    protected $guarded = [];

    protected $fillable = [
        'id_menu',
        'id_tipo_tercero',
        'crear',
        'editar',
        'ver',
        'importar',
        'exportar'
    ];

    public function tblmenu() {
        return $this->belongsTo(TblMenu::class, 'id_menu');
    }

    public function tbltipotercero() {
        return $this->belongsTo(TblDominio::class, 'id_tipo_tercero');
    }

    public function getCrearAttribute() {
        return $this->attributes['crear'] == 1 ? '<i class="fa-solid fa-check fw-bolder fs-4 text-success"></i>' : '<i class="fa-solid fa-xmark fw-bolder fs-4 text-danger"></i>';
    }

    public function getEditarAttribute() {
        return $this->attributes['editar'] == 1 ? '<i class="fa-solid fa-check fw-bolder fs-4 text-success"></i>' : '<i class="fa-solid fa-xmark fw-bolder fs-4 text-danger"></i>';
    }

    public function getVerAttribute() {
        return $this->attributes['ver'] == 1 ? '<i class="fa-solid fa-check fw-bolder fs-4 text-success"></i>' : '<i class="fa-solid fa-xmark fw-bolder fs-4 text-danger"></i>';
    }

    public function getImportarAttribute() {
        return $this->attributes['importar'] == 1 ? '<i class="fa-solid fa-check fw-bolder fs-4 text-success"></i>' : '<i class="fa-solid fa-xmark fw-bolder fs-4 text-danger"></i>';
    }

    public function getExportarAttribute() {
        return $this->attributes['exportar'] == 1 ? '<i class="fa-solid fa-check fw-bolder fs-4 text-success"></i>' : '<i class="fa-solid fa-xmark fw-bolder fs-4 text-danger"></i>';
    }

    public function getViewAttribute() {
        return $this->attributes['ver'];
    }

    public function getCreateAttribute() {
        return $this->attributes['crear'];
    }

    public function getUpdateAttribute() {
        return $this->attributes['editar'];
    }

    public function getImportAttribute() {
        return $this->attributes['importar'];
    }

    public function getExportAttribute() {
        return $this->attributes['exportar'];
    }
}
