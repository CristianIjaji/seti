<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblInventario extends Model
{
    use HasFactory;

    protected $table = 'tbl_inventario';
    protected $primaryKey = 'id_inventario';
    protected $guarded = [];

    protected $fillable = [
        'id_tercero_almacen',
        'clasificacion',
        'descripcion',
        'marca',
        'cantidad',
        'unidad',
        'valor_unitario',
        'ubicacion',
        'cantidad_minima',
        'cantidad_maxima',
        'estado',
        'id_usuareg'
    ];

    public function tblterceroalmacen() {
        return $this->belongsTo(TblTercero::class, 'id_tercero_almacen');
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
            '1' => 'required|string|max:255',
            '2' => 'required||string|max:255',
            '3' => 'nullable|string:max:255',
            '4' => 'required',
            '5' => 'required|string:max:255',
            '6' => 'required',
            '7' => 'nullable|string|max:255',
            '8' => 'required|min:0',
            '9' => 'required|min:0'
        ];
    }

    public static function getProperties() {
        return [
            '0' => 'Almacén',
            '1' => 'Clasificación',
            '2' => 'Descripción',
            '3' => 'Marca',
            '4' => 'Cantidad',
            '5' => 'Unidad',
            '6' => 'Valor Unitario',
            '7' => 'Ubicación',
            '8' => 'Cantidad Mínima',
            '9' => 'Cantidad Máxima'
        ];
    }

    public static function createRow(array $row) {
        $documento = trim(($row[0]));
        $clasificacion = trim($row[1]);
        $descripcion = trim($row[2]);
        $marca = trim($row[3]);
        $cantidad = trim($row[4]);
        $unidad = trim($row[5]);
        $valorUnitario = trim($row[6]);
        $ubicacion = trim($row[7]);
        $cantidadMinima = trim($row[8]);
        $cantidadMaxima = trim($row[9]);

        $almacen = TblTercero::where(['documento' => $documento])->first();

        $existe = TblInventario::where([
            'clasificacion' => $clasificacion,
            'descripcion' => $descripcion,
            'marca' => $marca
        ])->first();

        if(!$existe) {
            $producto = TblInventario::create([
                'id_tercero_almacen' => (isset($almacen->id_tercero) ? $almacen->id_tercero : null),
                'clasificacion' => $clasificacion,
                'descripcion' => $descripcion,
                'marca' => $marca,
                'cantidad' => $cantidad,
                'unidad' => $unidad,
                'valor_unitario' => $valorUnitario,
                'ubicacion' => $ubicacion,
                'cantidad_minima' => $cantidadMinima,
                'cantidad_maxima' => $cantidadMaxima,
                'id_usuareg' => auth()->id()
            ]);

            return new  TblKardex([
                'id_inventario' => $producto->id_inventario,
                'concepto' => 'Inventario inicial',
                'documento' => $producto->id_inventario,
                'cantidad' => $producto->cantidad,
                'valor_unitario' => $producto->valor_unitario,
                'valor_total' => $producto->valor_unitario * $producto->cantidad,
                'saldo_cantidad' => $producto->cantidad,
                'saldo_valor_unitario' => $producto->valor_unitario,
                'saldo_valor_total' => $producto->valor_unitario * $producto->cantidad,
                'id_usuareg' => auth()->id()
            ]);
        }
    }
}
