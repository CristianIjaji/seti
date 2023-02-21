<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveActividadRequest;
use App\Http\Requests\SaveCotizacionRequest;
use App\Models\TblActividad;
use App\Models\TblCotizacion;
use App\Models\TblDominio;
use App\Models\TblPuntosInteres;
use App\Models\TblTercero;
use App\Models\TblUsuario;
use App\Models\TblEstado;
use App\Models\TblInformeActivdad;
use App\Models\TblLiquidacion;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Pusher\Pusher;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel;

class ActividadController extends Controller
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

                if(!in_array($key, ['nombre'])) {
                    $querybuilder->where($key, $query['operator'], $query['value']);
                } else if($key == 'nombre') {
                    $querybuilder->whereHas('tblestacion', function($q) use($query) {
                        $q->where('nombre', $query['operator'], $query['value']);
                    });
                }

            }

            $this->filtros[$key] = $value;
        }

        return $querybuilder;
    }

    private function sendNotification($actividad, $channel, $event) {
        $options = [
            'cluster' => 'us2',
            'useTLS' => true
        ];

        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );

        $pusher->trigger(
            $channel,
            $event,
            $actividad
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view', new TblActividad);
        
        return $this->getView('actividades.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', new TblActividad);

        $id_tercero_cliente = 0;
        if(isset(request()->cotizacion)) {
            $quote = TblCotizacion::find(request()->cotizacion);
            $id_tercero_cliente = (isset($quote->tblCliente->id_tercero_responsable)
                ? $quote->tblCliente->id_tercero_responsable
                : $quote->id_tercero_cliente
            );
        }

        return view('actividades._form', [
            'activity' => new TblActividad,
            'create_client' => isset(TblUsuario::getPermisosMenu('clients.index')->create) ? TblUsuario::getPermisosMenu('clients.index')->create : false,
            'tipos_trabajo' => TblDominio::getListaDominios(session('id_dominio_tipos_trabajo'), 'nombre'),
            'create_site' => isset(TblUsuario::getPermisosMenu('sites.index')->create) ? TblUsuario::getPermisosMenu('sites.index')->create : false,
            'contratistas' => TblTercero::where('estado', '=', '1')
                ->wherein('id_dominio_tipo_tercero', [session('id_dominio_coordinador'), session('id_dominio_contratista')])
                ->get(),
            'clientes' => TblTercero::where([
                'estado' => 1,
                'id_dominio_tipo_tercero' => session('id_dominio_representante_cliente')
            ])->where('id_tercero_responsable', '>', 0)->get(),
            'proveedores' => TblTercero::where([
                'estado' => 1,
                'id_dominio_tipo_tercero' => session('id_dominio_proveedor')
            ])->get(),
            'medios_pago_ordenes_compra' => TblDominio::getListaDominios(session('id_dominio_medio_pago_orden_compra')),
            'tipos_ordenes_compra' => TblDominio::getListaDominios(session('id_dominio_tipo_orden_compra')),
            'estaciones' => TblPuntosInteres::where(['estado' => 1, 'id_tercero_cliente' => $id_tercero_cliente])->pluck('nombre', 'id_punto_interes'),
            'prioridades' => TblDominio::getListaDominios(session('id_dominio_tipos_prioridad')),
            'subsistemas' => TblDominio::getListaDominios(session('id_dominio_subsistemas'), 'nombre'),
            'estados' => TblDominio::wherein('id_dominio', [session('id_dominio_actividad_programado'), session('id_dominio_actividad_comprando')])->get(),
            'quote' => isset(request()->cotizacion) ? TblCotizacion::find(request()->cotizacion) : [],
            'movimiento' => null
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveActividadRequest $request)
    {
        try {            
            $this->authorize('create', new TblActividad);

            DB::beginTransaction();
            $actividad = TblActividad::create($request->validated());

            $actividad->comentario = $actividad->observaciones;
            $this->createTrack($actividad, $actividad->id_dominio_estado);

            if(!empty($actividad->ot) && !empty($actividad->id_cotizacion) && $actividad->tblcotizacion->ot_trabajo != $actividad->ot) {
                $cotizacion = TblCotizacion::find($actividad->id_cotizacion);
                $cotizacion->ot_trabajo = $actividad->ot;
                $cotizacion->save();
            }

            DB::commit();
            return response()->json([
                'success' => 'Actividad creada exitosamente!',
                'response' => [
                    'value' => $actividad->id_actividad,
                    'option' => $actividad->descripcion,
                ],
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Error creando actividad: ".$th->__toString());
            return response()->json([
                'errors' => 'Error creando actividad.'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(TblActividad $activity)
    {
        $this->authorize('view', $activity);

        return view('actividades._form', [
            'edit' => false,
            'activity' => $activity,
            'estados_actividad' => TblEstado::where(['id_tabla' => $activity->id_actividad, 'tabla' => $activity->getTable()])->orderby('created_at', 'desc')->paginate(1000000),
            'quote' => isset($activity->id_cotizacion) ? $activity->tblcotizacion : [],
            'movimiento' => $activity->getMovimientoInventario(),
            'carrito' => isset($activity->id_cotizacion) ? TblLiquidacion::getDetalleLiquidacion($activity) : [],
            'liquidacion' => TblLiquidacion::where(['id_actividad' => $activity->id_actividad])->first(),
            'uploadReport' => false,
            'liquidate' => false
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(TblActividad $activity)
    {
        $this->authorize('update', $activity);

        $id_tercero_cliente = (isset($activity->tblencargadocliente->id_tercero_responsable)
            ? $activity->tblencargadocliente->id_tercero_responsable
            : $activity->id_tercero_encargado_cliente
        );

        return view('actividades._form', [
            'edit' => true,
            'activity' => $activity,
            'clientes' => TblTercero::where([
                'estado' => 1,
                'id_dominio_tipo_tercero' => session('id_dominio_representante_cliente')
            ])->where('id_tercero_responsable', '>', 0)->get(),
            'estaciones' => TblPuntosInteres::where(['estado' => 1, 'id_tercero_cliente' => $id_tercero_cliente])->pluck('nombre', 'id_punto_interes'),
            'tipos_trabajo' => TblDominio::getListaDominios(session('id_dominio_tipos_trabajo'), 'nombre'),
            'subsistemas' => TblDominio::getListaDominios(session('id_dominio_subsistemas'), 'nombre'),
            'estados_actividad' => TblEstado::where(['id_tabla' => $activity->id_actividad, 'tabla' => $activity->getTable()])->orderby('created_at','desc')->paginate(1000000),
            'estados' => TblDominio::where(['id_dominio_padre' => session('id_dominio_estados_actividad')])->get(),
            'contratistas' => TblTercero::where('estado', '=', '1')
                ->wherein('id_dominio_tipo_tercero', [session('id_dominio_coordinador'), session('id_dominio_contratista')])
                ->get(),
            'cotizaciones' => TblCotizacion::select(DB::raw('DISTINCT tbl_cotizaciones.*'))
            ->leftjoin('tbl_actividades as act', 'tbl_cotizaciones.id_cotizacion', '=', 'act.id_cotizacion')
            ->where('act.id_cotizacion')
            ->where([
                'tbl_cotizaciones.id_tercero_cliente' => $activity->id_tercero_encargado_cliente,
                'tbl_cotizaciones.id_estacion' => $activity->id_estacion,
                'tbl_cotizaciones.id_dominio_tipo_trabajo' => $activity->id_tipo_actividad,
                'tbl_cotizaciones.id_dominio_estado' => session('id_dominio_cotizacion_aprobada')
            ])
            ->get(),
            'create_client' => isset(TblUsuario::getPermisosMenu('clients.index')->create) ? TblUsuario::getPermisosMenu('clients.index')->create : false,
            'create_site' => isset(TblUsuario::getPermisosMenu('sites.index')->create) ? TblUsuario::getPermisosMenu('sites.index')->create : false,
            'quote' => isset($activity->id_cotizacion) ? $activity->tblcotizacion : [],
            'movimiento' => $activity->getMovimientoInventario(),
            'carrito' => isset($activity->id_cotizacion) ? TblLiquidacion::getDetalleLiquidacion($activity) : [],
            'liquidacion' => TblLiquidacion::where(['id_actividad' => $activity->id_actividad])->first(),
            'uploadReport' => Gate::allows('uploadReport', $activity),
            'liquidate' => Gate::allows('liquidatedActivity', $activity),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SaveActividadRequest $request, TblActividad $activity)
    {
        try {
            $this->authorize('update', $activity);
            DB::beginTransaction();

            $activity->update($request->validated());

            if(isset($activity->tblcotizacion)) {
                if($activity->valor !== $activity->tblcotizacion->total_sin_iva) {
                    $activity->valor = $activity->tblcotizacion->valor;
                    $activity->update();
                }
            }

            $activity->comentario = $activity->observaciones;
            $this->createTrack($activity, $activity->id_dominio_estado);

            if(!empty($activity->id_cotizacion) && $activity->tblcotizacion->ot_trabajo != $activity->ot) {
                $cotizacion = TblCotizacion::find($activity->id_cotizacion);
                $cotizacion->ot_trabajo = $activity->ot;
                $cotizacion->save();
            }

            DB::commit();
            return response()->json([
                'success' => 'Cotización actualizada correctamente!'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Error editando actividad: ".$th->__toString());
            return response()->json([
                'errors' => 'Error editando actividad.'
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

    public function handleActivity(TblActividad $activity) {
        try {
            $response = '';
            switch (request()->action) {
                case 'reshedule-activity':
                    $activity->comentario = (
                        isset(request()->comentario) && trim(request()->comentario) != ''
                            ? request()->comentario
                            : "Actividad reprogramada."
                    )."\nFecha: ".request()->input_fecha;
                    $activity->fecha_reprogramacion = request()->input_fecha;
                    $response = $this->updateActivity(
                        $activity,
                        [session('id_dominio_actividad_programado'), session('id_dominio_actividad_pausada')],
                        session('id_dominio_actividad_reprogramado'),
                        'Actividad reprogramada!',
                        ''
                    );
                    break;
                case 'pause-activity':
                    $activity->comentario = (
                        isset(request()->comentario) && trim(request()->comentario) != ''
                            ? request()->comentario
                            : "Actividad pausada."
                    )." ".request()->input_fecha;
                    $response = $this->updateActivity(
                        $activity,
                        [session('id_dominio_actividad_programado'), session('id_dominio_actividad_reprogramado')],
                        session('id_dominio_actividad_pausada'),
                        'Actividad pausada!',
                        ''
                    );
                    break;
                case 'executed-activity':
                    $activity->comentario = (
                        isset(request()->comentario) && trim(request()->comentario) != ''
                            ? request()->comentario
                            : "Actividad ejecutada."
                    )."\nFecha: ".request()->input_fecha;
                    $activity->fecha_ejecucion = (isset(request()->input_fecha) && !empty(request()->input_fecha) ? request()->input_fecha : date('Y-m-d'));
                    $response = $this->updateActivity(
                        $activity,
                        [session('id_dominio_actividad_programado'), session('id_dominio_actividad_reprogramado'), session('id_dominio_actividad_pausada')],
                        session('id_dominio_actividad_ejecutado'),
                        'Actividad ejecutada',
                        ''
                    );
                    break;
                case 'liquid-activity':
                    $activity->comentario = (
                        isset(request()->comentario) && trim(request()->comentario) != ''
                            ? request()->comentario
                            : 'Actividad liquidada.'
                    );
                    $activity->fecha_liquidado = date('Y-m-d');
                    $response = $this->updateActivity(
                        $activity,
                        [session('id_dominio_actividad_informe_cargado')],
                        session('id_dominio_actividad_liquidado'),
                        'Actividad liquidada',
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
        return $this->getView('actividades.grid');
    }

    private function getView($view) {
        $actividad = new TblActividad;

        $contratistas = TblTercero::getTercerosTipo(session('id_dominio_contratista'));

        return view($view, [
            'model' => TblActividad::with(['tbltipoactividad', 'tblsubsistema', 'tblencargadocliente',
                'tblresposablecontratista', 'tblestacion', 'tblestadoactividad', 'tblcotizacion', 'tblusuario'])
                ->where(function($q) {
                    $this->dinamyFilters($q);
                })->latest()->paginate(10),
            'clientes' => TblTercero::getTercerosTipo(session('id_dominio_representante_cliente')),
            'tipos_trabajo' => TblDominio::getListaDominios(session('id_dominio_tipos_trabajo'), 'nombre'),
            'contratistas' => TblTercero::getTercerosTipo(session('id_dominio_coordinador'))->union($contratistas),
            'estados_actividad' => TblDominio::getListaDominios(session('id_dominio_estados_actividad')),
            'status' => $actividad->status,
            'create' => Gate::allows('create', $actividad),
            'edit' => Gate::allows('update', $actividad),
            'view' => Gate::allows('view', $actividad),
            'request' => $this->filtros,
        ]);
    }

    private function updateActivity($activity, $estados, $nuevoEstado, $msg, $notification, $usuarioFinal = '') {
        try {
            if(in_array($activity->id_dominio_estado, $estados)) {
                $activity->id_dominio_estado = $nuevoEstado;

                $this->createTrack($activity, $nuevoEstado);
                unset($activity->comentario);
                $activity->save();
            }

            $id_usuario = TblActividad::find($activity->id_actividad)->id_usuareg;

            if($notification !== '') {
                $channel = 'user-'.(intval(auth()->id()) != intval($id_usuario)
                    ? $id_usuario
                    : (isset($activity->tblresposablecontratista->tbluser->idu_usuario) ? $activity->tblresposablecontratista->tbluser->idu_usuario : null)
                );

                $this->sendNotification($activity, $channel, $notification);
            }

            return ['success' => $msg];
        } catch (\Throwable $th) {
            Log::error($th->__toString());
            return ['error' => $th->getMessage()];
        }
    }

    private function createTrack($activity, $action) {
        try {
            TblEstado::create([
                'id_tabla' => $activity->id_actividad,
                'tabla' => $activity->getTable(),
                'id_dominio_estado' => $action,
                'comentario' => $activity->comentario,
                'id_usuareg' => auth()->id()
            ]);
            
        } catch (\Throwable $th) {
            Log::error("Error creando track de actividad: ".$th->__toString());
        }
    }

    public function cotizacionesCliente(TblActividad $activity) {
        return view('partials._search', [
            'minimuminputlength' => 0,
            'multiple' => false,
            'cotizaciones' => TblCotizacion::select(DB::raw('DISTINCT tbl_cotizaciones.*'))
            ->leftjoin('tbl_actividades as act', 'tbl_cotizaciones.id_cotizacion', '=', 'act.id_cotizacion')
            ->where('act.id_cotizacion')
            ->where([
                'tbl_cotizaciones.id_tercero_cliente' => $activity->id_tercero_encargado_cliente,
                'tbl_cotizaciones.id_estacion' => $activity->id_estacion,
                'tbl_cotizaciones.id_dominio_tipo_trabajo' => $activity->id_tipo_actividad,
                'tbl_cotizaciones.id_dominio_estado' => session('id_dominio_cotizacion_aprobada')
            ])
            ->get(),
        ]);
    }

    public function seguimiento(TblActividad $activity) {
        return view('partials._seguimiento', [
            'model' => $activity,
            'route' => 'activities.handleActivity'
        ]);
    }

    public function getResponsablesInventario() {
        return DB::select("WITH movimientos AS
            (
                SELECT
                    m.id_movimiento,
                    m.id_tercero_recibe,
                    m.id_tercero_entrega,
                    m.documento,
                    id_dominio_tipo_movimiento
            
                FROM tbl_movimientos AS m
                WHERE m.id_dominio_estado = :id_estado_movimiento
                AND documento > 0
                AND id_dominio_tipo_movimiento IN(:id_tipo_movimientos)
            ),
            salidas AS (
                SELECT
                    m.id_tercero_recibe,
                    det.id_inventario,
                    det.cantidad,
                    m.documento AS id_actividad
                FROM movimientos AS m
                INNER JOIN tbl_movimientos_detalle AS det ON(det.id_movimiento = m.id_movimiento)
                WHERE m.id_dominio_tipo_movimiento = :id_tipo_salida
            ),
            entradas AS (
                SELECT
                    m.id_tercero_entrega,
                    det.id_inventario,
                    det.cantidad,
                    m.documento AS id_actividad
                FROM tbl_movimientos AS m
                INNER JOIN tbl_movimientos_detalle AS det ON(det.id_movimiento = m.id_movimiento)
                WHERE id_dominio_tipo_movimiento = :id_tipo_entrada
            )
            
            SELECT
                t.id_tercero,
                CONCAT(t.nombres, ' ', t.apellidos) AS nombre
            FROM salidas
            INNER JOIN tbl_terceros AS t ON(t.id_tercero = salidas.id_tercero_recibe)
            LEFT JOIN entradas ON(
                entradas.id_tercero_entrega = salidas.id_tercero_recibe
                AND entradas.id_actividad = salidas.id_actividad
                AND entradas.id_inventario = salidas.id_inventario
            )

            WHERE entradas.id_inventario IS NULL
            GROUP BY t.id_tercero, t.nombres, t.apellidos
        ", [
            'id_estado_movimiento' => session('id_dominio_movimiento_completado'),
            'id_tipo_movimientos' => session('id_dominio_movimiento_salida_actividad').','.session('id_dominio_movimiento_entrada_devolucion'),
            'id_tipo_salida' => session('id_dominio_movimiento_salida_actividad'),
            'id_tipo_entrada' => session('id_dominio_movimiento_entrada_devolucion')
        ]);
    }

    public function getDocumentos($id_tercero_responsable, $id_tercero_almacen) {
        if(empty($id_tercero_responsable) || empty($id_tercero_almacen)) {
            return response()->json(['errors' => 'Error obteniendo actividaddes.']);
        }

        return response()->json([
            'documentos' => DB::select("WITH movimientos AS
                (
                    SELECT
                        m.id_movimiento,
                        m.id_tercero_recibe,
                        m.id_tercero_entrega,
                        m.documento,
                        id_dominio_tipo_movimiento
                
                    FROM tbl_movimientos AS m
                    WHERE m.id_dominio_estado = :id_estado_movimiento
                    AND documento > 0
                    AND id_dominio_tipo_movimiento IN(:id_tipo_movimientos)
                    AND m.id_tercero_recibe = :id_tercero_recibe
                    AND m.id_tercero_entrega = :id_tercero_entrega
                ),
                salidas AS (
                    SELECT
                        m.id_tercero_recibe,
                        det.id_inventario,
                        det.cantidad,
                        m.documento AS id_actividad
                    FROM movimientos AS m
                    INNER JOIN tbl_movimientos_detalle AS det ON(det.id_movimiento = m.id_movimiento)
                    WHERE m.id_dominio_tipo_movimiento = :id_tipo_salida
                ),
                entradas AS (
                    SELECT
                        m.id_tercero_entrega,
                        det.id_inventario,
                        det.cantidad,
                        m.documento AS id_actividad
                    FROM tbl_movimientos AS m
                    INNER JOIN tbl_movimientos_detalle AS det ON(det.id_movimiento = m.id_movimiento)
                    WHERE id_dominio_tipo_movimiento = :id_tipo_entrada
                )
                
                SELECT
                    salidas.id_actividad
                FROM salidas
                LEFT JOIN entradas ON(
                    entradas.id_tercero_entrega = salidas.id_tercero_recibe
                    AND entradas.id_actividad = salidas.id_actividad
                    AND entradas.id_inventario = salidas.id_inventario
                )

                WHERE entradas.id_inventario IS NULL
                GROUP BY salidas.id_actividad
            ", [
                'id_estado_movimiento' => session('id_dominio_movimiento_completado'),
                'id_tipo_movimientos' => session('id_dominio_movimiento_salida_actividad').','.session('id_dominio_movimiento_entrada_devolucion'),
                'id_tercero_recibe' => $id_tercero_responsable,
                'id_tercero_entrega' => $id_tercero_almacen,
                'id_tipo_salida' => session('id_dominio_movimiento_salida_actividad'),
                'id_tipo_entrada' => session('id_dominio_movimiento_entrada_devolucion')
            ])
        ]);
    }

    public function uploadReport(TblActividad $activity) {
        try {
            if($activity->id_dominio_estado != session('id_dominio_actividad_ejecutado') && $activity->id_informe_actividad) {
                throw new Exception("El reporte ya se cargo");
            }

            if(!isset($activity->id_informe_actividad)) {
                $fileName = Storage::disk('google')->put('', Request()->file('file_report'));
                $link = Storage::disk('google')->url($fileName);

                $informe = TblInformeActivdad::create([
                    'id_actividad' => $activity->id_actividad,
                    'link' => $link,
                    'id_usuareg' => auth()->id(),
                ]);

                $activity->id_informe_actividad = $informe->id_informe_actividad;
            } else {
                if(!empty($activity->tblinforme->link)) {
                    $parts = parse_url($activity->tblinforme->link);

                    parse_str($parts['query'], $query);
                    $id_file = isset($query['id']) ? $query['id'] : '';

                    if($id_file) {
                        Storage::disk('google')->delete(env('GOOGLE_DRIVE_FOLDER_ID')."/$id_file");
                    }
                }

                $fileName = Storage::disk('google')->put('', Request()->file('file_report'));
                $link = Storage::disk('google')->url($fileName);

                $informe = TblInformeActivdad::where(['id_actividad' => $activity->id_actividad])->first();
                $informe->link = $link;
                $informe->save();
            }

            $activity->id_dominio_estado = session('id_dominio_actividad_informe_cargado');
            $activity->save();
            $activity->comentario = "Reporte cargado al drive";
            $this->createTrack($activity, $activity->id_dominio_estado);

            return response()->json([
                'success' => 'Reporte subido con exito!',
                'url' => $link
            ]);
        } catch (\Throwable $th) {
            Log::error("Error subiendo reporte: ".$th->__toString());
            return response()->json([
                'errors' => $th->getMessage()
            ]);
        }
    }

    public function downloadReport(TblActividad $activity) {
        $parts = parse_url($activity->tblinforme->link);
        parse_str($parts['query'], $query);

        $id_file = isset($query['id']) ? $query['id'] : '';
        if($id_file) {
            $rawData = Storage::disk('google')->get(env('GOOGLE_DRIVE_FOLDER_ID')."/$id_file");
        }

        return response($rawData, 200)
                ->header('ContentType', 'application/pdf')
                ->header('Content-Disposition', "attachment; filename=".'Reporte actividad '.$activity->id_actividad.'.pdf');
    }
}
