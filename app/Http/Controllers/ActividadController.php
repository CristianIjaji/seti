<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveActividadRequest;
use App\Models\TblActividad;
use App\Models\TblCotizacion;
use App\Models\TblDominio;
use App\Models\TblPuntosInteres;
use App\Models\TblTercero;
use App\Models\TblUsuario;
use App\Models\TblEstadoActividad;
use App\Models\TblOrdenCompraDetalle;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Pusher\Pusher;

class ActividadController extends Controller
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

    private function sendNotification($actividad, $channel, $event) {
        // $activity
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
            $actividad = TblActividad::create($request->validated());
            $this->authorize('create', $actividad);

            $actividad->comentario = $actividad->observaciones;
            $this->createTrack($actividad, $actividad->id_dominio_estado);

            return response()->json([
                'success' => 'Actividad creada exitosamente!',
                'response' => [
                    'value' => $actividad->id_actividad,
                    'option' => $actividad->descripcion,
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
    public function show(TblActividad $activity)
    {
        $this->authorize('view', $activity);

        return view('actividades._form', [
            'edit' => false,
            'activity' => $activity,
            'estados_actividad' => TblEstadoActividad::where(['id_actividad' =>$activity->id_actividad])->orderby('created_at', 'desc')->paginate(10),
            'quote' => isset($activity->id_cotizacion) ? $activity->tblcotizacion : []
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
            'estados_actividad' => TblEstadoActividad::where(['id_actividad' =>$activity->id_actividad])->orderby('created_at','desc')->paginate(10),
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
            'quote' => isset($activity->id_cotizacion) ? $activity->tblcotizacion : []
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
            $activity->update($request->validated());

            if($activity->valor !== $activity->tblcotizacion->total_sin_iva) {
                $activity->valor = $activity->tblcotizacion->valor;
                $activity->update();
            }

            $activity->comentario = $activity->observaciones;
            $this->createTrack($activity, $activity->id_dominio_estado);

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
                    $activity->fecha_ejecucion = request()->input_fecha;
                    $response = $this->updateActivity(
                        $activity,
                        [session('id_dominio_actividad_programado'), session('id_dominio_actividad_reprogramado'), session('id_dominio_actividad_pausada')],
                        session('id_dominio_actividad_ejecutado'),
                        'Actividad ejecutada',
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
                'tblresposablecontratista', 'tblestacion', 'tblestadoactividad', 'tblcotizacion', 'tblordencompra', 'tblusuario'])
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
                $channel = 'user-'.(intval(Auth::user()->id_usuario) != intval($id_usuario)
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
            Log::info("Actividad: ".print_r($activity->comentario, 1));
            TblEstadoActividad::create([
                'id_actividad' => $activity->id_actividad,
                'estado' => $action,
                'comentario' => $activity->comentario,
                'id_usuareg' => Auth::id()
            ]);
            
        } catch (\Throwable $th) {
            Log::error("Error creando track de actividad: ".$th->getMessage());
        }
    }

    public function cotizacionesCliente(TblActividad $activity) {
        return view('partials._search', [
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
        return view('partials.seguimiento', [
            'model' => $activity,
            'route' => 'activities.handleActivity'
        ]);
    }
}
