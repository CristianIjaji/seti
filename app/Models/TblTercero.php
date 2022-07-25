<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public function tblmenutipotercero() {
        return $this->belongsTo(TblMenuTipoTercero::class, 'id_dominio_tipo_tercero');
    }

    public function tbluser() {
        return $this->belongsTo(TblUsuario::class, 'id_tercero');
    }

    public function tblusuario() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
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

    public static function getClientesTipo($type) {
        return DB::table('tbl_terceros', 't')
            ->join('tbl_dominios as doc', 't.id_dominio_tipo_documento', '=', 'doc.id_dominio')
            ->select('t.id_tercero',
                DB::raw("CONCAT(t.nombres, ' ', t.apellidos) as nombre")
            )->where('t.id_dominio_tipo_tercero', '=', $type)->pluck('nombre', 'id_tercero');
    }
}
