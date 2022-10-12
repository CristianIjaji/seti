<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveConsolidadoRequest;
use App\Models\TblActividad;
use App\Models\TblConsolidado;
use App\Models\TblConsolidadoDetalle;
use App\Models\TblTercero;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ConsolidadoController extends Controller
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
            }
            $this->filtros[$key] = $value;
        }

        return $querybuilder;
    }

    private function saveDetalleConsolidado($deal) {
        foreach (request()->id_actividad as $index => $valor) {
            $detalle = new TblConsolidadoDetalle;
            $detalle->id_consolidado = $deal->id_consolidado;
            $detalle->id_actividad = request()->id_actividad[$index];
            $detalle->observacion = request()->observacion[$index];

            $detalle->save();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view', new TblConsolidado);
        return $this->getView('consolidados.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', new TblConsolidado);

        return view('consolidados._form', [
            'consolidado' => new TblConsolidado,
            'clientes' => TblTercero::where([
                'estado' => 1,
                'id_dominio_tipo_tercero' => session('id_dominio_representante_cliente')
            ])->where('id_responsable_cliente', '>', 0)->get(),
            'contratistas' => TblTercero::where([
                'estado' => 1,
                'id_dominio_tipo_tercero' => session('id_dominio_coordinador')
            ])->where('id_responsable_cliente', '>', 0)->get(),
            'detalle_consolidado' => TblConsolidadoDetalle::where(['id_consolidado' => -1])->orderBy('id_detalle_consolidado', 'desc')->paginate(10)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveConsolidadoRequest $request)
    {
        try {
            $deal = TblConsolidado::create($request->validated());
            // $this->authorize('create', $deal);

            $this->saveDetalleConsolidado($deal);

            return response()->json([
                'success' => 'Cotización creada exitosamente!',
                'response' => [
                    'value' => $deal->id_consolidado,
                    'option' => $deal->anyo,
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
     * @param  \App\Models\Consolidado  $consolidado
     * @return \Illuminate\Http\Response
     */
    public function show(TblConsolidado $consolidado)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Consolidado  $consolidado
     * @return \Illuminate\Http\Response
     */
    public function edit(TblConsolidado $consolidado)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Consolidado  $consolidado
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TblConsolidado $consolidado)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Consolidado  $consolidado
     * @return \Illuminate\Http\Response
     */
    public function destroy(TblConsolidado $consolidado)
    {
        //
    }

    public function grid() {
        return $this->getView('consolidados.grid');
    }

    private function getView($view) {
        $consolidado = new TblConsolidado;

        return view($view, [
            'model' => TblConsolidado::where(function ($q) {
                $this->dinamyFilters($q);
            })->orderBy('id_consolidado', 'desc')->paginate(10),

            'create' => Gate::allows('create', $consolidado),
            'edit' => Gate::allows('update', $consolidado),
            'view' => Gate::allows('view', $consolidado),
            'request' => $this->filtros
        ]);
    }

    private function generateDownload($option) {

    }

    public function export() {
        
    }

    public function getActivities() {
        try {
            $id_cliente = request()->id_cliente;
            $id_responsable_cliente = request()->id_encargado;
            return view('consolidados.detalle', [
                'model' => TblActividad::select(
                    DB::raw("
                        ROW_NUMBER() OVER(PARTITION BY tbl_actividades.id_encargado_cliente) as item,
                        tbl_actividades.id_actividad,
                        z.nombre as zona,
                        tbl_actividades.ot,
                        e.nombre as estacion,
                        tbl_actividades.fecha_ejecucion,
                        tbl_actividades.descripcion,
                        c.valor as valor_cotizado,
                        det.observacion
                    ")
                )
                ->join('tbl_cotizaciones as c', 'tbl_actividades.id_cotizacion', '=', 'c.id_cotizacion')
                ->join('tbl_puntos_interes as e', 'c.id_estacion', '=', 'e.id_punto_interes')
                ->join('tbl_dominios as z', 'e.id_zona', '=', 'z.id_dominio')
                ->leftjoin('tbl_detalle_consolidado as det', 'tbl_actividades.id_actividad', '=', 'det.id_actividad')
                ->where([
                    'tbl_actividades.id_encargado_cliente' => $id_cliente,
                    'tbl_actividades.id_resposable_contratista' => $id_responsable_cliente
                ])
                ->orderBy('e.nombre', 'asc')
                ->get()
                // ->paginate(10)
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ]);
        }
    }
}
