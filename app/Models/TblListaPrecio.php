<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblListaPrecio extends Model
{
    use HasFactory;

    protected $table = 'tbl_lista_precios';
    protected $primaryKey = 'id_lista_precio';
    protected $guarded = [];

    protected $fillable = [
        'id_cliente',
        'id_tipo_item',
        'codigo',
        'descripcion',
        'unidad',
        'cantidad',
        'valor_unitario',
        'estado',
        'id_usuareg',
    ];

    public function tbltercerocliente() {
        return $this->belongsTo(TblTercero::class, 'id_cliente');
    }
   
    public function tbldominioitem() {
        return $this->belongsTo(TblDominio::class, 'id_tipo_item');
    }
   
    public function tblusuario() {
        return $this->belongsTo(TblUsuario::class, 'id_usuareg');
    }

    public function getValorUnitarioAttribute() {
        return (isset($this->attributes['valor_unitario'])) ? number_format($this->attributes['valor_unitario'], 2) : 0;
    }

    public function getValorUnitarioFormAttribute() {
        return (isset($this->attributes['valor_unitario'])) ? $this->attributes['valor_unitario'] : 0;
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
            '2' => 'required|max:20|unique:tbl_lista_precios,codigo',
            '3' => 'required',
            '4' => 'required|max:50',
            '5' => 'required|numeric|min:0',
            '6' => 'required|numeric|min:0',
        ];
    }

    public static function getProperties() {
        return [
            '0' => 'Cliente',
            '1' => 'Tipo Ítem',
            '2' => 'Código',
            '3' => 'Descripción',
            '4' => 'Unidad',
            '5' => 'Cantidad',
            '6' => 'Valor unitario',
        ];
    }

    public static function createRow(array $row) {
        $documento = trim(mb_strtolower($row[0]));
        $tipo_item = trim(mb_strtolower($row[1]));
        $codigo = trim(mb_strtoupper($row[2]));
        $descripcion = trim($row[3]);
        $unidad = trim($row[4]);
        $cantidad = trim($row[5]);
        $valor_unitario = str_replace(',', '', trim($row[6]));

        $parametro_items = TblParametro::where(['llave' => 'id_dominio_tipo_items'])->first()->valor;

        $cliente = TblTercero::where(['documento' => $documento])->first();
        $item = TblDominio::where(['nombre' => $tipo_item, 'id_dominio_padre' => $parametro_items])->first();

        return new TblListaPrecio([
            'id_cliente' => $cliente->id_tercero,
            'id_tipo_item' => $item->id_dominio,
            'codigo' => $codigo,
            'descripcion' => $descripcion,
            'unidad' => $unidad,
            'cantidad' => $cantidad,
            'valor_unitario' => $valor_unitario,
            'id_usuareg' => auth()->id()
        ]);
    }
}
