<?php

namespace App\Http\Controllers;

use App\Exports\ReportsExport;
use App\Http\Requests\SaveMovimientoRequest;
use App\Models\TblActividad;
use App\Models\TblDominio;
use App\Models\TblInventario;
use App\Models\TblKardex;
use App\Models\TblMovimiento;
use App\Models\TblMovimientoDetalle;
use App\Models\TblOrdenCompra;
use App\Models\TblOrdenCompraDetalle;
use App\Models\TblTercero;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Excel;

class MovimientoController extends Controller
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

                if(!in_array($key, ['nombre'])){
                    $querybuilder->where($key, (count($operador) > 1 ? $operador[0] : 'like'), (count($operador) > 1 ? $operador[1] : strtolower("%$value%")));
                } else if($key == 'nombre' && $value) {
                    // $querybuilder->wherehas('tbltipomovimiento', function($q) use($value) {
                    //     $q->where('tbl_dominios.nombre', 'like', strtolower("%$value%"));
                    // });
                }
            }
            $this->filtros[$key] = $value;
        }

        return $querybuilder;
    }

    private function getDetailMove($movimiento, $entrada = false, $salida = false) {
        TblMovimientoDetalle::where('id_movimiento', '=', $movimiento->id_movimiento)->wherenotin('id_inventario', request()->id_item)->delete();
        $total = 0;

        foreach (request()->id_dominio_tipo_item as $index => $valor) {
            $detalle = TblMovimientoDetalle::where(['id_movimiento' => $movimiento->id_movimiento, 'id_inventario' => request()->id_item[$index]])->first();
            if(!$detalle) {
                $detalle = new TblMovimientoDetalle;
            }

            $detalle->id_movimiento = $movimiento->id_movimiento;
            $detalle->id_inventario = request()->id_item[$index];
            $detalle->cantidad = request()->cantidad[$index];
            $detalle->valor_unitario = str_replace(',', '', request()->valor_unitario[$index]);
            $detalle->valor_total = $detalle->cantidad * $detalle->valor_unitario;
            $detalle->id_usuareg = auth()->id();

            $detalle->save();
            $total += $detalle->valor_total;

            if($entrada || $salida) {
                $producto = TblInventario::where('id_inventario', '=', request()->id_item[$index])->first();
                $cantidad = ($entrada
                    ? 1
                    : -1
                ) * request()->cantidad[$index];
                $valor_unitario = ($entrada && request()->id_dominio_tipo_movimiento == session('id_dominio_movimiento_entrada_orden')
                    ? ((str_replace(',', '', $producto->valor_unitario) * floatval($producto->cantidad)) + $detalle->valor_total)
                        / ($producto->cantidad + $detalle->cantidad)
                    : $detalle->valor_unitario
                );

                $producto->cantidad += $cantidad;
                $producto->valor_unitario = $valor_unitario;
                $producto->save();

                $concepto = "";

                switch (request()->id_dominio_tipo_movimiento) {
                    case session('id_dominio_movimiento_entrada_orden'):
                        $concepto = "Entrada orden compra";
                        break;
                    case session('id_dominio_movimiento_salida_actividad'):
                        $concepto = "Salida actividad";
                        break;
                    default:
                        # code...
                        break;
                }

                TblKardex::create([
                    'id_movimiento_detalle' => $detalle->id_movimiento_detalle,
                    'id_inventario' => $producto->id_inventario,
                    'concepto' => $concepto,
                    'documento' => $movimiento->id_movimiento,
                    'cantidad' => $detalle->cantidad,
                    'valor_unitario' => $detalle->valor_unitario,
                    'valor_total' => ($detalle->cantidad * $detalle->valor_unitario),
                    'saldo_cantidad' => $producto->cantidad,
                    'saldo_valor_unitario' => $valor_unitario,
                    'saldo_valor_total' => $producto->cantidad * $valor_unitario,
                    'id_usuareg' => auth()->id()
                ]);
            }
        }

        return $total;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view', new TblMovimiento);
        return $this->getView('movimientos.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', new TblMovimiento);

        return view('movimientos._form', [
            'movimiento' => new TblMovimiento,
            'tipo_movimientos' => TblDominio::with(['tbldominio'])
                ->where([
                    'estado' => 1,
                ])->whereIn('id_dominio_padre', [session('id_dominio_entrada'), session('id_dominio_salida')])
                ->whereNotIn('id_dominio', [
                    session('id_dominio_movimiento_entrada_inicial'),
                    session('id_dominio_movimiento_salida_ajuste'),
                    session('id_dominio_movimiento_entrada_ajuste')
                ])
                ->get(),
            'impuestos' => TblDominio::getListaDominios(session('id_dominio_impuestos')),
            "tipo_movimiento" => isset(request()->tipo_movimiento)
                ? TblDominio::where('id_dominio', '=', request()->tipo_movimiento)->first()
                : '',
            "tercero" => isset(request()->tercero)
                ? TblTercero::where('id_tercero', '=', request()->tercero)->first()
                : '',
            'actividad' => isset(request()->actividad)
                ? TblActividad::where('id_actividad', '=', request()->actividad)->first()
                : ''
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveMovimientoRequest $request)
    {
        try {
            $this->authorize('create', new TblMovimiento);

            DB::beginTransaction();

            $agotados = "";
            $carrito = [];
            $entrada = false;
            $salida = false;
            if(in_array($request->id_dominio_tipo_movimiento, [session('id_dominio_movimiento_salida_actividad')])) {
                // Se valida stock del producto
                foreach (request()->id_dominio_tipo_item as $index => $valor) {
                    $producto = TblInventario::find(request()->id_item[$index]);
                    if(floatval($producto->cantidad) - floatval(request()->cantidad[$index]) < 0) {
                        $agotados .= "
                            <tr>
                                <td class='text-justify'>$producto->descripcion</td>
                                <td class='text-end'>".floatval(request()->cantidad[$index])."</td>
                                <td class='text-end text-danger'>$producto->cantidad</th>
                                <td class='text-end'>".floatval(request()->cantidad[$index] - $producto->cantidad)."</td>
                            </tr>
                        ";

                        $carrito[session('id_dominio_tipo_orden_compra')][$producto->id_inventario] = [
                            'item' => $producto->id_inventario,
                            'descripcion' => $producto->descripcion,
                            'cantidad' => floatval(request()->cantidad[$index] - $producto->cantidad),
                            'valor_unitario' => $producto->valor_unitario,
                            'valor_total' => $producto->valor_total,
                        ];
                    }

                    $salida = true;
                }

                if($agotados != "") {
                    session_start();
    
                    $_SESSION['carrito'] = [
                        'id_tercero_almacen' => $request->id_tercero_entrega,
                        'carrito' => $carrito
                    ];
    
                    $table = "
                        <span class='text-start'>Los siguientes productos no tienen stock suficiente</span>:
                        <br><br>
                        <table class='table table-bordered fs-6'>
                            <tr>
                                <th>Descripción</th>
                                <th class='text-nowrap align-middle'>Solicitado</th>
                                <th class='text-nowrap align-middle text-danger'>Stock</th>
                                <th class='text-nowrap align-middle'>Faltantes</th>
                            </tr>
                            $agotados
                        </table>
                        <button
                            class='btn btn-danger modal-form'
                            data-title='Crear orden'
                            data-header-class='bg-primary bg-opacity-75 text-white'
                            data-size='modal-fullscreen'
                            data-action='".route('purchases.create')."'
                            data-reload-location='true'
                            onclick="."$('.swal2-confirm').click();"."
                        >Generar Orden compra</button>
                    ";
                    throw new Exception($table);
                }
            }

            if(in_array($request->id_dominio_tipo_movimiento, [session('id_dominio_movimiento_entrada_orden')])) {
                $saldoOrden = TblOrdenCompra::getCarritoOrden($request->documento);
                foreach (request()->id_dominio_tipo_item as $index => $id_tipo_movimiento) {
                    if(!array_key_exists(request()->id_item[$index], $saldoOrden[$id_tipo_movimiento])) {
                        continue;
                    }

                    $producto = $saldoOrden[$id_tipo_movimiento][request()->id_item[$index]];

                    if($producto['cantidad'] < request()->cantidad[$index] || request()->cantidad[$index] <= 0) {
                        $agotados .= "
                            <tr>
                                <td class='text-start'>$producto[descripcion]</td>
                                <td class='text-end'>$producto[solicitado]</td>
                                <td class='text-end'>$producto[recibido]</td>
                                <td class='text-end fw-bold'>$producto[cantidad]</th>
                                <td class='text-end text-danger fw-bold'>".request()->cantidad[$index]."</th>
                            </tr>
                        ";
                    }

                    unset($saldoOrden[$id_tipo_movimiento][request()->id_item[$index]]);
                }

                if($agotados !== '') {
                    $table = "
                        <span class='text-start'>Por favor revise la cantidad ingresada de los siguientes productos</span>:
                        <br><br>
                        <table class='table table-bordered fs-6'>
                            <tr>
                                <th rowspan='2' class='text-nowrap align-middle'>Descripción</th>
                                <th colspan='4'>Cantidades</th>
                            </tr>
                            <tr>
                                <th class='text-nowrap align-middle'>Ordenado</th>
                                <th class='text-nowrap align-middle'>Recibido</th>
                                <th class='text-nowrap align-middle'>Saldo</th>
                                <th class='text-nowrap align-middle'>Ingresado</th>
                            </tr>
                            $agotados
                        </table>
                    ";

                    throw new Exception($table);
                }

                $entrada = true;
                $orden = TblOrdenCompra::where('id_orden_compra', '=', $request->documento)->first();
                $orden->id_dominio_estado = session(count($saldoOrden[session('id_dominio_tipo_movimiento')]) == 0 ? 'id_dominio_orden_cerrada' : 'id_dominio_orden_parcial');
                $orden->save();
            }

            $movimiento = TblMovimiento::create($request->validated());
            $movimiento->total = $this->getDetailMove($movimiento, $entrada, $salida);

            $movimiento->id_dominio_estado = session('id_dominio_movimiento_completado');
            $movimiento->save();

            DB::commit();
            return response()->json([
                'success' => 'Movimiento creado exitosamente!',
                'response' => [
                    'value' => $movimiento->id_movimiento,
                    'option' => $movimiento->tbltipomovimiento->nombre
                ]
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Error creando movimiento: ".$th->getMessage());
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
    public function show(TblMovimiento $move)
    {
        $this->authorize('view', $move);

        return view('movimientos._form', [
            'edit' => false,
            'movimiento' => $move,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(TblMovimiento $move)
    {
        $this->authorize('update', $move);

        return view('movimientos._form', [
            'edit' => (in_array($move->id_dominio_estado, [session('id_dominio_movimiento_pendiente')]) ? true : false),
            'movimiento' => $move,
            'tipo_movimientos' => TblDominio::with(['tbldominio'])
                ->where([
                    'estado' => 1,
                ])->whereIn('id_dominio_padre', [session('id_dominio_entrada'), session('id_dominio_salida')])
                ->whereNotIn('id_dominio', [
                    session('id_dominio_movimiento_entrada_inicial'),
                    session('id_dominio_movimiento_salida_ajuste'),
                    session('id_dominio_movimiento_entrada_ajuste')
                ])
                ->get(),
            'impuestos' => TblDominio::getListaDominios(session('id_dominio_impuestos')),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
        return $this->getView('movimientos.grid');
    }

    public function getView($view) {
        $movimiento = new TblMovimiento;

        return view($view, [
            'model' => TblMovimiento::with(['tblmovimientodetalle', 'tbltipomovimiento', 'tbltercerorecibe', 'tblterceroentrega', 'tblestado'])
                ->where(function ($q) {
                    $this->dinamyFilters($q);
                })->orderBy('id_movimiento', 'desc')->paginate(10),
            'estados' => TblDominio::getListaDominios(session('id_dominio_estados_movimiento')),
            'entregan' => TblMovimiento::select(
                DB::raw("
                    t.id_tercero,
                    CASE WHEN t.razon_social != ''
                        THEN t.razon_social
                        ELSE CONCAT(t.nombres, ' ', t.apellidos)
                    END as nombre
                ")
            )->join('tbl_terceros as t', 'tbl_movimientos.id_tercero_entrega', '=', 't.id_tercero')
            ->pluck('nombre', 'id_tercero'),
            'reciben' => TblMovimiento::select(
                DB::raw("
                    t.id_tercero,
                    CASE WHEN t.razon_social != ''
                        THEN t.razon_social
                        ELSE CONCAT(t.nombres, ' ', t.apellidos)
                    END as nombre
                ")
            )->join('tbl_terceros as t', 'tbl_movimientos.id_tercero_recibe', '=', 't.id_tercero')
            ->pluck('nombre', 'id_tercero'),
            'tipo_movimientos' => TblDominio::select(
                DB::raw("
                    tbl_dominios.id_dominio,
                    CONCAT(padre.nombre, ' ', tbl_dominios.nombre) as nombre
                ")
            )->join('tbl_dominios as padre', 'tbl_dominios.id_dominio_padre', '=', 'padre.id_dominio')
            ->where('padre.id_dominio_padre', '=', session('id_dominio_tipo_movimiento'))
            ->pluck('nombre', 'id_dominio'),
            'export' => Gate::allows('export', $movimiento),
            'import' => Gate::allows('import', $movimiento),
            'create' => Gate::allows('create', $movimiento),
            'edit' => Gate::allows('update', $movimiento),
            'view' => Gate::allows('view', $movimiento),
            'request' => $this->filtros
        ]);
    }

    private function generateDownload($option) {
        return TblMovimiento::select(
            DB::raw("
                tbl_movimientos.id_movimiento,
                tbl_movimientos.created_at as fecha,
                CASE WHEN entrega.razon_social != ''
                        THEN entrega.razon_social
                        ELSE CONCAT(entrega.nombres, ' ', entrega.apellidos)
                END as nombre_entrega,
                CASE WHEN recibe.razon_social != ''
                        THEN recibe.razon_social
                        ELSE CONCAT(recibe.nombres, ' ', recibe.apellidos)
                END as nombre_recibe,
                CONCAT(p.nombre, ' ', d.nombre) as tipo_movimiento,
                tbl_movimientos.documento,
                tbl_movimientos.total as total_movimiento,
                tbl_movimientos.saldo as saldo_movimiento,
                tbl_movimientos.observaciones,
                estado.nombre
            ")
        )->join('tbl_terceros as entrega', 'tbl_movimientos.id_tercero_entrega', 'entrega.id_tercero')
        ->join('tbl_terceros as recibe', 'tbl_movimientos.id_tercero_recibe', 'recibe.id_tercero')
        ->join('tbl_dominios as d', 'tbl_movimientos.id_dominio_tipo_movimiento', '=', 'd.id_dominio')
        ->join('tbl_dominios as p', 'd.id_dominio_padre', '=', 'p.id_dominio')
        ->join('tbl_dominios as estado', 'tbl_movimientos.id_dominio_estado', '=', 'estado.id_dominio')
        ->where(function($q) use($option) {
            if($option == 1) {
                $this->dinamyFilters($q, [
                    'tbl_movimientos.created_at' => 'created_at',
                    'tbl_movimientos.documento' => 'documento'
                ]);
            } else {
                $q->where('tbl_movimientos.id_movimiento', '=', '-1');
            }
        })->get();
    }

    public function export() {
        $headers = ['#', 'Fecha', 'Entrega', 'Recibe', 'Tipo movimiento', 'Documento',
            'Total', 'Saldo', 'Observaciones', 'Estado'
        ];

        return $this->excel->download(new ReportsExport($headers, $this->generateDownload(1)), 'Reporte movimientos inventario.xlsx');
    }

    public function getViewDetalle($edit, $id_tipo_movimiento, $id) {
        switch ($id_tipo_movimiento) {
            case session('id_dominio_movimiento_entrada_orden'):
                $carrito = TblOrdenCompra::getCarritoOrden($id);
                break;
            
            default:
                # code...
                break;
        }

        return view('partials._detalle', [
            'edit' => $edit,
            'editable' => $edit,
            'tipo_carrito' => 'movimiento',
            'detalleCarrito' => $carrito
            ]
        );
    }
}
