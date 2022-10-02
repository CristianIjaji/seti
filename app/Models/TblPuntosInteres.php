<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Nette\Utils\Strings;

class TblPuntosInteres extends Model
{
    use HasFactory;

    protected $table = 'tbl_puntos_interes';
    protected $primaryKey = 'id_punto_interes';
    protected $guarded = [];

    protected $filable = [
        'id_cliente',
        'id_zona',
        'nombre',
        'latitud',
        'longitud',
        'estado',
        'descripcion',
        'id_tipo_transporte',
        'id_tipo_accesso',
        'id_usuareg'
    ];

    public function tblcliente() {
        return $this->belongsTo(TblTercero::class, 'id_cliente');
    }

    public function tbldominiozona() {
        return $this->belongsTo(TblDominio::class, 'id_zona');
    }

    public function tbldominiotransporte() {
        return $this->belongsTo(TblDominio::class, 'id_tipo_transporte');
    }

    public function tbldominioacceso() {
        return $this->belongsTo(TblDominio::class, 'id_tipo_accesso');
    }

    public function tblusuario() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }

    public function getEstadoFormAttribute() {
        return $this->attributes['estado'];
    }

    public function getEstadoAttribute() {
        $status = $this->attributes['estado'] == 1 ? '<i class="fa-solid fa-check fw-bolder fs-4 text-success"></i>' : '<i class="fa-solid fa-xmark fw-bolder fs-4 text-danger"></i>';
        return $status;
    }

    public static function getRules() {
        return [
            '0' => 'required|exists:tbl_terceros,documento',
            '1' => 'required|exists:tbl_dominios,nombre',
            '2' => 'required|string|max:255|unique:tbl_puntos_interes,nombre',
            '3' => 'nullable',
            '4' => 'nullable',
            '5' => 'required',
            '6' => 'required|exists:tbl_dominios,nombre',
            '7' => 'required|exists:tbl_dominios,nombre'
        ];
    }

    public static function getProperties() {
        return [
            '0' => 'Cliente',
            '1' => 'Zona',
            '2' => 'Nombre',
            '3' => 'Latitud',
            '4' => 'Longitud',
            '5' => 'Descripcion',
            '6' => 'Tipo transporte',
            '7' => 'Tipo acceso',
        ];
    }

    public static function createRow(array $row) {
        $documento = trim(mb_strtolower($row[0]));
        $zona = trim(mb_strtolower($row[1]));
        $nombre = trim(mb_strtoupper($row[2]));
        $latitud = trim($row[3]);
        $longitud = trim($row[4]);
        $descripcion = trim($row[5]);
        $transporte = trim(mb_strtolower($row[6]));
        $acceso = trim(mb_strtolower($row[7]));

        $parametro_zonas = TblParametro::where(['llave' => 'id_dominio_zonas'])->first()->valor;
        $parametro_transporte = TblParametro::where(['llave' => 'id_dominio_transportes'])->first()->valor;
        $parametro_acceso = TblParametro::where(['llave' => 'id_dominio_accesos'])->first()->valor;

        $cliente = TblTercero::where(['documento' => $documento])->first();
        $zona = TblDominio::where(['nombre' => $zona, 'id_dominio_padre' => $parametro_zonas])->first();
        $transporte = TblDominio::where(['nombre' => $transporte, 'id_dominio_padre' => $parametro_transporte])->first();
        $acceso = TblDominio::where(['nombre' => $acceso, 'id_dominio_padre' => $parametro_acceso])->first();

        return new TblPuntosInteres([
            'id_cliente' => (isset($cliente->id_tercero) ? $cliente->id_tercero : null),
            'id_zona' => (isset($zona->id_dominio) ? $zona->id_dominio : null),
            'nombre' => $nombre,
            'latitud' => $latitud,
            'longitud' => $longitud,
            'descripcion' => $descripcion,
            'id_tipo_transporte' => (isset($transporte->id_dominio) ? $transporte->id_dominio : null),
            'id_tipo_accesso' => (isset($acceso->id_dominio) ? $acceso->id_dominio : null),
            'id_usuareg' => auth()->id()
        ]);
    }
}
