<?php

namespace App\Http\Controllers;

use App\Exports\ConsolidadoExport;
use App\Http\Requests\SaveConsolidadoRequest;
use App\Models\TblActividad;
use App\Models\TblConsolidado;
use App\Models\TblConsolidadoDetalle;
use App\Models\TblDominio;
use App\Models\TblTercero;
use Illuminate\Support\Facades\Gate;
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
        foreach (request()->all() as $key => $value) {
            if($value !== null && !in_array($key, ['_token', 'table', 'page'])) {
                $query = getValoresConsulta($value);

                $key = (array_search($key, $fields) ? array_search($key, $fields) : $key);

                if($key == 'mes') {
                    $meses = ['enero' => 1, 'febrero' => 2, 'marzo' => 3, 'abril' => 4, 'mayo' => 5, 'junio' => 6,
                        'julio' => 7, 'agosto' => 8, 'septiembre' => 9, 'octubre' => 10, 'noviembre' => 11, 'diciembre' => 12
                    ];
                    $date = explode('-', $value);
                    $month = mb_strtolower($date[1]);
                    $value = $date[0].'-'.$meses[$month];
                }

                $querybuilder->where($key, $query['operator'], $query['value']);
            }

            $this->filtros[$key] = $value;
        }

        return $querybuilder;
    }

    private function saveDetalleConsolidado($deal) {
        TblActividad::join('tbl_consolidados_detalle as det', 'tbl_actividades.id_actividad', '=', 'det.id_actividad')
            ->where('det.id_consolidado', '=', $deal->id_consolidado)
            ->wherenotin('tbl_actividades.id_actividad', request()->id_actividad)
            ->update(['mes_consolidado' => null]);

        TblConsolidadoDetalle::where('id_consolidado', '=', $deal->id_consolidado)->wherenotin('id_actividad', request()->id_actividad)->delete();

        foreach (request()->id_actividad as $index => $valor) {
            $detalle = TblConsolidadoDetalle::where(['id_consolidado' => $deal->id_consolidado, 'id_actividad' => request()->id_actividad[$index]])->first();
            if(!$detalle) {
                $detalle = new TblConsolidadoDetalle;
            }

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
            ])->where('id_tercero_responsable', '>', 0)->get(),
            'contratistas' => TblTercero::where([
                'estado' => 1
            ])
            ->whereIN('id_dominio_tipo_tercero', [session('id_dominio_coordinador'), session('id_dominio_contratista')])
            ->get(),
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
            $this->authorize('create', new TblConsolidado);

            DB::beginTransaction();
            $deal = TblConsolidado::create($request->validated());

            $this->saveDetalleConsolidado($deal);
            DB::commit();

            return response()->json([
                'success' => 'Cotización creada exitosamente!',
                'response' => [
                    'value' => $deal->id_consolidado,
                    'option' => $deal->anyo,
                ],
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Error creando consolidado: ".$th->__toString());

            return response()->json([
                'errors' => 'Error creando consolidado.'
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
                    ROW_NUMBER() OVER(PARTITION BY con.id_consolidado ORDER BY tbl_consolidados_detalle.id_consolidado_detalle) as item,
                    tbl_consolidados_detalle.id_actividad,
                    zon.nombre as zona,
                    act.ot,
                    est.nombre as estacion,
                    act.fecha_ejecucion,
                    act.descripcion,
                    act.valor as valor_cotizado,
                    act.valor,
                    tbl_consolidados_detalle.observacion
                ")
            )
            ->join('tbl_consolidados as con', 'tbl_consolidados_detalle.id_consolidado', '=', 'con.id_consolidado')
            ->join('tbl_actividades as act', 'tbl_consolidados_detalle.id_actividad', '=', 'act.id_actividad')
            ->join('tbl_puntos_interes as est', 'act.id_estacion', '=', 'est.id_punto_interes')
            ->join('tbl_dominios as zon', 'est.id_dominio_zona', '=', 'zon.id_dominio')
            ->where([
                'tbl_consolidados_detalle.id_consolidado' => $deal->id_consolidado
            ])
            ->orderBy('item', 'asc')
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
            ])->where('id_tercero_responsable', '>', 0)->get(),
            'contratistas' => TblTercero::where([
                'estado' => 1
            ])
            ->whereIN('id_dominio_tipo_tercero', [session('id_dominio_coordinador'), session('id_dominio_contratista')])
            ->get(),
            'detalle_consolidado' => TblConsolidadoDetalle::select(
                DB::raw("
                    ROW_NUMBER() OVER(PARTITION BY con.id_consolidado) as item,
                    tbl_consolidados_detalle.id_actividad,
                    zon.nombre as zona,
                    act.ot,
                    est.nombre as estacion,
                    act.fecha_ejecucion,
                    act.descripcion,
                    act.valor as valor_cotizado,
                    act.valor,
                    tbl_consolidados_detalle.observacion
                ")
            )
            ->join('tbl_consolidados as con', 'tbl_consolidados_detalle.id_consolidado', '=', 'con.id_consolidado')
            ->join('tbl_actividades as act', 'tbl_consolidados_detalle.id_actividad', '=', 'act.id_actividad')
            ->join('tbl_puntos_interes as est', 'act.id_estacion', '=', 'est.id_punto_interes')
            ->join('tbl_dominios as zon', 'est.id_dominio_zona', '=', 'zon.id_dominio')
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
    public function update(SaveConsolidadoRequest $request, TblConsolidado $deal)
    {
        try {
            $this->authorize('update', $deal);
       
            DB::beginTransaction();
            $deal->update($request->validated());
            $this->saveDetalleConsolidado($deal);

            DB::commit();
            return response()->json([
                'success' => 'Consolidado actualizado correctamente!'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Error editando consolidado: ".$th->__toString());
            return response()->json([
                'errors' => 'Error editando consolidado.'
            ]);
        }
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
            'clientes' => TblTercero::getTercerosTipo(session('id_dominio_representante_cliente')),
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

    public function exportDeal() {
        $deal = TblConsolidado::with(['tblconsolidadodetalle'])->find(request()->deal)->get();
        return Excel::download(new ConsolidadoExport($deal), 'Consolidado.xlsx');
    }

    public function getActivities() {
        try {
            $id_tercero_cliente = request()->id_tercero_cliente;
            $id_tercero_responsable = request()->id_tercero_encargado;
            $id_consolidado = request()->id_consolidado;

            $filters = [
                'tbl_actividades.id_tercero_encargado_cliente' => $id_tercero_cliente,
                'tbl_actividades.id_tercero_resposable_contratista' => $id_tercero_responsable,
                'tbl_actividades.id_dominio_estado' => session('id_dominio_actividad_liquidado'),
                // 'det.id_consolidado' => $id_consolidado,
                // 'tbl_actividades.mes_consolidado' => null,
            ];

            return view('consolidados.detalle', [
                'edit' => true,
                'model' => TblActividad::select(
                    DB::raw("
                        ROW_NUMBER() OVER(PARTITION BY tbl_actividades.id_tercero_encargado_cliente ORDER BY e.nombre) as item,
                        tbl_actividades.id_actividad,
                        z.nombre as zona,
                        tbl_actividades.ot,
                        e.nombre as estacion,
                        tbl_actividades.fecha_ejecucion,
                        tbl_actividades.descripcion,
                        c.valor as valor_cotizado,
                        c.valor,
                        det.observacion
                    ")
                )
                ->join('tbl_cotizaciones as c', 'tbl_actividades.id_cotizacion', '=', 'c.id_cotizacion')
                ->join('tbl_puntos_interes as e', 'c.id_estacion', '=', 'e.id_punto_interes')
                ->join('tbl_dominios as z', 'e.id_dominio_zona', '=', 'z.id_dominio')
                ->leftjoin('tbl_consolidados_detalle as det', 'tbl_actividades.id_actividad', '=', 'det.id_actividad')
                ->where($filters)
                // ->orwhere([
                //     'det.id_consolidado' => $id_consolidado,
                //     'tbl_actividades.id_dominio_estado' => session('id_dominio_actividad_liquidado')
                // ])
                // ->orwhere('det.id_consolidado', '=', $id_consolidado)
                ->orderBy('item', 'asc')
                ->get()
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ]);
        }
    }
}
