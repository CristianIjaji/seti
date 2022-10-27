<?php

namespace App\Http\Controllers;

use App\Exports\CotizacionExport;
use App\Exports\ReportsExport;
use App\Http\Requests\SaveCotizacionRequest;
use App\Imports\DataImport;
use App\Models\TblActividad;
use App\Models\TblCotizacion;
use App\Models\TblCotizacionDetalle;
use App\Models\TblDominio;
use App\Models\TblEstadoCotizacion;
use App\Models\TblPuntosInteres;
use App\Models\TblTercero;
use App\Models\TblUsuario;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Excel;
use Pusher\Pusher;

class CotizacionController extends Controller
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

                if(!in_array($key, ['full_name', 'nombre'])){
                    $querybuilder->where($key, (count($operador) > 1 ? $operador[0] : 'like'), (count($operador) > 1 ? $operador[1] : strtolower("%$value%")));
                } else if($key == 'full_name' && $value) {
                    $querybuilder->whereHas('tblCliente', function($q) use($value) {
                        $q->where('nombres', 'like', strtolower("%$value%"));
                        $q->orwhere('apellidos', 'like', strtolower("%$value%"));
                    });
                } else if($key == 'nombre' && $value) {
                    $querybuilder->whereHas('tblEstacion', function($q) use($value) {
                        $q->where('nombre', 'like', strtolower("%$value%"));
                    });
                }
            }
            $this->filtros[$key] = $value;
        }

        if(!in_array(Auth::user()->role, [session('id_dominio_super_administrador'), session('id_dominio_administrador')])) {
            if(Auth::user()->role == session('id_dominio_analista')) {
                $querybuilder->where('tbl_cotizaciones.id_usuareg', '=', Auth::user()->id_usuario);
            }
            if(Auth::user()->role == session('id_dominio_coordinador')) {
                $querybuilder->where([
                    'tbl_cotizaciones.id_responsable_cliente' => Auth::user()->id_tercero,
                    'tbl_cotizaciones.estado' => session('id_dominio_cotizacion_creada'),
                ]);
                $querybuilder->orwhere([
                    'tbl_cotizaciones.id_usuareg' => Auth::user()->id_usuario
                ]);
            }
        }

        return $querybuilder;
    }

    private function sendNotification($cotizacion, $channel, $event) {
        $cotizacion->descripcion = $cotizacion->tblEstacion->nombre."\nFecha solicitud: ".$cotizacion->fecha_solicitud."\nAlcance: ".$cotizacion->descripcion;

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
            $cotizacion
        );
    }

    private function getDetailQuote($quote) {
        TblCotizacionDetalle::where('id_cotizacion', '=', $quote->id_cotizacion)->wherenotin('id_lista_precio', request()->id_lista_precio)->delete();

        $total = 0;
        foreach (request()->id_tipo_item as $index => $valor) {
            $detalle = TblCotizacionDetalle::where(['id_cotizacion' => $quote->id_cotizacion, 'id_lista_precio' => request()->id_lista_precio[$index]])->first();
            if(!$detalle) {
                $detalle = new TblCotizacionDetalle;
            }

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

        $iva = intval(str_replace(['iva', ' ', '%'], ['', '', ''], mb_strtolower($quote->tblIva->nombre))) / 100;
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
        $this->authorize('view', new TblCotizacion);

        return $this->getView('cotizaciones.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', new TblCotizacion);

        return view('cotizaciones._form', [
            'cotizacion' => new TblCotizacion,
            'carrito' => [],
            'clientes' => TblTercero::where([
                'estado' => 1,
                'id_dominio_tipo_tercero' => session('id_dominio_representante_cliente')
            ])->where('id_responsable_cliente', '>', 0)->get(),
            'tipos_trabajo' => TblDominio::getListaDominios(session('id_dominio_tipos_trabajo')),
            'prioridades' => TblDominio::getListaDominios(session('id_dominio_tipos_prioridad')),
            'impuestos' => TblDominio::getListaDominios(session('id_dominio_impuestos')),
            'contratistas' => TblTercero::where([
                'estado' => 1,
                'id_dominio_tipo_tercero' => session('id_dominio_coordinador')
            ])->where('id_responsable_cliente', '>', 0)->get(),
            'create_client' => isset(TblUsuario::getPermisosMenu('clients.index')->create) ? TblUsuario::getPermisosMenu('clients.index')->create : false,
            'create_site' => isset(TblUsuario::getPermisosMenu('sites.index')->create) ? TblUsuario::getPermisosMenu('sites.index')->create : false,
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
            $this->authorize('create', $cotizacion);

            $cotizacion->valor = $this->getDetailQuote($cotizacion);
            $cotizacion->save();

            $cotizacion->comentario = "Cotización creada # $cotizacion->id_cotizacion.";

            $this->createTrack($cotizacion, session('id_dominio_cotizacion_creada'));
            if(isset($cotizacion->tblContratista->tbluser)) {
                $this->sendNotification($cotizacion, 'user-'.$cotizacion->tblContratista->tbluser->id_usuario, 'quote-created');
            }

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
        $this->authorize('view', $quote);

        return view('cotizaciones._form', [
            'edit' => false,
            'cotizacion' => $quote,
            'estados_cotizacion' => TblEstadoCotizacion::where(['id_cotizacion' => $quote->id_cotizacion])->orderBy('id_estado_cotizacion', 'desc')->paginate(9999999999),
            'carrito' => $this->getDetalleCotizacion($quote),
            'actividad' => TblActividad::where(['id_cotizacion' => $quote->id_cotizacion])->first(),
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
        $this->authorize('update', $quote);

        $id_cliente = (isset($quote->tblCliente->id_responsable_cliente)
            ? $quote->tblCliente->id_responsable_cliente
            : $quote->id_cliente
        );

        return view('cotizaciones._form', [
            'edit' => true,
            'cotizacion' => $quote,
            'estados_cotizacion' => TblEstadoCotizacion::where(['id_cotizacion' => $quote->id_cotizacion])->orderBy('id_estado_cotizacion', 'desc')->paginate(9999999999),
            'carrito' => $this->getDetalleCotizacion($quote),
            'clientes' => TblTercero::where([
                'estado' => 1,
                'id_dominio_tipo_tercero' => session('id_dominio_representante_cliente')
            ])->where('id_responsable_cliente', '>', 0)->get(),
            'estaciones' => TblPuntosInteres::where(['estado' => 1, 'id_cliente' => $id_cliente])->pluck('nombre', 'id_punto_interes'),
            'tipos_trabajo' => TblDominio::getListaDominios(session('id_dominio_tipos_trabajo')),
            'prioridades' => TblDominio::getListaDominios(session('id_dominio_tipos_prioridad')),
            'impuestos' => TblDominio::getListaDominios(session('id_dominio_impuestos')),
            'contratistas' => TblTercero::where([
                'estado' => 1,
                'id_dominio_tipo_tercero' => session('id_dominio_coordinador')
            ])->where('id_responsable_cliente', '>', 0)->get(),
            'subsistemas' => TblDominio::getListaDominios(session('id_dominio_subsistemas')),
            'create_client' => isset(TblUsuario::getPermisosMenu('clients.index')->create) ? TblUsuario::getPermisosMenu('clients.index')->create : false,
            'create_site' => isset(TblUsuario::getPermisosMenu('sites.index')->create) ? TblUsuario::getPermisosMenu('sites.index')->create : false,
            'estados_actividad' => TblDominio::wherein('id_dominio', [session('id_dominio_actividad_programado'), session('id_dominio_actividad_comprando')])->get(),
            'actividad' => TblActividad::where(['id_cotizacion' => $quote->id_cotizacion])->first(),
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
            $this->authorize('update', $quote);

            $estado = $quote->estado;
            $quote->update($request->validated());

            $quote->valor = $this->getDetailQuote($quote);
            $quote->save();

            if($estado !== session('id_dominio_cotizacion_creada')) {
                $quote->comentario = (
                    isset(request()->comentario) && trim(request()->comentario) != ''
                    ? request()->comentario
                    : "Cotización editada."
                );

                $this->createTrack($quote, session('id_dominio_cotizacion_creada'));

                if(isset($quote->tblContratista->tbluser)) {
                    $this->sendNotification($quote, 'user-'.$quote->tblContratista->tbluser->id_usuario, 'quote-created');
                }
            }

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

    public function handleQuote(TblCotizacion $quote) {
        try {
            $response = '';
            switch (request()->action) {
                case 'check':
                    $quote->comentario = (
                        isset(request()->comentario) && trim(request()->comentario) != ''
                        ? request()->comentario
                        : "Cotización aprobada."
                    );
                    $response = $this->updateQuote(
                        $quote,
                        [session('id_dominio_cotizacion_creada')],
                        session('id_dominio_cotizacion_revisada'),
                        'Cotización aprobada!',
                        'quote-check'
                    );
                    break;
                case 'deny':
                    $quote->comentario = (
                        isset(request()->comentario) && trim(request()->comentario) != ''
                        ? request()->comentario
                        : "Cotización devuelta."
                    );
                    $response = $this->updateQuote(
                        $quote,
                        [session('id_dominio_cotizacion_creada')],
                        session('id_dominio_cotizacion_devuelta'),
                        'Cotización devuelta!',
                        'quote-deny'
                    );
                    break;
                case 'wait':
                    $quote->comentario = (
                        isset(request()->comentario) && trim(request()->comentario) != ''
                        ? request()->comentario
                        : "Cotización enviada al cliente."
                    );
                    $quote->fecha_envio = Carbon::now();
                    $response = $this->updateQuote(
                        $quote,
                        [session('id_dominio_cotizacion_revisada')],
                        session('id_dominio_cotizacion_pendiente_aprobacion'),
                        'Cotización pendiente por aprobación!',
                        'quote-wait'
                    );
                    break;
                case 'aprove':
                    $quote->comentario = (
                        isset(request()->comentario) && trim(request()->comentario) != ''
                        ? request()->comentario
                        : "Cotización aprobada por el cliente."
                    );
                    $response = $this->updateQuote(
                        $quote,
                        [session('id_dominio_cotizacion_pendiente_aprobacion')],
                        session('id_dominio_cotizacion_aprobada'),
                        'Cotización aprobada cliente!',
                        'quote-aprove'
                    );
                    break;
                case 'reject':
                    $quote->comentario = (
                        isset(request()->comentario) && trim(request()->comentario) != ''
                        ? request()->comentario
                        : 'Cotización rechazada por el cliente.'
                    );
                    $response = $this->updateQuote(
                        $quote,
                        [session('id_dominio_cotizacion_pendiente_aprobacion')],
                        session('id_dominio_cotizacion_rechazada'),
                        'Cotización rechazada cliente!',
                        'quote-reject'
                    );
                    break;
                case 'cancel':
                    $quote->comentario = (
                        isset(request()->comentario) && trim(request()->comentario) != ''
                        ? request()->comentario
                        : 'Cotización cancelada.'
                    );
                    $response = $this->updateQuote(
                        $quote,
                        [
                            session('id_dominio_cotizacion_creada'), session('id_dominio_cotizacion_devuelta'), session('id_dominio_cotizacion_revisada'),
                            session('id_dominio_cotizacion_enviada'), session('id_dominio_cotizacion_pendiente_aprobacion'), session('id_dominio_cotizacion_rechazada'),
                            session('id_dominio_cotizacion_aprobada')
                        ],
                        session('id_dominio_cotizacion_cancelada'),
                        'Cotización cancelada!',
                        'quote-cancel'
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
                'success' => 'Pruebas'//$response['success']
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ]);
        }
    }

    private function getDetalleCotizacion($quote) {
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

        return $carrito;
    }

    public function grid() {
        return $this->getView('cotizaciones.grid');
    }

    private function getView($view) {
        $cotizacion = new TblCotizacion;

        return view($view, [
            'model' => TblCotizacion::with(['tblCliente', 'tblEstacion', 'tblTipoTrabajo', 'tblPrioridad', 'tblContratista'])
                ->where(function ($q) {
                    $this->dinamyFilters($q);
                })->orderBy('id_cotizacion', 'desc')->paginate(10),
            'clientes' => TblTercero::getClientesTipo(session('id_dominio_representante_cliente')),
            'estaciones' => TblPuntosInteres::where('estado', '=', 1)->pluck('nombre', 'id_punto_interes'),
            'prioridades' => TblDominio::getListaDominios(session('id_dominio_tipos_prioridad')),
            'procesos' => TblDominio::getListaDominios(session('id_dominio_tipos_proceso')),
            'contratistas' => TblTercero::getClientesTipo(session('id_dominio_coordinador')),
            'status' => $cotizacion->status,
            'export' => Gate::allows('export', $cotizacion),
            'import' => Gate::allows('import', $cotizacion),
            'create' => Gate::allows('create', $cotizacion),
            'edit' => Gate::allows('update', $cotizacion),
            'view' => Gate::allows('view', $cotizacion),
            'request' => $this->filtros,
        ]);
    }

    private function generateDownload($option) {
        return TblCotizacion::select(
            DB::raw("
                tbl_cotizaciones.id_cotizacion,
                tbl_cotizaciones.ot_trabajo,
                COALESCE(tc.razon_social, COALESCE(
                    CONCAT(tc.nombres, ' ', tc.apellidos),
                        COALESCE(t.razon_social,
                            CONCAT(t.nombres, ' ', t.apellidos)
                        )
                    )
                ) as full_name,
                pi.nombre as estacion,
                tbl_cotizaciones.descripcion,
                tbl_cotizaciones.fecha_solicitud,
                tbl_cotizaciones.fecha_envio,
                tt.nombre as tipo_trabajo,
                p.nombre as prioridad,
                e.nombre as estado,
                CONCAT(tpr.nombres, ' ', tpr.apellidos) as proveedor,
                iva.descripcion as iva,
                tbl_cotizaciones.valor
            ")
        )
        ->join('tbl_terceros as t', 'tbl_cotizaciones.id_cliente', '=', 't.id_tercero')
        ->leftjoin('tbl_terceros as tc', 't.id_responsable_cliente', '=', 'tc.id_tercero')
        ->join('tbl_puntos_interes as pi', 'tbl_cotizaciones.id_estacion', '=', 'pi.id_punto_interes')
        ->join('tbl_dominios as tt', 'tbl_cotizaciones.id_tipo_trabajo', '=', 'tt.id_dominio')
        ->join('tbl_dominios as p', 'tbl_cotizaciones.id_prioridad', '=', 'p.id_dominio')
        ->join('tbl_dominios as e', 'tbl_cotizaciones.estado', '=', 'e.id_dominio')
        ->join('tbl_dominios as iva', 'tbl_cotizaciones.iva', '=', 'iva.id_dominio')
        ->join('tbl_terceros as tpr', 'tbl_cotizaciones.id_responsable_cliente', '=', 'tpr.id_tercero')

        ->where(function($q) use($option) {
            if($option == 1) {
                $this->dinamyFilters($q, [
                    'tbl_cotizaciones.id_cliente' => 'id_cliente',
                    'tbl_cotizaciones.estado' => 'estado',
                    'tbl_cotizaciones.id_responsable_cliente' => 'id_responsable_cliente'
                ]);
            } else {
                $q->where('tbl_cotizaciones.estado', '=', '-1');
            }
        })
        ->get();
    }

    public function export() {
        ob_end_clean();
        ob_start();
        $headers = ['#', 'OT', 'Proveedor', 'Estación', 'Descripción Orden', 'Fecha Solicitud', 'Fecha Envio',
            'Tipo Trabajo', 'Prioridad', 'Estado', 'Encargado', 'IVA', 'Valor', 
        ];

        return $this->excel->download(new ReportsExport($headers, $this->generateDownload(1)), 'Reporte cotizaciones.xlsx');
    }

    public function download_template() {
        $headers = ['OT', 'Proveedor', 'Estación', 'Descripción Orden', 'Fecha Solicitud', 'Fecha Envio',
            'Tipo Trabajo', 'Prioridad', 'Estado', 'Encargado', 'IVA', 'Valor'
        ];
        return $this->excel->download(new ReportsExport($headers, $this->generateDownload(2)), 'Template cotizaciones.xlsx');
    }

    public function import() {
        (new DataImport(new TblCotizacion))->import(request()->file('input_file'));
        return back();
    }
    
    private function updateQuote($quote, $estados, $nuevoEstado, $msg, $notification, $usuarioFinal = '') {
        try {
            if(in_array($quote->estado, $estados)) {
                $quote->estado = $nuevoEstado;
                
                $this->createTrack($quote, $nuevoEstado);
                unset($quote->comentario);
                $quote->save();
            }

            $id_usuario = TblCotizacion::find($quote->id_cotizacion)->id_usuareg;

            if($notification !== '') {
                $channel = 'user-'.(intval(Auth::user()->id_usuario) != intval($id_usuario)
                    ? $id_usuario
                    : (isset($quote->tblContratista->tbluser->id_usuario) ? $quote->tblContratista->tbluser->id_usuario : null)
                );

                $this->sendNotification($quote, $channel, $notification);
            }

            return ['success' => $msg];
        } catch (\Throwable $th) {
            Log::error($th->__toString());
            return ['error' => $th->getMessage()];
        }
    }

    private function createTrack($quote, $action) {
        try {
            TblEstadoCotizacion::create([
                'id_cotizacion' => $quote->id_cotizacion,
                'estado' => $action,
                'comentario' => $quote->comentario,
                'id_usuareg' => Auth::id()
            ]);
        } catch (\Throwable $th) {
            Log::error("Error creando track cotización: ".$th->getMessage());
        }
    }

    public function exportQuote() {
        $cotizacion = TblCotizacion::with(['tblEstacion'])->where('id_cotizacion', '=', request()->quote)->first();
        return $this->excel->download(new CotizacionExport($cotizacion), "Cotizacion ".$cotizacion->tblEstacion->nombre.".xlsx");
    }

    public function getCotizacion(TblCotizacion $quote) {
        $quote->estacion = $quote->tblEstacion->nombre;
        return $quote;
    }

    public function seguimiento(TblCotizacion $quote) {
        return view('cotizaciones.seguimiento', [
            'cotizacion' => $quote
        ]);
    }
}
