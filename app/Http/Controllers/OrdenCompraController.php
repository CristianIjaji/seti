<?php

namespace App\Http\Controllers;

use App\Exports\OrdenExport;
use App\Exports\ReportsExport;
use App\Http\Requests\SaveOrdenRequest;
use App\Models\TblDominio;
use App\Models\TblEstado;
use App\Models\TblOrdenCompra;
use App\Models\TblOrdenCompraDetalle;
use App\Models\TblTercero;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Excel;

class OrdenCompraController extends Controller
{
    protected $filtros;
    protected $excel;

    public function __construct(Excel $excel)
    {
        $this->middleware('auth');
        $this->excel = $excel;
    }

    private function dinamyFilters($querybuilder) {
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

                $querybuilder->where($key, (count($operador) > 1 ? $operador[0] : 'like'), (count($operador) > 1 ? $operador[1] : strtolower("%$value%")));
            }

            $this->filtros[$key] = $value;
        }

        return $querybuilder;
    }

    private function getDetailPurshase($orden) {
        TblOrdenCompraDetalle::where('id_orden_compra', '=', $orden->id_orden_compra)->wherenotin('id_inventario', request()->id_item)->delete();
        $total = 0;

        foreach (request()->id_dominio_tipo_item as $index => $valor) {
            $detalle = TblOrdenCompraDetalle::where(['id_orden_compra' => $orden->id_orden_compra, 'id_inventario' => request()->id_item[$index]])->first();
            if(!$detalle) {
                $detalle = new TblOrdenCompraDetalle;
            }

            $detalle->id_orden_compra = $orden->id_orden_compra;
            $detalle->id_inventario = request()->id_item[$index];
            $detalle->descripcion = request()->descripcion_item[$index];
            $detalle->cantidad = request()->cantidad[$index];
            $detalle->valor_unitario = str_replace(',', '', request()->valor_unitario[$index]);
            $detalle->valor_total = $detalle->cantidad * $detalle->valor_unitario;

            $detalle->save();
            $total += $detalle->valor_total;
        }

        return $total;// + $valor_iva;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view', new TblOrdenCompra);

        return $this->getView('ordenes_compra.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', new TblOrdenCompra);
        session_start();
        $session = [];

        if(isset($_SESSION['carrito'])) {
            $session = $_SESSION['carrito'];
            unset($_SESSION['carrito']);
        }

        return view('ordenes_compra._form', [
            'orden' => new TblOrdenCompra,
            'proveedores' => TblTercero::where([
                'estado' => 1,
                'id_dominio_tipo_tercero' => session('id_dominio_proveedor')
            ])->get(),
            'almacenes' => TblTercero::where([
                'estado' => 1,
                'id_dominio_tipo_tercero' => session('id_dominio_almacen')
            ])->get(),
            'impuestos' => TblDominio::getListaDominios(session('id_dominio_impuestos')),
            'medios_pago_ordenes_compra' => TblDominio::getListaDominios(session('id_dominio_medio_pago_orden_compra')),
            'tipos_ordenes_compra' => TblDominio::getListaDominios(session('id_dominio_tipo_orden_compra')),
            'tercero_almacen' => isset($session['id_tercero_almacen']) ? TblTercero::where('id_tercero', '=', $session['id_tercero_almacen'])->first() : null,
            'carrito' => isset($session['carrito']) ? $session['carrito'] : null
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveOrdenRequest $request)
    {
        try {
            $this->authorize('create', new TblOrdenCompra);

            DB::beginTransaction();
            $orden = TblOrdenCompra::create($request->validated());

            $orden->cupo_actual = $this->getDetailPurshase($orden);
            $orden->save();

            $orden->comentario = "Orden creada # $orden->id_orden_compra.";
            $this->createTrack($orden, session('id_dominio_orden_abierta'));

            DB::commit();
            return response()->json([
                'success' => 'Orden creada exitosamente!',
                'response' => [
                    'value' => $orden->id_orden_compra,
                    'option' => $orden->descripcion,
                ],
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Error creando orden de compra: ".$th->__toString());
            return response()->json([
                'errors' => 'Error creando orden de compra.'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(TblOrdenCompra $purchase)
    {
        $this->authorize('update', $purchase);

        return view('ordenes_compra._form', [
            'edit' => false,
            'orden' => $purchase
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(TblOrdenCompra $purchase)
    {
        $this->authorize('update', $purchase);

        return view('ordenes_compra._form', [
            'edit' => true,
            'orden' => $purchase,
            'proveedores' => TblTercero::where([
                'estado' => 1,
                'id_dominio_tipo_tercero' => session('id_dominio_proveedor')
            ])->get(),
            'almacenes' => TblTercero::where([
                'estado' => 1,
                'id_dominio_tipo_tercero' => session('id_dominio_almacen')
            ])->get(),
            'impuestos' => TblDominio::getListaDominios(session('id_dominio_impuestos')),
            'medios_pago_ordenes_compra' => TblDominio::getListaDominios(session('id_dominio_medio_pago_orden_compra')),
            'tipos_ordenes_compra' => TblDominio::getListaDominios(session('id_dominio_tipo_orden_compra')),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SaveOrdenRequest $request, TblOrdenCompra $purchase)
    {
        try {
            $this->authorize('update', $purchase);

            if(in_array($purchase->id_dominio_estado, [session('id_dominio_orden_parcial'), session('id_dominio_orden_cerrada')])) {
                throw new Exception("no se puede modificar la orden, porque ya se hizo una entrega a bodega");
            }

            DB::beginTransaction();

            $estado = $purchase->id_dominio_estado;
            $purchase->update($request->validated());

            $purchase->cupo_actual = $this->getDetailPurshase($purchase);
            $purchase->save();

            if($estado !== session('id_dominio_orden_abierta')) {
                $purchase->comentario = (
                    isset(request()->comentario) && trim(request()->comentario) != ''
                    ? request()->comentario
                    : 'Orden editada.'
                );

                $this->createTrack($purchase, session('id_dominio_orden_abierta'));
            }

            DB::commit();
            return response()->json([
                'success' => 'Orden actualizada correctamente!'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Error editando orden de compra: ".$th->__toString());
            return response()->json([
                'errors' => 'Error editando orden de compra.'
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

    public function handlePurchase(TblOrdenCompra $purchase) {
        try {
            $response = '';
            switch (request()->action) {
                case 'parcial':
                    $purchase->comentario = (
                        isset(request()->comentario) && trim(request()->comentario) != ''
                        ? request()->comentario
                        : 'Entrega parcial.'
                    );
                    $response = $this->updatePurchase(
                        $purchase,
                        [session('id_dominio_orden_abierta')],
                        session('id_dominio_orden_parcial'),
                        'Orden con entrega parcial!',
                        ''
                    );
                    break;
                case 'cerrada':
                    $purchase->comentario = (
                        isset(request()->comentario) && trim(request()->comentario) != ''
                        ? request()->comentario
                        : 'Entrega completada.'
                    );
                    $response = $this->updatePurchase(
                        $purchase,
                        [session('id_dominio_orden_parcial'), session('id_dominio_orden_abierta')],
                        session('id_dominio_orden_cerrada'),
                        'Orden cerrada!',
                        ''
                    );
                    break;
                case 'cancelada':
                    $purchase->comentario = (
                        isset(request()->comentario) && trim(request()->comentario) != ''
                        ? request()->comentario
                        : 'Orden cancelada.'
                    );
                    $response = $this->updatePurchase(
                        $purchase,
                        [session('id_dominio_orden_abierta')],
                        session('id_dominio_orden_cancelada'),
                        '',
                        ''
                    );
                    break;
                default:
                    # code...
                    break;
            }

            if(!isset($response['success'])) {
                throw new Exception($response['error']);
            }

            return response()->json([
                'success' => $response['success']
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ]);
        }
    }

    public function grid() {
        return $this->getView('ordenes_compra.grid');
    }

    private function getView($view) {
        return view($view, [
            'model' => TblOrdenCompra::where(function($q) {
                $this->dinamyFilters($q);
            })->orderBy('id_orden_compra', 'desc')->paginate(10),
            'almacenes' => TblTercero::getTercerosTipo(session('id_dominio_almacen')),
            'proveedores' => TblTercero::getTercerosTipo(session('id_dominio_proveedor')),
            'modosPago' => TblDominio::getListaDominios(session('id_dominio_medio_pago_orden_compra')),
            'estados' => TblDominio::getListaDominios(session('id_dominio_estados_orden')),
            'export' => Gate::allows('export', new TblOrdenCompra),
            'import' => Gate::allows('import', new TblOrdenCompra),
            'create' => Gate::allows('create', new TblOrdenCompra),
            'edit' => Gate::allows('update', new TblOrdenCompra),
            'view' => Gate::allows('view', new TblOrdenCompra),
            'request' => $this->filtros
        ]);
    }

    public function getDocumentos($id_tercero_proveedor, $id_tercero_almacen) {
        if(empty($id_tercero_proveedor) || empty($id_tercero_almacen)) {
            return response()->json(['errors' => 'Error obteniendo ordenes de compra.']);
        }

        return response()->json([
            'documentos' => TblOrdenCompra::where([
                'id_tercero_proveedor' => $id_tercero_proveedor,
                'id_tercero_almacen' => $id_tercero_almacen,
            ])->wherein('id_dominio_estado', [session('id_dominio_orden_abierta'), session('id_dominio_orden_parcial')])->get()
        ]);
    }

    public function search($id_tercero_proveedor) {
        if(empty($id_tercero_proveedor)) {
            return response()->json(['errors' => 'Error obteniendo ordenes de compra.']);
        }
    }

    private function generateDonwload($option) {

    }

    public function export() {
        $headers = [];

        return $this->excel->download(new ReportsExport($headers, $this->generateDonwload(1)), 'Reporte ordenes.xlsx');
    }

    private function updatePurchase($purchase, $estados, $nuevoEstado, $msg, $notificacoin, $usuarioFinal = '') {
        try {
            if(in_array($purchase->id_dominio_estado, $estados)) {
                $purchase->id_dominio_estado = $nuevoEstado;

                $this->createTrack($purchase, $nuevoEstado);
                unset($purchase->comentario);
                $purchase->save();
            }
        } catch (\Throwable $th) {
            Log::error($th->__toString());
            return ['error' => $th->getMessage()];
        }
    }

    private function createTrack($purchase, $action) {
        try {
            TblEstado::create([
                'id_tabla' => $purchase->id_orden_compra,
                'tabla' => $purchase->getTable(),
                'id_dominio_estado' => $action,
                'comentario' => $purchase->comentario,
                'id_usuareg' => auth()->id()
            ]);
        } catch (\Throwable $th) {
            Log::error("Error creando track orden compra: ".$th->__toString());
        }
    }

    public function exportPurchase() {
        $orden = TblOrdenCompra::with(['tblproveedor', 'tblasesor'])->where('id_orden_compra', '=', request()->purchase)->first();
        return $this->excel->download(new OrdenExport($orden), 'OC '.$orden->tblproveedor->full_name.' '.str_pad($orden->id_orden_compra, 4, '0', STR_PAD_LEFT).'.xlsx');
    }

    public function seguimiento(TblOrdenCompra $purchase) {
        return view('partials._seguimiento', [
            'model' => $purchase,
            'route' => 'purchases.handlePurchase'
        ]);
    }
}
