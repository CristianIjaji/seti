<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveCotizacionRequest;
use App\Models\TblCotizacion;
use App\Models\TblCotizacionDetalle;
use App\Models\TblDominio;
use App\Models\TblPuntosInteres;
use App\Models\TblTercero;
use Illuminate\Support\Facades\Log;

class CotizacionController extends Controller
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

                if(!in_array($key, ['full_name'])){
                    $querybuilder->where($key, (count($operador) > 1 ? $operador[0] : 'like'), (count($operador) > 1 ? $operador[1] : strtolower("%$value%")));
                } else if($key == 'full_name' && $value) {
                    // $querybuilder->where('nombres', 'like', strtolower("%$value%"));
                    // $querybuilder->orWhere('apellidos', 'like', strtolower("%$value%"));
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
        return $this->getView('cotizaciones.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('cotizaciones._form', [
            'cotizacion' => new TblCotizacion,
            'carrito' => [],
            'clientes' => TblTercero::getClientesTipo(session('id_dominio_cliente')),
            'tipos_trabajo' => TblDominio::getListaDominios(session('id_dominio_tipos_trabajo')),
            'prioridades' => TblDominio::getListaDominios(session('id_dominio_tipos_prioridad')),
            'impuestos' => TblDominio::getListaDominios(session('id_dominio_impuestos')),
            'contratistas' => TblTercero::getClientesTipo(session('id_dominio_contratista')),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveCotizacionRequest $request)
    {
        try {
            $cotizacion = TblCotizacion::create($request->validated());
            $total = 0;
            foreach (request()->id_tipo_item as $index => $valor) {
                $detalle = new TblCotizacionDetalle;
                $detalle->id_cotizacion = $cotizacion->id_cotizacion;
                $detalle->id_tipo_item = request()->id_tipo_item[$index];
                $detalle->id_lista_precio = request()->id_lista_precio[$index];
                $detalle->unidad = request()->unidad[$index];
                $detalle->cantidad = request()->cantidad[$index];
                $detalle->valor_unitario = str_replace(',', '', $this->get('valor_unitario'));
                $detalle->valor_total = $detalle->cantidad * $detalle->valor_unitario;

                $detalle->save();
                $total += $detalle->valor_total;
            }

            $cotizacion->valor = $total;
            $cotizacion->save();

            return response()->json([
                'success' => 'Cotización creada exitosamente!',
                'response' => [
                    'value' => $cotizacion->id_cotizacion,
                    'option' => $cotizacion->descripcion,
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
    public function show(TblCotizacion $quote)
    {
        $carrito = [];
        $items = TblCotizacionDetalle::with(['tblListaprecio'])->where(['id_cotizacion' => $quote->id_cotizacion])->get();

        foreach ($items as $item) {
            $carrito[$item->id_tipo_item][$item->id_lista_precio] = [
                'item' => $item->tblListaprecio->codigo,
                'descripcion' => $item->descripcion,
                'cantidad' => $item->cantidad,
                'unidad' => $item->unidad,
                'valor_unitario' => $item->valor_unitario,
                'valor_total' => $item->valor_total,
            ];
        }

        return view('cotizaciones._form', [
            'edit' => false,
            'cotizacion' => $quote,
            'carrito' => $carrito,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(TblCotizacion $quote)
    {
        $carrito = [];
        $items = TblCotizacionDetalle::with(['tblListaprecio'])->where(['id_cotizacion' => $quote->id_cotizacion])->get();

        foreach ($items as $item) {
            $carrito[$item->id_tipo_item][$item->id_lista_precio] = [
                'item' => $item->tblListaprecio->codigo,
                'descripcion' => $item->descripcion,
                'cantidad' => $item->cantidad,
                'unidad' => $item->unidad,
                'valor_unitario' => $item->valor_unitario,
                'valor_total' => $item->valor_total,
            ];
        }

        return view('cotizaciones._form', [
            'edit' => true,
            'cotizacion' => $quote,
            'carrito' => $carrito,
            'clientes' => TblTercero::getClientesTipo(session('id_dominio_cliente')),
            'estaciones' => TblPuntosInteres::where(['estado' => 1, 'id_cliente' => $quote->id_cliente])->pluck('nombre', 'id_punto_interes'),
            'tipos_trabajo' => TblDominio::getListaDominios(session('id_dominio_tipos_trabajo')),
            'prioridades' => TblDominio::getListaDominios(session('id_dominio_tipos_prioridad')),
            'impuestos' => TblDominio::getListaDominios(session('id_dominio_impuestos')),
            'contratistas' => TblTercero::getClientesTipo(session('id_dominio_contratista')),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SaveCotizacionRequest $request, TblCotizacion $quote)
    {
        try {
            $quote->update($request->validated());

            TblCotizacionDetalle::where('id_cotizacion', '=', $quote->id_cotizacion)->delete();

            $total = 0;
            foreach (request()->id_tipo_item as $index => $valor) {
                $detalle = new TblCotizacionDetalle;
                $detalle->id_cotizacion = $quote->id_cotizacion;
                $detalle->id_tipo_item = request()->id_tipo_item[$index];
                $detalle->id_lista_precio = request()->id_lista_precio[$index];
                $detalle->descripcion = request()->descripcion_item[$index];
                $detalle->unidad = request()->unidad[$index];
                $detalle->cantidad = request()->cantidad[$index];
                $detalle->valor_unitario = str_replace(',', '', request()->valor_unitario[$index]);
                $detalle->valor_total = $detalle->cantidad * $detalle->valor_unitario;

                $detalle->save();
                $total += $detalle->valor_total;
            }
            
            $quote->valor = $total;
            $quote->save();

            return response()->json([
                'success' => 'Cotización actualizada correctamente!'
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
        return $this->getView('cotizaciones.grid');
    }

    private function getView($view) {
        $cotizacion = new TblCotizacion;

        return view($view, [
            'model' => TblCotizacion::where(function ($q) {
                $this->dinamyFilters($q);
            })->latest()->paginate(10),
            'clientes' => TblTercero::getClientesTipo(session('id_dominio_cliente')),
            'estaciones' => TblPuntosInteres::where('estado', '=', 1)->pluck('nombre', 'id_punto_interes'),
            'prioridades' => TblDominio::getListaDominios(session('id_dominio_tipos_prioridad')),
            'procesos' => TblDominio::getListaDominios(session('id_dominio_tipos_proceso')),
            'contratistas' => TblTercero::getClientesTipo(session('id_dominio_contratista')),
            'status' => $cotizacion->status,
            'create' => true,//Gate::allows('create', $tercero),
            'edit' => true,//Gate::allows('update', $tercero),
            'view' => true,//Gate::allows('view', $tercero),
            'request' => $this->filtros,
        ]);
    }
}
