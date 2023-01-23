<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class TblInventario extends Model
{
    use HasFactory;

    protected $table = 'tbl_inventario';
    protected $primaryKey = 'id_inventario';
    protected $guarded = [];

    protected $fillable = [
        'id_tercero_almacen',
        'id_dominio_clasificacion',
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

    public function tblclasificacion() {
        return $this->belongsTo(TblDominio::class, 'id_dominio_clasificacion');
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

    public function getValorUnitarioAttribute() {
        return (isset($this->attributes['valor_unitario'])) ? number_format($this->attributes['valor_unitario'], 2) : 0;
    }

    public function getValorUnitarioFormAttribute() {
        return (isset($this->attributes['valor_unitario'])) ? $this->attributes['valor_unitario'] : 0;
    }

    public function getProductoAttribute() {
        return $this->tblclasificacion->nombre.' '.$this->attributes['descripcion'].' '.$this->attributes['marca'];
    }

    public static function getRules() {
        return [
            '0' => 'required|exists:tbl_terceros,documento',
            '1' => 'required|exists:tbl_dominios,nombre',
            '2' => 'required|string|max:255',
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
        $dominio_clasificacion = TblDominio::where(['nombre' => $clasificacion])->first();

        $existe = TblInventario::where([
            'id_dominio_clasificacion' => (isset($dominio_clasificacion->id_dominio) ? $dominio_clasificacion->id_dominio : null),
            'descripcion' => $descripcion,
            'marca' => $marca
        ])->first();

        if(!$existe) {
            // Se intenta crear producto
            $producto = TblInventario::create([
                'id_tercero_almacen' => (isset($almacen->id_tercero) ? $almacen->id_tercero : null),
                'id_dominio_clasificacion' => (isset($dominio_clasificacion->id_dominio) ? $dominio_clasificacion->id_dominio : null),
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

            // Se busca el movimiento
            $movimiento = TblMovimiento::where([
                'id_dominio_tipo_movimiento' => session('id_dominio_movimiento_entrada_inicial'),
                'id_dominio_estado' => session('id_dominio_movimiento_pendiente'),
                'id_tercero_recibe' => $producto->id_tercero_almacen,
                'id_tercero_entrega' => auth()->user()->id_tercero,
            ])->first();

            if(!$movimiento) {
                $movimiento = TblMovimiento::create([
                    'id_dominio_tipo_movimiento' => session('id_dominio_movimiento_entrada_inicial'),
                    'id_tercero_recibe' => $producto->id_tercero_almacen,
                    'id_tercero_entrega' => auth()->user()->id_tercero,
                    'documento' => '',
                    'observaciones' => 'Inventario inicial',
                    'id_dominio_iva' => TblDominio::where(['estado' => 1, 'nombre' => 'IVA 19%'])->first()->id_dominio,
                    'total' => 0,
                    'saldo' => 0,
                    'id_dominio_estado' => session('id_dominio_movimiento_pendiente'),
                    'id_usuareg' => auth()->id()
                ]);
            }

            $detalle = TblMovimientoDetalle::create([
                'id_movimiento' => $movimiento->id_movimiento,
                'id_inventario' => $producto->id_inventario,
                'cantidad' => $cantidad,
                'valor_unitario' => $valorUnitario,
                'valor_total' => ($cantidad * $valorUnitario),
                'id_usuareg' => auth()->id()
            ]);

            TblKardex::create([
                'id_movimiento_detalle' => $detalle->id_movimiento_detalle,
                'id_inventario' => $producto->id_inventario,
                'concepto' => 'Inventario inicial',
                'documento' => $movimiento->id_movimiento,
                'cantidad' => $producto->cantidad,
                'valor_unitario' => $valorUnitario,
                'valor_total' => ($cantidad * $valorUnitario),
                'saldo_cantidad' => $producto->cantidad,
                'saldo_valor_unitario' => $valorUnitario,
                'saldo_valor_total' => ($cantidad * $valorUnitario),
                'id_usuareg' => auth()->id()
            ]);

            return $producto;
        }
    }
}
