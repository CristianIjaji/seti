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
use App\Models\TblTercero;
use Exception;
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
        foreach (request()->all() as $key => $value) {
            if($value !== null && !in_array($key, ['_token', 'table', 'page'])) {
                $query = getValoresConsulta($value);

                $key = (array_search($key, $fields) ? array_search($key, $fields) : $key);
                $querybuilder->where($key, $query['operator'], $query['value']);
            }

            $this->filtros[$key] = $value;
        }

        return $querybuilder;
    }

    private function getDetailMove($movimiento, $entrada = false, $salida = false) {
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

            if(request()->cantidad[$index] > 0 && ($entrada || $salida)) {
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

                if($entrada && request()->id_dominio_tipo_movimiento == session('id_dominio_movimiento_salida_traslado')) {
                    $producto->id_tercero_almacen = request()->id_tercero_recibe;
                }

                $producto->cantidad += $cantidad;
                $producto->valor_unitario = $valor_unitario;
                $producto->save();

                $concepto = "";

                switch (request()->id_dominio_tipo_movimiento) {
                    case session('id_dominio_movimiento_entrada_devolucion'):
                        $concepto = "Entrada devoluci??n inventario actividad";
                        break;
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
                    session('id_dominio_movimiento_entrada_ajuste'),
                    session('id_dominio_movimiento_salida_actividad'),
                    session('id_dominio_movimiento_entrada_traslado')
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

            $entrada = ['success' => false];
            $salida = ['success' => false];

            switch ($request->id_dominio_tipo_movimiento) {
                case session('id_dominio_movimiento_entrada_devolucion'):
                    $entrada = $this->entradaDevolucionActividad();
                    if(!$entrada['success']) {
                        throw new Exception($entrada['error'], $entrada['code']);
                    }
                    break;
                case session('id_dominio_movimiento_entrada_orden'):
                    $entrada = $this->entradaOrdenCompra();
                    if(!$entrada['success']) {
                        throw new Exception($entrada['error'], $entrada['code']);
                    }
                    break;

                case session('id_dominio_movimiento_salida_actividad'):
                    $salida = $this->salidaActividad();
                    if(!$salida['success']) {
                        throw new Exception($salida['error'], $salida['code']);
                    }
                    break;
                case session('id_dominio_movimiento_salida_traslado'):
                    // Se crea el movimiento de entrada al nuevo almacen
                    $movimiento = TblMovimiento::create([
                        'id_dominio_tipo_movimiento' => session('id_dominio_movimiento_entrada_traslado'),
                        'id_tercero_recibe' => request()->id_tercero_entrega,
                        'id_tercero_entrega' => request()->id_tercero_recibe,
                        'documento' => request()->documento,
                        'observaciones' => 'Entrada traslado',
                        'id_dominio_iva' => request()->id_dominio_iva,
                        'total' => 0,
                        'saldo' => 0,
                        'id_dominio_estado' => session('id_dominio_movimiento_pendiente'),
                        'id_usuareg' => auth()->id()
                    ]);

                    $movimiento->total = $this->getDetailMove($movimiento, true, false);
                    $movimiento->id_dominio_estado = session('id_dominio_movimiento_completado');
                    $movimiento->save();

                    $salida = $this->salidaTraslado();
                    if(!$salida['success']) {
                        throw new Exception($salida['error'], $salida['code']);
                    }
                    break;
                default:
                    # code...
                    break;
            }

            $movimiento = TblMovimiento::create($request->validated());
            $movimiento->total = $this->getDetailMove($movimiento, $entrada['success'], $salida['success']);

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
            Log::error("Error creando movimiento: ".$th->__toString());
            return response()->json([
                'errors' => ($th->getCode() != -911 ? "Error creando movimiento." : $th->getMessage())
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

        if(in_array($move->id_dominio_tipo_movimiento, [session('id_dominio_movimiento_salida_actividad')])) {
            $actividad = TblActividad::where(['id_actividad' => $move->documento])
                ->whereNotin('id_dominio_estado', [
                    session('id_dominio_actividad_ejecutado'),
                    session('id_dominio_actividad_informe_cargado'),
                    session('id_dominio_actividad_liquidado'),
                    session('id_dominio_actividad_conciliado'),
                ])
                ->first();
        }

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
            'editar_movimiento' => ($actividad ? true : false)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SaveMovimientoRequest $request, TblMovimiento $move)
    {
        try {
            $this->authorize('update', $move);

            DB::beginTransaction();
            $entrada = ['success' => false];
            $salida = ['success' => false];

            switch ($request->id_dominio_tipo_movimiento) {
                case session('id_dominio_movimiento_entrada_devolucion'):
                    $entrada = $this->entradaDevolucionActividad();
                    if(!$entrada['success']) {
                        throw new Exception($entrada['error'], $entrada['code']);
                    }
                    break;
                case session('id_dominio_movimiento_entrada_orden'):
                    $entrada = $this->entradaOrdenCompra();
                    if(!$entrada['success']) {
                        throw new Exception($entrada['error'], $entrada['code']);
                    }
                    break;

                case session('id_dominio_movimiento_salida_actividad'):
                    $salida = $this->salidaActividad();
                    if(!$salida['success']) {
                        throw new Exception($salida['error'], $salida['code']);
                    }
                    break;
                case session('id_dominio_movimiento_salida_traslado'):
                    // Se crea el movimiento de entrada al nuevo almacen
                    $movimiento = TblMovimiento::create([
                        'id_dominio_tipo_movimiento' => session('id_dominio_movimiento_entrada_traslado'),
                        'id_tercero_recibe' => request()->id_tercero_entrega,
                        'id_tercero_entrega' => request()->id_tercero_recibe,
                        'documento' => request()->documento,
                        'observaciones' => 'Entrada traslado',
                        'id_dominio_iva' => request()->id_dominio_iva,
                        'total' => 0,
                        'saldo' => 0,
                        'id_dominio_estado' => session('id_dominio_movimiento_pendiente'),
                        'id_usuareg' => auth()->id()
                    ]);

                    $movimiento->total = $this->getDetailMove($movimiento, true, false);
                    $movimiento->id_dominio_estado = session('id_dominio_movimiento_completado');
                    $movimiento->save();

                    $salida = $this->salidaTraslado();
                    if(!$salida['success']) {
                        throw new Exception($salida['error'], $salida['code']);
                    }
                    break;
                default:
                    # code...
                    break;
            }

            $move->fill($request->validated());
            $move->total = $this->getDetailMove($move, $entrada['success'], $salida['success']);

            $move->id_dominio_estado = session('id_dominio_movimiento_completado');
            $move->save();

            DB::commit();
            return response()->json([
                'success' => 'Movimiento actualizado exitosamente!',
                'response' => [
                    'value' => $move->id_movimiento,
                    'option' => $move->tbltipomovimiento->nombre
                ]
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Error editando movimiento: ".$th->__toString());
            return response()->json([
                'errors' => ($th->getCode() != -911 ? "Error editando movimiento." : $th->getMessage())
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
            case session('id_dominio_movimiento_entrada_devolucion'):
                $carrito = TblActividad::getCarritoActividad($id);
                break;
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

    private function entradaDevolucionActividad() {
        try {
            $agotados = '';

            $saldoActividad = TblActividad::getCarritoActividad(request()->documento);
            foreach (request()->id_dominio_tipo_item as $index => $id_tipo_movimiento) {
                if(!array_key_exists(request()->id_item[$index], $saldoActividad[$id_tipo_movimiento])) {
                    continue;
                }

                $producto = $saldoActividad[$id_tipo_movimiento][request()->id_item[$index]];
                if($producto['cantidad'] < request()->cantidad[$index]) {
                    $agotados .= "
                        <tr>
                            <td class='text-start'>$producto[descripcion]</td>
                            <td class='text-end'>$producto[cantidad]</td>
                            <td class='text-end text-danger fw-bold'>".request()->cantidad[$index]."</th>
                        </tr>
                    ";
                }

                if($agotados !== '') {
                    $table = "
                        <span class='text-start'>Por favor revise la cantidad ingresada de los siguientes productos</span>:
                        <br><br>
                        <table class='table table-bordered fs-6'>
                            <tr>
                                <th rowspan='2' class='text-nowrap align-middle'>Descripci??n</th>
                                <th colspan='2'>Cantidades</th>
                            </tr>
                            <tr>
                                <th class='text-nowrap align-middle'>Cargado</th>
                                <th class='text-nowrap align-middle'>Ingresado</th>
                            </tr>
                            $agotados
                        </table>
                    ";
    
                    throw new Exception($table);
                }
            }

            return [
                'success' => true,
                'entrada' => true
            ];
        } catch (\Throwable $th) {
            return [
                'success' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    private function entradaOrdenCompra() {
        try {
            $agotados = '';

            $saldoOrden = TblOrdenCompra::getCarritoOrden(request()->documento);
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
                            <th rowspan='2' class='text-nowrap align-middle'>Descripci??n</th>
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

                throw new Exception($table, -911);
            }

            $orden = TblOrdenCompra::where('id_orden_compra', '=', request()->documento)->first();
            $controller = new OrdenCompraController($this->excel);
            request()->merge([
                'action' => (count($saldoOrden[session('id_dominio_tipo_movimiento')]) == 0 ? 'cerrada' : 'parcial')
            ]);
            $controller->handlePurchase($orden);

            return [
                'success' => true,
                'entrada' => true
            ];
        } catch (\Throwable $th) {
            return [
                'success' => false,
                'error' => $th->getMessage(),
                'code' => $th->getCode()
            ];
        }
    }

    private function salidaActividad() {
        try {
            $agotados = '';

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
            }

            if($agotados != "") {
                session_start();

                $_SESSION['carrito'] = [
                    'id_tercero_almacen' => request()->id_tercero_entrega,
                    'carrito' => $carrito
                ];

                $table = "
                    <span class='text-start'>Los siguientes productos no tienen stock suficiente</span>:
                    <br><br>
                    <table class='table table-bordered fs-6'>
                        <tr>
                            <th>Descripci??n</th>
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

                throw new Exception($table, -911);
            }

            return [
                'success' => true,
                'salida' => true
            ];
        } catch (\Throwable $th) {
            Log::error("Error cargando inventario a actividad: ".$th->__toString());
            return [
                'success' => false,
                'error' => $th->getMessage(),
                'code' => $th->getCode()
            ];
        }
    }

    private function salidaTraslado() {
        try {
            $agotados = '';

            if(request()->id_tercero_recibe == request()->id_tercero_entrega) {
                throw new Exception("No puede realizar un traslado de inventario al mismo almac??n");
            }

            // se recorren los elementos a trasladar
            foreach (request()->id_dominio_tipo_item as $index => $valor) {
                $producto = TblInventario::find(request()->id_item[$index]);
                if(floatval($producto->cantidad) <= 0) {
                    $agotados .= "
                        <tr>
                            <td class='text-justify'>$producto->descripcion</td>
                            <td class='text-end text-danger'>$producto->cantidad</th>
                        </tr>
                    ";
                }

                $producto->id_tercero_almacen = request()->id_tercero_recibe;
            }

            if($agotados != "") {
                $table = "
                    <span class='text-start'>Los siguientes productos no tienen stock</span>:
                    <br><br>
                    <table class='table table-bordered fs-6'>
                        <tr>
                            <th>Descripci??n</th>
                            <th class='text-nowrap align-middle text-danger'>Stock</th>
                        </tr>
                        $agotados
                    </table>
                ";

                throw new Exception($table, -911);
            }

            return [
                'success' => true,
                'salida' => true
            ];
        } catch (\Throwable $th) {
            Log::error("Error realizando traslado: ".$th->__toString());
            return [
                'success' => false,
                'error' => $th->getMessage(),
                'code' => $th->getCode()
            ];
        }
    }
}
