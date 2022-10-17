<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveConsolidadoRequest;
use App\Models\TblActividad;
use App\Models\TblConsolidado;
use App\Models\TblConsolidadoDetalle;
use App\Models\TblDominio;
use App\Models\TblTercero;
use Carbon\Carbon;
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

                if($key == 'mes') {
                    $meses = ['enero' => 1, 'febrero' => 2, 'marzo' => 3, 'abril' => 4, 'mayo' => 5, 'junio' => 6,
                        'julio' => 7, 'agosto' => 8, 'septiembre' => 9, 'octubre' => 10, 'noviembre' => 11, 'diciembre' => 12
                    ];
                    $date = explode('-', $value);
                    $month = mb_strtolower($date[1]);
                    $value = $date[0].'-'.$meses[$month];
                }

                $querybuilder->where($key, (count($operador) > 1 ? $operador[0] : 'like'), (count($operador) > 1 ? $operador[1] : strtolower("%$value%")));
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

            $actividad = TblActividad::find($detalle->id_actividad);
            $actividad->mes_consolidado = $deal->mes_form;
            $actividad->save();
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
            'detalle_consolidado' => TblConsolidadoDetalle::where(['id_consolidado' => -1])->orderBy('id_consolidado_detalle', 'desc')->get()
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
    public function show(TblConsolidado $deal)
    {
        $this->authorize('view', $deal);

        return view('consolidados._form', [
            'edit' => false,
            'consolidado' => $deal,
            'detalle_consolidado' => TblConsolidadoDetalle::select(
                DB::raw("
                    ROW_NUMBER() OVER(PARTITION BY con.id_consolidado) as item,
                    tbl_consolidados_detalle.id_actividad,
                    zon.nombre as zona,
                    act.ot,
                    est.nombre as estacion,
                    act.fecha_ejecucion,
                    act.descripcion,
                    act.valor,
                    tbl_consolidados_detalle.observacion
                ")
            )
            ->join('tbl_consolidados as con', 'tbl_consolidados_detalle.id_consolidado', '=', 'con.id_consolidado')
            ->join('tbl_actividades as act', 'tbl_consolidados_detalle.id_actividad', '=', 'act.id_actividad')
            ->join('tbl_puntos_interes as est', 'act.id_estacion', '=', 'est.id_punto_interes')
            ->join('tbl_dominios as zon', 'est.id_zona', '=', 'zon.id_dominio')
            ->where([
                'tbl_consolidados_detalle.id_consolidado' => $deal->id_consolidado
            ])
            ->orderBy('tbl_consolidados_detalle.id_consolidado_detalle', 'asc')
            ->get()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Consolidado  $consolidado
     * @return \Illuminate\Http\Response
     */
    public function edit(TblConsolidado $deal)
    {
        $this->authorize('update', $deal);

        return view('consolidados._form',[
            'edit' => true,
            'consolidado' => $deal,
            'clientes' => TblTercero::where([
                'estado' => 1,
                'id_dominio_tipo_tercero' => session('id_dominio_representante_cliente')
            ])->where('id_responsable_cliente', '>', 0)->get(),
            'contratistas' => TblTercero::where([
                'estado' => 1,
                'id_dominio_tipo_tercero' => session('id_dominio_coordinador')
            ])->where('id_responsable_cliente', '>', 0)->get(),
            'detalle_consolidado' => TblConsolidadoDetalle::select(
                DB::raw("
                    ROW_NUMBER() OVER(PARTITION BY con.id_consolidado) as item,
                    tbl_consolidados_detalle.id_actividad,
                    zon.nombre as zona,
                    act.ot,
                    est.nombre as estacion,
                    act.fecha_ejecucion,
                    act.descripcion,
                    act.valor,
                    tbl_consolidados_detalle.observacion
                ")
            )
            ->join('tbl_consolidados as con', 'tbl_consolidados_detalle.id_consolidado', '=', 'con.id_consolidado')
            ->join('tbl_actividades as act', 'tbl_consolidados_detalle.id_actividad', '=', 'act.id_actividad')
            ->join('tbl_puntos_interes as est', 'act.id_estacion', '=', 'est.id_punto_interes')
            ->join('tbl_dominios as zon', 'est.id_zona', '=', 'zon.id_dominio')
            ->where([
                'tbl_consolidados_detalle.id_consolidado' => $deal->id_consolidado
            ])
            ->orderBy('tbl_consolidados_detalle.id_consolidado_detalle', 'asc')
            ->get()
        ]);
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
            'model' => TblConsolidado::with(['tblcliente', 'tblestadoconsolidado', 'tblresponsablecliente', 'tblusuario'])
            ->where(function ($q) {
                $this->dinamyFilters($q);
            })->orderBy('id_consolidado', 'desc')->paginate(10),
            'clientes' => TblTercero::getClientesTipo(session('id_dominio_representante_cliente')),
            'estados' => TblDominio::getListaDominios(session('id_dominio_estados_consolidado')),
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
                'edit' => true,
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
                        '' as observacion
                    ")
                )
                ->join('tbl_cotizaciones as c', 'tbl_actividades.id_cotizacion', '=', 'c.id_cotizacion')
                ->join('tbl_puntos_interes as e', 'c.id_estacion', '=', 'e.id_punto_interes')
                ->join('tbl_dominios as z', 'e.id_zona', '=', 'z.id_dominio')
                ->leftjoin('tbl_consolidados_detalle as det', 'tbl_actividades.id_actividad', '=', 'det.id_actividad')
                ->where([
                    'tbl_actividades.id_encargado_cliente' => $id_cliente,
                    'tbl_actividades.id_resposable_contratista' => $id_responsable_cliente,
                    'tbl_actividades.mes_consolidado' => null
                ])
                ->orderBy('e.nombre', 'asc')
                ->get()
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ]);
        }
    }
}
