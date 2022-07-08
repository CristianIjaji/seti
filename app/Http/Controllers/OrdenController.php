<?php

namespace App\Http\Controllers;

use App\Exports\ReportsExport;
use App\Http\Requests\SaveOrdenRequest;
use App\Models\TblDominio;
use App\Models\TblOrden;
use App\Models\TblOrdenTrack;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Pusher\Pusher;
use Maatwebsite\Excel\Excel;

class OrdenController extends Controller
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

                if(!in_array($key, ['razon_social', 'full_name'])){
                    $querybuilder->where($key, (count($operador) > 1 ? $operador[0] : 'like'), (count($operador) > 1 ? $operador[1] : strtolower("%$value%")));
                } else if($key == 'razon_social') {
                    $querybuilder->whereHas('tbltercero', function($q) use($value){
                        $q->where('razon_social', 'like', strtolower("%$value%"));
                    });
                } else if($key == 'full_name') {
                    $querybuilder->whereHas('tblusuario.tbltercero', function($q) use($value){
                        $q->where('nombres', 'like', strtolower("%$value%"));
                        $q->orwhere('apellidos', 'like', strtolower("%$value%"));
                    });
                }
            }
            $this->filtros[$key] = $value;
        }

        if(in_array(Auth::user()->role, [session('id_dominio_asociado')])){
            $querybuilder->wherenotin('tbl_ordenes.estado', [
                session('id_dominio_orden_rechazada'),
                session('id_dominio_orden_entregada'),
                session('id_dominio_orden_completada')
            ]);

            $usuario = Auth::user()->id_usuario;
            $tercero = Auth::user()->tbltercero->id_tercero;
            $querybuilder->where('id_tercero_cliente', $tercero);

            $querybuilder->whereRaw("CASE WHEN tbl_ordenes.estado = ".session('id_dominio_orden_devuelta')." THEN id_usuario_final != ".$usuario." ELSE 1 = 1 END");
        }

        if(in_array(Auth::user()->role, [session('id_dominio_agente')])) {
            $usuario = Auth::user()->id_usuario;
            $querybuilder->where('id_usuareg', $usuario);
            $querybuilder->wherenotin('tbl_ordenes.estado', [session('id_dominio_orden_entregada'), session('id_dominio_orden_completada')]);
            $querybuilder->whereRaw("CASE WHEN tbl_ordenes.estado = ".session('id_dominio_orden_devuelta')." THEN id_usuario_final != ".$usuario." ELSE 1 = 1 END");
        }

        return $querybuilder;
    }

    private function sendNotification($orden, $channel, $event) {
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
            $orden
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view', new TblOrden);

        return $this->getView('orden.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', new TblOrden);

        return view('orden._form', [
            'orden' => new TblOrden,
            'clientes' => DB::table('tbl_terceros', 't')
                ->join('tbl_dominios as doc', 't.id_dominio_tipo_documento', '=', 'doc.id_dominio')
                ->select('t.id_tercero',
                    DB::raw("CONCAT(t.razon_social, ' Teléfono: ', t.telefono, ' Dirección: ', t.direccion, ' - ', t.ciudad) as nombre,
                        t.id_dominio_tipo_tercero = ".session('id_dominio_asociado')." as cliente"
                    )
                )->where('t.id_dominio_tipo_tercero', '=', session('id_dominio_asociado'))->get(),
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
            $orden = TblOrden::create($request->validated());
            $this->authorize('create', $orden);

            $this->createTrack($orden, session('id_dominio_orden_cola'));
            $this->sendNotification($orden, 'user-'.$orden->tbltercero->tbluser->id_usuario, 'orden-created');

            return response()->json([
                'success' => 'Orden creada exitosamente!',
            ]);
        } catch (\Exception $e) {
            Log::error("Error creando la orden: ".$e->getMessage());

            return response()->json([
                'errors' => $e
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($orden)
    {
        $orden = TblOrden::withTrashed()->findOrFail($orden);
        $this->authorize('view', $orden);

        return view('orden._view', [
            'edit' => false,
            'orden' => $orden,
            'estados' => $this->getList(session('id_dominio_estados_orden')),
            'tiempos' => TblDominio::where(['estado' => 1, 'id_dominio_padre' => session('id_dominio_tiempos_domicilio')])->get(),
            'steps' => $orden->flujoOrden(),
            'impresora' => (isset(Auth::user()->tbltercero->tblconfiguracion->impresora)
                ? Auth::user()->tbltercero->tblconfiguracion->impresora
                : ''
            ),
            'recibo' => $this->getRecibo($orden),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(TblOrden $orden)
    {
        $this->authorize('update', $orden);

        return view('orden._form', [
            'edit' => true,
            'orden' => $orden,
            'clientes' => DB::table('tbl_terceros', 't')
                ->join('tbl_dominios as doc', 't.id_dominio_tipo_documento', '=', 'doc.id_dominio')
                ->select('t.id_tercero',
                    DB::raw("CONCAT(t.razon_social, ' Teléfono: ', t.telefono, ' Dirección: ', t.direccion, ' - ', t.ciudad) as nombre,
                        t.id_dominio_tipo_tercero = ".session('id_dominio_asociado')." as cliente"
                    )
                )->where('t.id_dominio_tipo_tercero', '=', session('id_dominio_asociado'))->get(),
            'tipos_orden' => TblDominio::where(['estado' => 1, 'id_dominio_padre' => session('id_dominio_tipo_orden')])->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TblOrden $orden, SaveOrdenRequest $request)
    {
        try {
            $this->authorize('update', $orden);
            $orden->update($request->validated());

            return response()->json([
                'success' => 'Orden actualizada correctamente!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ]);
        }
    }

    public function gestionarOrden(TblOrden $orden) {
        try {
            $response = '';
            switch (request()->accion) {
                case 'accepted':
                    if(isset(request()->pedir_domiciliario)) {
                        $this->updateOrden(
                            $orden,
                            [session('id_dominio_orden_cola')],
                            (session('id_dominio_orden_aceptada')),
                            '',
                            ''
                        );
                    }

                    $response = $this->updateOrden(
                        $orden,
                        [session('id_dominio_orden_cola'), session('id_dominio_orden_aceptada')],
                        (isset(request()->pedir_domiciliario) ? session('id_dominio_orden_aceptada_domiciliario') : session('id_dominio_orden_aceptada')),
                        'Orden aceptada correctamente!',
                        'orden-acepted'
                    );

                    if(isset(request()->pedir_domiciliario)) {
                        $orden->pedir_domiciliario = 1;
                        $orden->id_dominio_tiempo_llegada = request()->id_dominio_tiempo_llegada;
                        $orden->save();
                    }

                    break;
                case 'rejected':
                    $response = $this->updateOrden(
                        $orden,
                        [session('id_dominio_orden_cola')],
                        session('id_dominio_orden_rechazada'),
                        'Orden rechazada correctamente!',
                        'orden-rejected',
                        Auth::user()->id_usuario
                    );

                    break;
                case 'domiciliary':
                    if(in_array($orden->estado, [session('id_dominio_orden_aceptada')])) {
                        $orden->pedir_domiciliario = 1;
                        $orden->id_dominio_tiempo_llegada = request()->id_dominio_tiempo_llegada;
                        $orden->save();
                    }

                    $response = $this->updateOrden(
                        $orden,
                        [session('id_dominio_orden_aceptada')],
                        session('id_dominio_orden_aceptada_domiciliario'),
                        'Se solicito un domiciliario correctamente',
                        'orden-domiciliary'
                    );
                    break;
                case 'send':
                    $response = $this->updateOrden(
                        $orden,
                        [session('id_dominio_orden_aceptada'), session('id_dominio_orden_aceptada_domiciliario')],
                        session('id_dominio_orden_camino'),
                        'Orden enviada al cliente correctamente!',
                        'orden-sended'
                    );
                    break;
                case 'deny':
                    $response = $this->updateOrden(
                        $orden,
                        [
                            session('id_dominio_orden_cola'),
                            session('id_dominio_orden_aceptada'),
                            session('id_dominio_orden_aceptada_domiciliario'),
                            session('id_dominio_orden_camino'),
                        ],
                        session('id_dominio_orden_devuelta'),
                        'Orden rechazada por cliente correctamente!',
                        'orden-denyed',
                        Auth::user()->id_usuario
                    );
                    break;
                case 'deliver':
                    $response = $this->updateOrden(
                        $orden,
                        [session('id_dominio_orden_aceptada_domiciliario'), session('id_dominio_orden_camino')],
                        session('id_dominio_orden_entregada'),
                        'Orden entregada correctamente!',
                        'orden-delivered',
                        Auth::user()->id_usuario
                    );
                    break;
                case 'complete':
                    $response = $this->updateOrden(
                        $orden,
                        [session('id_dominio_orden_aceptada')],
                        session('id_dominio_orden_completada'),
                        'Orden completada correctamente!',
                        'orden-complete',
                        Auth::user()->id_usuario
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

    private function updateOrden($orden, $estados, $nuevoEstado, $msg, $notification, $usuarioFinal = '') {
        try {
            if(in_array($orden->estado, $estados)) {
                $orden->estado = $nuevoEstado;
                
                if($usuarioFinal !== '') {
                    $orden->id_usuario_final = $usuarioFinal;
                    if(!request()->fecha_fin) {
                        $orden->fecha_fin = date('Y-m-d H:i:s');
                    }
                }

                $this->createTrack($orden, $nuevoEstado);
    
                $orden->save();
            } else {
                throw new Exception('La orden no cumple el estado');
            }
    
            if($notification !== '') {
                $channel = 'user-'.(Auth::user()->id_usuario == $orden->id_usuareg
                    ? $orden->tbltercero->tbluser->id_usuario
                    : $orden->id_usuareg
                );

                $this->sendNotification($orden, $channel, $notification);
            }
    
            return [
                'success' => $msg
            ];
        } catch (\Throwable $th) {
            return [
                'error' => $th->getMessage()
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(TblOrden $orden)
    {
        $this->authorize('delete', $orden);
        $orden->delete();

        return response()->json([
            'success' => 'Orden eliminada correctamente!'
        ]);
    }

    public function grid(){
        return $this->getView('orden.grid');
    }

    public function export() {
        $track = (env('DB_CONNECTION') == 'mysql'
            ? "GROUP_CONCAT(CONCAT(tr.created_at, ': ', d.nombre) ORDER BY tr.created_at SEPARATOR '\r')"
            : "string_agg(CONCAT(tr.created_at, ': ', d.nombre), '\r\n')"
        );

        $ordenes = TblOrden::withTrashed()->select(
            DB::raw("
                tbl_ordenes.id_orden,
                tbl_ordenes.created_at as fecha,
                t.razon_social as asociado,
                to.nombre as tipo_orden,
                tbl_ordenes.datos_cliente,
                tbl_ordenes.fecha_inicio as inicio,
                tbl_ordenes.fecha_fin as fin,
                tbl_ordenes.valor as precio,
                tbl_ordenes.metodo_pago,
                tbl_ordenes.descripcion,
                e.nombre as estado,
                $track AS track,
                CONCAT(tc.nombres, ' ', tc.apellidos) as usuario
            ")
        )
        ->join('tbl_terceros as t', 'tbl_ordenes.id_tercero_cliente', '=', 't.id_tercero')
        ->join('tbl_orden_track as tr', 'tbl_ordenes.id_orden', '=', 'tr.id_orden')
        ->join('tbl_dominios as d', 'tr.id_dominio_accion', '=', 'd.id_dominio')
        ->join('tbl_dominios as e', 'tbl_ordenes.estado', '=', 'e.id_dominio')
        ->join('tbl_usuarios as u', 'tbl_ordenes.id_usuareg', '=', 'u.id_usuario')
        ->join('tbl_terceros as tc', 'u.id_tercero', '=', 'tc.id_tercero')
        ->join('tbl_dominios as to', 'tbl_ordenes.id_dominio_tipo_orden', '=', 'to.id_dominio')
        ->where(function ($q) {
            $this->dinamyFilters($q, [
                'tbl_ordenes.id_orden' => 'id_orden',
                'tbl_ordenes.created_at' => 'created_at',
                't.razon_social' => 'razon_social',
                'tbl_ordenes.datos_cliente' => 'datos_cliente',
                'tbl_ordenes.valor' => 'valor',
                'tbl_ordenes.descripcion' => 'descripcion',
                'e.nombre' => 'nombre',
                "CONCAT(tc.nombres, ' ', tc.apellidos)" => 'usuario',
                'tbl_ordenes.estado' => 'estado'
            ]);
        })
        ->groupBy('tbl_ordenes.id_orden', 'tbl_ordenes.created_at', 't.razon_social', 'to.nombre',
            'tbl_ordenes.datos_cliente', 'tbl_ordenes.fecha_inicio', 'tbl_ordenes.fecha_fin',
            'tbl_ordenes.valor', 'e.nombre', 'tbl_ordenes.metodo_pago',
            'tbl_ordenes.descripcion', 'tc.nombres', 'tc.apellidos')
        ->get();

        $headers = ['#', 'Fecha', 'Asociado', 'Tipo Orden', 'Datos cliente', 'Fecha Inicio', 'Fecha Fin', 'Valor', 'Metodo Pago', 'Pedido', 'Estado', 'Seguimiento orden', 'Usuario'];
        return $this->excel->download(new ReportsExport($headers, $ordenes), "Reporte Ordenes ".date('Ymdhms').".xlsx");
    }

    private function getView($view) {
        $orden = new TblOrden;
        return view($view, [
            'model' => (
                in_array(Auth::user()->role, [session('id_dominio_super_administrador'), session('id_dominio_administrador')])
                ?  TblOrden::withTrashed()->where(function ($q) {
                    $this->dinamyFilters($q);
                })->latest()->paginate(1000)
                : TblOrden::where(function ($q) {
                    $this->dinamyFilters($q);
                })->latest()->paginate(1000)
            ),
            'create' => Gate::allows('create', $orden),
            'edit' => Gate::allows('create', $orden),
            'view' => Gate::allows('view', $orden),
            'export' => Gate::allows('export', $orden),
            'request' => $this->filtros,
            'status' => $orden->status,
            'estados' => $this->getList(session('id_dominio_estados_orden')),
            'tipo_ordenes' => $this->getList(session('id_dominio_tipo_orden')),
        ]);
    }

    private function getList($id_dominio) {
        $list = [];
        foreach (TblDominio::where(['estado' => 1, 'id_dominio_padre' => $id_dominio])->get() as $dominio) {
            $list[$dominio->id_dominio] = $dominio->nombre;
        }

        return $list;
    }

    private function getRecibo($orden) {
        $tercero = $orden->tbltercero;
        $configuracion = $orden->tbltercero->tblconfiguracion;

        $recibo = isset($configuracion->tbldominiorecibo->descripcion)
            ? str_replace(["\r\n", "\r", "\n"], '', $configuracion->tbldominiorecibo->descripcion)
            : '';

        $empresa = isset($orden->tbltercero->tbluser->usuario) ? strtoupper($orden->tbltercero->tbluser->usuario) : '';
        $direccion_ = $tercero->direccion;
        $telefono_ = $tercero->telefono;
        $correo_ = $tercero->correo;
        $fecha = date('d/m/Y', strtotime($orden->created_at));
        $hora = date('H:i', strtotime($orden->created_at));
        $line = str_pad('=', 42, '=');

        $orden->descripcion = str_replace(["\r\n", "\r", "\n", "<br>"], '<br>', $orden->descripcion);
        $items = explode("<br>", $orden->descripcion);
        $nombre = $orden->datos_cliente_form[0];
        $direccion = $orden->datos_cliente_form[1];
        $telefono = $orden->datos_cliente_form[2];

        $descripcion = '';
        foreach ($items as $item) {
            if(trim($item) !== '') {
                $descripcion .= '{"text": "'.$item.'"},';
            }
        }
        
        $descripcion = trim($descripcion, ',');

        $recibo = str_replace([
            '$empresa', '$direccion_', '$telefono_', '$correo_', '$fecha', '$hora', '$line', '$descripcion', '$total', '$nombre', '$direccion', '$telefono'
        ], [
            $empresa, $direccion_, $telefono_, $correo_, $fecha, $hora, $line, $descripcion, $orden->valor, $nombre, $direccion, $telefono
        ], $recibo);

        return $recibo;
    }

    private function createTrack($orden, $accion) {
        try {
            TblOrdenTrack::create([
                'id_orden' => $orden->id_orden,
                'id_dominio_accion' => $accion,
                'id_usuareg' => Auth::user()->id_usuario
            ]);
        } catch (\Throwable $th) {
            Log::error("Error creando track orden: ".$th->getMessage());
        }
    }

    
}
