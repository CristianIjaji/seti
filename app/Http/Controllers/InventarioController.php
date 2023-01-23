<?php

namespace App\Http\Controllers;

use App\Exports\ReportsExport;
use App\Http\Requests\SaveInventarioRequest;
use App\Imports\DataImport;
use App\Models\TblDominio;
use App\Models\TblInventario;
use App\Models\TblKardex;
use App\Models\TblListaPrecio;
use App\Models\TblMovimiento;
use App\Models\TblMovimientoDetalle;
use App\Models\TblTercero;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class InventarioController extends Controller
{
    protected $filtros;
    protected $excel;

    public function __construct(Excel $excel)
    {
        $this->middleware('auth');
        $this->excel = $excel;
    }

    private function dinamyFilters($querybuilder, $fields = []) {
        $operadores = ['>=', '<=', '!=', '=', '>', '<'];

        foreach (request()->all() as $key => $value) {
            if($value !== null && !in_array($key, ['_token', 'table', 'page'])) {
                $operador = [];

                foreach ($operadores as $item) {
                    $operador = explode($item, trim($value));

                    if(count($operador) > 1){
                        $operador[0] = $item;
                        break;
                    }
                }

                $key = (array_search($key, $fields) ? array_search($key, $fields) : $key);

                if(!in_array($key, ['full_name'])){
                    $querybuilder->where($key, (count($operador) > 1 ? $operador[0] : 'like'), (count($operador) > 1 ? $operador[1] : strtolower("%$value%")));
                } else if($key == 'full_name' && $value) {
                    $querybuilder->whereHas('tblterceroalmacen', function($q) use($value){
                        $q->where('tbl_terceros.razon_social', 'like', strtolower("%$value%"));
                        $q->orwhere('tbl_terceros.nombres', 'like', strtolower("%$value%"));
                        $q->orwhere('tbl_terceros.apellidos', 'like', strtolower("%$value%"));
                    });
                }
            }
            $this->filtros[$key] = $value;
        }

        return $querybuilder;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view', new TblInventario);
        return $this->getView('inventario.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', new TblInventario);
        
        $unidades1 = TblListaPrecio::pluck('unidad', 'unidad');
        $unidades2 = TblInventario::pluck('unidad', 'unidad')->union($unidades1);

        return view('inventario._form', [
            'inventario' => new TblInventario,
            'almacenes' => TblTercero::where([
                'estado' => 1,
                'id_dominio_tipo_tercero' => session('id_dominio_almacen'),
                'id_tercero_responsable' => auth()->user()->id_tercero
            ])->get(),
            'clasificaciones' => TblDominio::getListaDominios(session('id_dominio_clasificacion_inventario')),
            'unidades' => $unidades2,
            'ubicaciones' => TblInventario::pluck('ubicacion', 'ubicacion'),
            'marcas' => TblInventario::pluck('marca', 'marca'),
            'impuestos' => TblDominio::getListaDominios(session('id_dominio_impuestos')),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveInventarioRequest $request)
    {
        try {
            $inventario = TblInventario::create($request->validated());
            $this->authorize('create', $inventario);

            $movimiento = TblMovimiento::create([
                'id_dominio_tipo_movimiento' => session('id_dominio_movimiento_entrada_inicial'),
                'id_tercero_recibe' => $inventario->id_tercero_almacen,
                'id_tercero_entrega' => auth()->user()->id_tercero,
                'documento' => '',
                'observaciones' => 'Inventario inicial',
                'id_dominio_iva' => $request->id_dominio_iva,
                'total' => 0,
                'saldo' => 0,
                'id_dominio_estado' => session('id_dominio_movimiento_pendiente'),
                'id_usuareg' => auth()->id()
            ]);

            $detalle = TblMovimientoDetalle::create([
                'id_movimiento' => $movimiento->id_movimiento,
                'id_inventario' => $inventario->id_inventario,
                'cantidad' => $inventario->cantidad,
                'valor_unitario' => $inventario->valor_unitario_form,
                'valor_total' => ($inventario->cantidad * $inventario->valor_unitario_form),
                'id_usuareg' => auth()->id()
            ]);

            TblKardex::create([
                'id_movimiento_detalle' => $detalle->id_movimiento_detalle,
                'id_inventario' => $inventario->id_inventario,
                'concepto' => 'Inventario inicial',
                'documento' => $movimiento->id_movimiento,
                'cantidad' => $inventario->cantidad,
                'valor_unitario' => $inventario->valor_unitario_form,
                'valor_total' => ($inventario->cantidad * $inventario->valor_unitario_form),
                'saldo_cantidad' => $inventario->cantidad,
                'saldo_valor_unitario' => $inventario->valor_unitario_form,
                'saldo_valor_total' => ($inventario->cantidad * $inventario->valor_unitario_form),
                'id_usuareg' => auth()->id()
            ]);

            $movimiento->id_dominio_estado = session('id_dominio_movimiento_completado');
            $movimiento->save();

            return response()->json([
                'success' => 'Producto creado exitosamente!',
                'reponse' => [
                    'value' => $inventario->id_inventario,
                    'option' => $inventario->descripcion
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => $th->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(TblInventario $store)
    {
        $this->authorize('view', $store);

        return view('inventario._form', [
            'edit' => false,
            'inventario' => $store,
            'kardex' => TblKardex::with(['tblinventario', 'tblmovimientodetalle', 'tblusuario'])
                ->where(['id_inventario' => $store->id_inventario])
                ->orderBy('created_at', 'asc')
                ->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(TblInventario $store)
    {
        $this->authorize('update', $store);

        $unidades1 = TblListaPrecio::pluck('unidad', 'unidad');
        $unidades2 = TblInventario::pluck('unidad', 'unidad')->union($unidades1);

        return view('inventario._form', [
            'edit' => true,
            'inventario' => $store,
            'almacenes' => TblTercero::where([
                'estado' => 1,
                'id_dominio_tipo_tercero' => session('id_dominio_almacen'),
                'id_tercero_responsable' => auth()->id()
            ])->get(),
            'clasificaciones' => TblDominio::getListaDominios(session('id_dominio_clasificacion_inventario')),
            'unidades' => $unidades2,
            'ubicaciones' => TblInventario::pluck('ubicacion', 'ubicacion'),
            'marcas' => TblInventario::pluck('marca', 'marca'),
            'kardex' => TblKardex::with(['tblinventario', 'tblmovimientodetalle', 'tblusuario'])
                ->where(['id_inventario' => $store->id_inventario])
                ->orderBy('created_at', 'asc')
                ->get(),
            'impuestos' => TblDominio::getListaDominios(session('id_dominio_impuestos')),
            'estados' => [
                0 => 'Inactivo',
                1 => 'Activo'
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TblInventario $store, SaveInventarioRequest $request)
    {
        try {
            $this->authorize('update', $store);
            $movimiento = null;

            if($store->cantidad != $request->cantidad) {
                // Se crea el movimiento de inventario
                $id_dominio_tipo_movimiento = ($request->cantidad > $store->cantidad
                    ? session('id_dominio_movimiento_entrada_ajuste')
                    : session('id_dominio_movimiento_salida_ajuste')
                );

                $id_tercero_recibe = ($request->cantidad > $store->cantidad
                    ? $store->id_tercero_almacen
                    : auth()->user()->id_tercero
                );
                $id_tercero_entrega = ($request->cantidad > $store->cantidad
                    ? auth()->user()->id_tercero
                    : $store->id_tercero_almacen
                );

                $cantidad = ($request->cantidad > $store->cantidad
                    ? $request->cantidad
                    : abs($store->cantidad - $request->cantidad)
                );
                
                $movimiento = TblMovimiento::create([
                    'id_dominio_tipo_movimiento' => $id_dominio_tipo_movimiento,
                    'id_tercero_recibe' => $id_tercero_recibe,
                    'id_tercero_entrega' => $id_tercero_entrega,
                    'documento' => '',
                    'observaciones' => 'Ajuste inventario',
                    'id_dominio_iva' => TblDominio::where(['estado' => 1, 'nombre' => 'IVA 19%'])->first()->id_dominio,
                    'total' => (($cantidad * $request->valor_unitario)),
                    'saldo' => 0,
                    'id_dominio_estado' => session('id_dominio_movimiento_pendiente'),
                    'id_usuareg' => auth()->id()
                ]);

                $detalle = TblMovimientoDetalle::create([
                    'id_movimiento' => $movimiento->id_movimiento,
                    'id_inventario' => $store->id_inventario,
                    'cantidad' => $cantidad,
                    'valor_unitario' => $request->valor_unitario,
                    'valor_total' => ($cantidad * $request->valor_unitario),
                    'id_usuareg' => auth()->id()
                ]);

                TblKardex::create([
                    'id_movimiento_detalle' => $detalle->id_movimiento_detalle,
                    'id_inventario' => $store->id_inventario,
                    'concepto' => 'Ajuste inventario',
                    'documento' => $movimiento->id_movimiento,
                    'cantidad' => $cantidad,
                    'valor_unitario' => $request->valor_unitario,
                    'valor_total' => ($cantidad * $request->valor_unitario),
                    'saldo_cantidad' => $cantidad,
                    'saldo_valor_unitario' => $detalle->valor_unitario,
                    'saldo_valor_total' => ($cantidad * $request->valor_unitario),
                    'id_usuareg' => auth()->id()
                ]);
            }

            $store->update($request->validated());
            if($movimiento) {
                $movimiento->id_dominio_estado = session('id_dominio_movimiento_completado');
                $movimiento->save();
            }

            return response()->json([
                'success' => 'Producto actualizado correctamente!'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => $th->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function grid() {
        return $this->getView('inventario.grid');
    }

    private function getView($view) {
        $inventario = new TblInventario;

        return view($view, [
            'model' => TblInventario::with(['tblterceroalmacen', 'tblusuario'])
                ->where(function ($q) {
                    $this->dinamyFilters($q);
                })->orderBy('id_inventario', 'desc')->paginate(10),
            'almacenes' => TblTercero::getTercerosTipo(session('id_dominio_almacen')),
            'clasificaciones' => TblDominio::getListaDominios(session('id_dominio_clasificacion_inventario')),
            'export' => Gate::allows('export', $inventario),
            'import' => Gate::allows('import', $inventario),
            'create' => Gate::allows('create', $inventario),
            'edit' => Gate::allows('update', $inventario),
            'view' => Gate::allows('view', $inventario),
            'request' => $this->filtros,
        ]);
    }

    public function search($id_almacen, $tipo_carrito) {
        if(empty($id_almacen)) {
            return response()->json(['errors' => 'Error consultando lista de productos.']);
        }

        return view('partials._search', [
            'type' => session('id_dominio_tipo_orden_compra'),
            'tipo_carrito' => $tipo_carrito,
            'productos' => TblInventario::where(['estado' => 1, 'id_tercero_almacen' => $id_almacen])->get(),
        ]);
    }

    private function generateDownload($option) {
        return TblInventario::select(
            DB::raw("
                tbl_inventario.id_inventario,
                CONCAT(t.nombres, ' ', t.apellidos) as almacen,
                clasificacion.nombre,
                tbl_inventario.descripcion,
                tbl_inventario.marca,
                tbl_inventario.cantidad,
                tbl_inventario.unidad,
                tbl_inventario.valor_unitario,
                tbl_inventario.ubicacion,
                tbl_inventario.cantidad_minima,
                tbl_inventario.cantidad_maxima,
                CASE WHEN tbl_inventario.estado = 1 THEN 'Activo' ELSE 'Inactivo' END estado_inventario
            ")
        )->join('tbl_terceros as t', 'tbl_inventario.id_tercero_almacen', '=', 't.id_tercero')
        ->join('tbl_dominios as clasificacion', 'tbl_inventario.id_dominio_clasificacion', '=', 'clasificacion.id_dominio')
        ->where(function ($q) use($option) {
            if($option == 1) {
                $this->dinamyFilters($q, [
                    'tbl_inventario.descripcion' => 'descripcion',
                    'tbl_inventario.estado' => 'estado'
                ]);
            } else {
                $q->where('tbl_inventario.estado', '=', '-1');
            }
        })
        ->get();
    }

    public function export() {
        $headers = ['#', 'Almacén', 'Clasificación', 'Descripción', 'Marca', 'Cantidad', 'Unidad', 'Valor unitario',
            'Ubicación', 'Cantidad mínima', 'Cantidad máxima', 'Estado'
        ];

        return $this->excel->download(new ReportsExport($headers, $this->generateDownload(1)), 'Reporte inventario.xlsx');
    }

    public function downloadTemplate() {
        $headers = ['Documento encargado', 'Clasificación', 'Descripción', 'Marca', 'Cantidad', 'Unidad', 'Valor unitario',
            'Ubicación', 'Cantidad mínima', 'Cantidad máxima'
        ];

        return $this->excel->download(new ReportsExport($headers, $this->generateDownload(2)), 'Template inventario.xlsx');
    }

    public function import() {
        (new DataImport(new TblInventario))->import(request()->file('input_file'));

        TblMovimiento::where([
            'id_dominio_tipo_movimiento' => session('id_dominio_movimiento_entrada_inicial'),
            'id_dominio_estado' => session('id_dominio_movimiento_pendiente'),
            // 'id_tercero_recibe' => $producto->id_tercero_almacen,
            'id_tercero_entrega' => auth()->user()->id_tercero,
        ])->update(['id_dominio_estado' => session('id_dominio_movimiento_completado')]);

        return back();
    }
}
