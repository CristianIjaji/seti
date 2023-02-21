<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveLiquidacionRequest;
use App\Models\TblCotizacionDetalle;
use App\Models\TblInventario;
use App\Models\TblLiquidacion;
use App\Models\TblLiquidacionDetalle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Excel;

class LiquidacionController extends Controller
{
    protected $excel;

    public function __construct(Excel $excel)
    {
        $this->middleware('auth');
        $this->excel = $excel;
    }

    private function getDetailCloseout($closeout) {
        if(!isset(request()->id_item)) {
            return 0;
        }

        TblLiquidacionDetalle::where('id_liquidacion', '=', $closeout->id_liquidacion)->wherenotin('id_lista_precio', request()->id_item)->delete();
        $total = 0;

        foreach (request()->id_dominio_tipo_item as $index => $valor) {
            $detalle = TblLiquidacionDetalle::where(['id_liquidacion' => $closeout->id_liquidacion, 'id_lista_precio' => request()->id_item[$index]])->first();
            if(!$detalle) {
                $detalle = new TblLiquidacionDetalle;
            }

            $item = TblCotizacionDetalle::where(['id_cotizacion' => $closeout->tblactividad->id_cotizacion, 'id_lista_precio' => request()->id_item[$index]])->first();
            if(!$item) {
                $item = TblInventario::find(request()->id_item[$index]);
            }

            $detalle->id_liquidacion = $closeout->id_liquidacion;
            $detalle->id_dominio_tipo_item = request()->id_dominio_tipo_item[$index];
            $detalle->id_lista_precio = request()->id_item[$index];
            $detalle->descripcion = $item->descripcion;
            $detalle->unidad = $item->unidad;
            $detalle->cantidad = request()->cantidad[$index];
            $detalle->valor_unitario = str_replace(',', '', request()->valor_unitario[$index]);
            $detalle->valor_total = $detalle->cantidad * $detalle->valor_unitario;

            $detalle->save();
            $total += $detalle->valor_total;
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
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveLiquidacionRequest $request)
    {
        try {
            DB::beginTransaction();

            $liquidacion = TblLiquidacion::create($request->validated());
            $liquidacion->valor = $this->getDetailCloseout($liquidacion);
            $liquidacion->save();

            $controller = new ActividadController($this->excel);
            request()->merge([
                'action' => 'liquid-activity',
                'comentario' => 'Actividad liquidada por '.$liquidacion->tblusereg->tbltercero->full_name
            ]);
            $controller->handleActivity($liquidacion->tblactividad);

            DB::commit();
            return response()->json([
                'success' => 'Liquidación creada exitosamente!',
                'response' => [
                    'value' => $liquidacion->id_liquidacion,
                    'option' => $liquidacion->id_liquidacion,
                ],
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->__toString());

            return response()->json([
                'errors' => 'Error creando la liquidación.'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    public function update(SaveLiquidacionRequest $request, TblLiquidacion $closeout)
    {
        try {
            DB::beginTransaction();

            $closeout->update($request->validated());
            $closeout->valor = $this->getDetailCloseout($closeout);
            $closeout->save();

            $controller = new ActividadController($this->excel);
            request()->merge([
                'action' => 'liquid-activity',
                'comentario' => 'Actividad liquidada por '.$closeout->tblusereg->tbltercero->full_name
            ]);
            $controller->handleActivity($closeout->tblactividad);

            DB::commit();
            return response()->json([
                'success' => 'Liquidación actualizada correctamente!'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->__toString());

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
}
