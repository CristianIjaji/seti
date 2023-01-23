<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveOrdenRequest;
use App\Models\TblDominio;
use App\Models\TblInventario;
use App\Models\TblOrdenCompra;
use App\Models\TblOrdenCompraDetalle;
use App\Models\TblTercero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class OrdenController extends Controller
{
    protected $filtros;

    public function __construct()
    {
        $this->middleware('auth');        
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

        $iva = intval(str_replace(['iva', ' ', '%'], ['', '', ''], mb_strtolower($orden->tblIva->nombre))) / 100;
        $valor_iva = $total * $iva;
        return $total + $valor_iva;
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
            $orden = TblOrdenCompra::create($request->validated());
            $this->authorize('create', $orden);

            $orden->cupo_actual = $this->getDetailPurshase($orden);
            $orden->save();

            return response()->json([
                'success' => 'Orden creada exitosamente!',
                'response' => [
                    'value' => $orden->id_orden_compra,
                    'option' => $orden->descripcion,
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
    public function show(TblOrdenCompra $purchase)
    {
        $this->authorize('update', $purchase);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
            'create' => Gate::allows('create', new TblOrdenCompra),
            'edit' => Gate::allows('update', new TblOrdenCompra),
            'view' => Gate::allows('view', new TblOrdenCompra),
            'request' => $this->filtros
        ]);
    }
}
