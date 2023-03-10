<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMenu extends Model
{
    use HasFactory;

    protected $table = "tbl_menus";
    protected $primaryKey = "id_menu";
    protected $guarded = [];

    protected $fillable = [
        'id_menu_padre',
        'url',
        'icon',
        'nombre',
        'estado',
        'orden',
        'id_usuareg'
    ];

    public function tblmenupadre() {
        return $this->belongsTo(TblMenu::class, 'id_menu_padre');
    }

    public function tblusuario() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }

    public function getNombreAttribute() {
        return "<div class='row'>
            <i class='col-2 text-center ".$this->attributes['icon']." fs-5 text-primary'></i>
            <div class='col-10'>".$this->attributes['nombre']."</div>
        </div>";
    }

    // public function getSubmenuAttribute() {
    //     return $this->belongsTo(TblMenu::class, )
    // }

    public function getNombreFormAttribute() {
        return $this->attributes['nombre'];
    }
}
