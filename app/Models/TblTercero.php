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
        'id_tercero_responsable',
        'logo',
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

    public function tblterceroresponsable() {
        return $this->belongsTo(TblTercero::class, 'id_tercero_responsable');
    }

    public function tbluser() {
        return $this->hasOne(TblUsuario::class, 'id_tercero', 'id_tercero');
    }

    public function tblusuario() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }

    public function getFullNameFormAttribute() {
        return "{$this->attributes['nombres']} {$this->attributes['apellidos']}";
    }

    public function getFullNameAttribute(){
        return ($this->attributes['razon_social'] != ''
            ? $this->attributes['razon_social']
            : "{$this->attributes['nombres']} {$this->attributes['apellidos']}"
        );
    }

    public function getEstadoFormAttribute() {
        return $this->attributes['estado'];
    }

    public function getEstadoAttribute() {
        $status = $this->attributes['estado'] == 1 ? '<i class="fa-solid fa-check fw-bolder fs-4 text-success"></i>' : '<i class="fa-solid fa-xmark fw-bolder fs-4 text-danger"></i>';
        return $status;
    }

    public static function getTipoTerceros() {
        $tipo_terceros = TblDominio::getListaDominios(session('id_dominio_tipo_tercero'), 'nombre');
        if(auth()->user()->role !== session('id_dominio_super_administrador')) {
            $tipo_terceros = $tipo_terceros->filter(function($value, $key) {
                return $key != session('id_dominio_super_administrador');
            });
        } else if(!in_array(auth()->user()->role, [session('id_dominio_super_administrador'), session('id_dominio_administrador')])) {
            $tipo_terceros = $tipo_terceros->filter(function($value, $key) {
                return $key != session('id_dominio_administrador');
            });
        }

        return $tipo_terceros;
    }

    public static function getTercerosTipo($id_dominio_tipo) {
        $nombre_tercero = (in_array($id_dominio_tipo, [session('id_dominio_cliente'), session('id_dominio_proveedor')])
            ? "COALESCE(razon_social, CONCAT(nombres, ' ', apellidos))"
            : "CONCAT(t.nombres, ' ', t.apellidos)"
        );

        return DB::table('tbl_terceros', 't')
            ->join('tbl_dominios as doc', 't.id_dominio_tipo_documento', '=', 'doc.id_dominio')
            ->select('t.id_tercero',
                DB::raw("$nombre_tercero as nombre")
            )->where(['t.estado' => 1, 't.id_dominio_tipo_tercero' => $id_dominio_tipo])
            ->orderBy(DB::raw($nombre_tercero), 'asc')
            ->pluck('nombre', 'id_tercero');
    }

    public static function getRules() {
        return [
            '0' => 'required|exists:tbl_dominios,nombre',
            '1' => 'required|max:255|unique:tbl_terceros,documento',
            '2' => 'nullable',
            '3' => 'nullable|string|max:255',
            '4' => 'required|string|max:255',
            '5' => 'required|string|max:255',
            '6' => 'required|string|max:255',
            '7' => 'required|max:255',
            '8' => 'required|email|unique:tbl_terceros,correo',
            '9' => 'required|max:255',
            '10' => 'required|exists:tbl_dominios,nombre',
            '11' => 'nullable|exists:tbl_terceros,documento'
        ];
    }

    public static function getProperties() {
        return [
            '0' => 'Tipo documento',
            '1' => 'Documento',
            '2' => 'DV',
            '3' => 'Razón social',
            '4' => 'Nombres',
            '5' => 'Apellidos',
            '6' => 'Ciudad',
            '7' => 'Dirección',
            '8' => 'Correo',
            '9' => 'Teléfono',
            '10' => 'Tipo tercero',
            '11' => 'Dependencia'
        ];
    }

    public static function createRow(array $row) {
        $tipo_documento = trim(($row[0]));
        $documento = trim($row[1]);
        $digito = trim($row[2]);
        $razon = trim($row[3]);
        $nombres = trim($row[4]);
        $apellidos = trim($row[5]);
        $ciudad = trim($row[6]);
        $direccion = trim($row[7]);
        $correo = trim($row[8]);
        $telefono = trim($row[9]);
        $tipo_tercero = trim(($row[10]));
        $dependencia = trim($row[11]);

        $parametro_documentos = TblParametro::where(['llave' => 'id_dominio_tipo_documento'])->first()->valor;
        $parametro_terceros = TblParametro::where(['llave' => 'id_dominio_tipo_tercero'])->first()->valor;

        $tipoDocumento = TblDominio::where(['nombre' => $tipo_documento, 'id_dominio_padre' => $parametro_documentos])->first();
        $tipoTercero = TblDominio::where(['nombre' => $tipo_tercero, 'id_dominio_padre' => $parametro_terceros])->first();
        $responsable = TblTercero::where(['documento' => $dependencia])->first();
        
        return new TblTercero([
            'id_dominio_tipo_documento' => (isset($tipoDocumento->id_dominio) ? $tipoDocumento->id_dominio : null),
            'documento' => $documento,
            'dv' => $digito,
            'razon_social' => $razon,
            'nombres' => $nombres,
            'apellidos' => $apellidos,
            'ciudad' => $ciudad,
            'direccion' => $direccion,
            'correo' => $correo,
            'telefono' => $telefono,
            'id_dominio_tipo_tercero' => (isset($tipoTercero->id_dominio) ? $tipoTercero->id_dominio : null),
            'id_tercero_responsable' => (isset($responsable->id_tercero) ? $responsable->id_tercero : null),
            'id_usuareg' => auth()->id()
        ]);
    }
}
