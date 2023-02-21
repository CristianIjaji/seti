<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TblConsolidado extends Model
{
    use HasFactory;

    protected $table = 'tbl_consolidados';
    protected $primaryKey = 'id_consolidado';

    protected $fillable = [
        'id_tercero_cliente',
        'mes',
        'id_dominio_estado',
        'id_tercero_responsable',
        'id_usuareg'
    ];

    public function tblcliente() {
        return $this->belongsTo(TblTercero::class, 'id_tercero_cliente');
    }

    public function tblestadoconsolidado() {
        return $this->belongsTo(TblDominio::class, 'id_dominio_estado');
    }

    public function tblresponsablecliente() {
        return $this->belongsTo(TblTercero::class, 'id_tercero_responsable');        
    }

    public function tblusuario() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }

    public function tblconsolidadodetalle() {
        return $this->hasMany(TblConsolidadoDetalle::class, 'id_consolidado');
    }

    public function getMesAttribute() {
        return (isset($this->attributes['mes'])
            ? Carbon::createFromFormat('Y-m-d', $this->attributes['mes'])->isoFormat('Y-MMMM')
            : ''
        );
    }

    public function getNombreMesAttribute() {
        return ucfirst(Carbon::createFromFormat('Y-m-d', $this->attributes['mes'])->isoFormat('MMMM'));
    }

    public function getMesFormAttribute() {
        return isset($this->attributes['mes']) ? $this->attributes['mes'] : '';
    }

    public static function getCarritoConsolidado($id_tercero_cliente, $id_tercero_responsable, $id_consolidado) {
        return DB::select("
            SELECT
                act.id_actividad
            FROM tbl_actividades as act
            
        ");
    }
}
