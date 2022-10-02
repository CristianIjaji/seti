<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveActividadRequest;
use App\Models\TblActividad;
use App\Models\TblCotizacion;
use App\Models\TblDominio;
use App\Models\TblPuntosInteres;
use App\Models\TblTercero;
use App\Models\TblUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

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

        $id_cliente = 0;
        if(isset(request()->cotizacion)) {
            $quote = TblCotizacion::find(request()->cotizacion);
            $id_cliente = (isset($quote->tblCliente->id_responsable_cliente)
                ? $quote->tblCliente->id_responsable_cliente
                : $quote->id_cliente
            );
        }

        return view('actividades._form', [
            'activity' => new TblActividad,
            'create_client' => isset(TblUsuario::getPermisosMenu('clients.index')->create) ? TblUsuario::getPermisosMenu('clients.index')->create : false,
            'tipos_trabajo' => TblDominio::getListaDominios(session('id_dominio_tipos_trabajo'), 'nombre'),
            'create_site' => isset(TblUsuario::getPermisosMenu('sites.index')->create) ? TblUsuario::getPermisosMenu('sites.index')->create : false,
            'contratistas' => TblTercero::where([
                'estado' => 1,
                'id_dominio_tipo_tercero' => session('id_dominio_coordinador')
            ])->where('id_responsable_cliente', '>', 0)->get(),
            'clientes' => TblTercero::where([
                'estado' => 1,
                'id_dominio_tipo_tercero' => session('id_dominio_representante_cliente')
            ])->where('id_responsable_cliente', '>', 0)->get(),
            'estaciones' => TblPuntosInteres::where(['estado' => 1, 'id_cliente' => $id_cliente])->pluck('nombre', 'id_punto_interes'),
            'prioridades' => TblDominio::getListaDominios(session('id_dominio_tipos_prioridad')),
            'subsistemas' => TblDominio::getListaDominios(session('id_dominio_subsistemas'), 'nombre'),
            'estados' => TblDominio::wherein('id_dominio', [session('id_dominio_actividad_programado'), session('id_dominio_actividad_comprando')])->get(),
            'quote' => isset(request()->cotizacion) ? TblCotizacion::find(request()->cotizacion)->first() : [],
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

            $this->createTrak($actividad, session(''));
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
            'activity' => $activity
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

        $id_cliente = (isset($activity->tblencargadocliente->id_responsable_cliente)
            ? $activity->tblencargadocliente->id_responsable_cliente
            : $activity->id_encargado_cliente
        );

        return view('actividades._form', [
            'edit' => true,
            'activity' => $activity,
            'clientes' => TblTercero::where([
                'estado' => 1,
                'id_dominio_tipo_tercero' => session('id_dominio_representante_cliente')
            ])->where('id_responsable_cliente', '>', 0)->get(),
            'estaciones' => TblPuntosInteres::where(['estado' => 1, 'id_cliente' => $id_cliente])->pluck('nombre', 'id_punto_interes'),
            'tipos_trabajo' => TblDominio::getListaDominios(session('id_dominio_tipos_trabajo'), 'nombre'),
            'subsistemas' => TblDominio::getListaDominios(session('id_dominio_subsistemas'), 'nombre'),
            // 'estados' => TblDominio::wherein('id_dominio', [session('id_dominio_actividad_programado'), session('id_dominio_actividad_comprando')])->get(),
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
    public function update(SaveActividadRequest $request, TblActividad $activity)
    {
        try {
            $this->authorize('update', $activity);
            $activity->update($request->validated());


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
        return $this->getView('actividades.grid');
    }

    private function getView($view) {
        $actividad = new TblActividad;

        return view($view, [
            'model' => TblActividad::with(['tbltipoactividad', 'tblsubsistema', 'tblencargadocliente',
                'tblresposablecontratista', 'tblestacion', 'tblestadoactividad', 'tblcotizacion', 'tblordencompra',
                'tblmesconsolidado', 'tblusuario'])
                ->where(function($q) {
                    $this->dinamyFilters($q);
                })->latest()->paginate(10),
            'clientes' => TblTercero::getClientesTipo(session('id_dominio_representante_cliente')),
            'tipos_trabajo' => TblDominio::getListaDominios(session('id_dominio_tipos_trabajo'), 'nombre'),
            'contratistas' => TblTercero::getClientesTipo(session('id_dominio_coordinador')),
            'estados_actividad' => TblDominio::getListaDominios(session('id_dominio_estados_actividad')),
            'status' => $actividad->status,
            'create' => Gate::allows('create', $actividad),
            'edit' => Gate::allows('update', $actividad),
            'view' => Gate::allows('view', $actividad),
            'request' => $this->filtros,
        ]);
    }

    private function createTrak($activity, $action) {
        try {
            
        } catch (\Throwable $th) {
            Log::error("Error creando track de actividad: ".$th->getMessage());
        }
    }
}
