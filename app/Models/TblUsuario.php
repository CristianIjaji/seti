<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class TblUsuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = "tbl_usuarios";
    protected $primaryKey = "id_usuario";
    protected $guarded = [];

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

    public static function getMenusPerfil() {
        $menus = DB::table('tbl_menu_tipo_tercero', 't')
            ->join('tbl_menus as m', 't.id_menu', '=', 'm.id_menu')
            ->select('m.id_menu', 'm.url', 'm.icon', 'm.nombre', 'm.id_menu_padre', 'm.orden')
            ->where(['m.estado' => 1, 't.id_dominio_tipo_tercero' => auth()->user()->role])
            ->orderBy(DB::raw('COALESCE(id_menu_padre, orden)', 'asc'))
            ->get();
        
        $menus_asignados = [];
        foreach ($menus as $menu) {
            if(!isset($menus_asignados[$menu->id_menu]) && !$menu->id_menu_padre) {
                $menus_asignados[$menu->id_menu] = [];
                $menus_asignados[$menu->id_menu] = $menu;
            }

            if($menu->id_menu_padre && isset($menus_asignados[$menu->id_menu_padre])) {
                if(!isset($menus_asignados[$menu->id_menu_padre]->submenu)) {
                    $menus_asignados[$menu->id_menu_padre]->submenu = [];
                }

                $menus_asignados[$menu->id_menu_padre]->submenu[] = $menu;
            }
        }

        return $menus_asignados;
    }

    public static function getPermisosMenu($menu) {
        return TblMenuTipoTercero::where(['id_menu' => TblMenu::where(['url' => $menu])->first()->id_menu, 'id_dominio_tipo_tercero' => auth()->user()->role])->first();
    }

    public function getMimesTypeAttribute() {
        return ['.jpe', '.jpg', '.jpeg', '.png'];
    }
}
