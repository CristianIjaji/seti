<?php

namespace App\Http\Controllers;

use App\Exports\CotizacionExport;
use App\Http\Requests\SaveCotizacionRequest;
use App\Models\TblCotizacion;
use App\Models\TblCotizacionDetalle;
use App\Models\TblDominio;
use App\Models\TblEstadoCotizacion;
use App\Models\TblPuntosInteres;
use App\Models\TblTercero;
use App\Models\TblUsuario;
use Exception;
use Illuminate\Support\Facades\Auth;
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
            // $querybuilder->where('id_responsable_cliente', '=', Auth::user()->id_tercero);
            // $querybuilder->orwhere('id_usuareg', '=', Auth::user()->id_tercero);

            // $querybuilder->where('tbl_cotizaciones.estado', '=', session('id_dominio_cotizacion_creada'));
            // if(Auth::user()->role == session('id_dominio_coordinador')) {
                // }
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

            $total = 0;
            foreach (request()->id_tipo_item as $index => $valor) {
                $detalle = new TblCotizacionDetalle;
                $detalle->id_cotizacion = $cotizacion->id_cotizacion;
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

            $cotizacion->valor = $total;
            $cotizacion->save();

            $cotizacion->comentario = "Creación cotización # ".$cotizacion->id_cotizacion;

            $cotizacion->comentario = "Cotización creada # $cotizacion->id_cotizacion.";
            $this->createTrack($cotizacion, session('id_dominio_cotizacion_creada'));
            $this->sendNotification($cotizacion, 'user-'.$cotizacion->tblCliente->tblusuario->id_usuario, 'quote-created');

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
            'estados_cotizacion' => TblEstadoCotizacion::where(['id_cotizacion' => $quote->id_cotizacion])->orderBy('created_at', 'desc')->paginate(10),
            'carrito' => $this->getDetalleCotizacion($quote),
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
            : $quote->cliente
        );

        return view('cotizaciones._form', [
            'edit' => true,
            'cotizacion' => $quote,
            'estados_cotizacion' => TblEstadoCotizacion::where(['id_cotizacion' => $quote->id_cotizacion])->orderBy('created_at', 'desc')->paginate(10),
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
            'create_client' => isset(TblUsuario::getPermisosMenu('clients.index')->create) ? TblUsuario::getPermisosMenu('clients.index')->create : false,
            'create_site' => isset(TblUsuario::getPermisosMenu('sites.index')->create) ? TblUsuario::getPermisosMenu('sites.index')->create : false,
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

            if($estado !== session('id_dominio_cotizacion_creada')) {
                $quote->comentario = (
                    isset(request()->comentario) && trim(request()->comentario) != ''
                    ? request()->comentario
                    : "Cotización editada."
                );

                $this->createTrack($quote, session('id_dominio_cotizacion_creada'));

                $id_usuario = (isset($quote->tblContratista->tbluser->id_usuario)
                    ? $quote->tblContratista->tbluser->id_usuario
                    : TblCotizacion::find($quote->id_cotizacion)->id_usuareg
                );
                $channel = 'user-'.(intval(Auth::user()->id_usuario) != intval($id_usuario)
                    ? $id_usuario
                    : TblCotizacion::find($quote->id_cotizacion)->id_usuareg
                );

                $this->sendNotification($quote, $channel, 'quote-created');
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
                case 'aprove':
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
                        'quote-aprove'
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
                case 'send':
                    $quote->comentario = (
                        isset(request()->comentario) && trim(request()->comentario) != ''
                        ? request()->comentario
                        : "Cotización descargada."
                    );
                    $response = $this->updateQuote(
                        $quote,
                        [session('id_dominio_cotizacion_revisada')],
                        session('id_dominio_cotizacion_enviada'),
                        'Cotización descargada!',
                        'quote-aprove'
                    );
                    break;
                default:
                    # code...
                    break;
            }
            return $response;
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

    private function getDetalleCotizacion($quote) {
        $carrito = [];
        $items = TblCotizacionDetalle::with(['tblListaprecio'])->where(['id_cotizacion' => $quote->id_cotizacion])->get();

        foreach ($items as $item) {
            $carrito[$item->id_tipo_item]['update'] = false;
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
            'create' => Gate::allows('create', $cotizacion),
            'edit' => Gate::allows('update', $cotizacion),
            'view' => Gate::allows('view', $cotizacion),
            'request' => $this->filtros,
        ]);
    }
    
    private function updateQuote($quote, $estados, $nuevoEstado, $msg, $notification, $usuarioFinal = '') {
        try {
            if(in_array($quote->estado, $estados)) {
                $quote->estado = $nuevoEstado;
                
                $this->createTrack($quote, $nuevoEstado);
                unset($quote->comentario);
                $quote->save();
            }

            $id_usuario = (isset($quote->tblContratista->tbluser->id_usuario)
                ? $quote->tblContratista->tbluser->id_usuario
                : TblCotizacion::find($quote->id_cotizacion)->id_usuareg
            );

            if($notification !== '') {
                $channel = 'user-'.(intval(Auth::user()->id_usuario) != intval($id_usuario)
                    ? $id_usuario
                    : TblCotizacion::find($quote->id_cotizacion)->id_usuareg
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
        $cotizacion = TblCotizacion::with(['tblEstacion'])->where(['id_cotizacion' => request()->quote])->get();
        return $this->excel->download(new CotizacionExport($cotizacion), "Cotizacion.xlsx");
    }
}
