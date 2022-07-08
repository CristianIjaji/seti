<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveTerceroRequest;
use App\Models\TblDominio;
use App\Models\TblHabitacion;
use App\Models\TblOrden;
use App\Models\TblTercero;
use App\Models\TblUsuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class TerceroController extends Controller
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

                if(!in_array($key, ['full_name'])){
                    $querybuilder->where($key, (count($operador) > 1 ? $operador[0] : 'like'), (count($operador) > 1 ? $operador[1] : strtolower("%$value%")));
                } else if($key == 'full_name' && $value) {
                    $querybuilder->where('nombres', 'like', strtolower("%$value%"));
                    $querybuilder->orWhere('apellidos', 'like', strtolower("%$value%"));
                }
            }
            $this->filtros[$key] = $value;
        }

        if(Auth::user()->role !== session('id_dominio_super_administrador')) {
            $querybuilder->where('id_dominio_tipo_tercero', '<>', session('id_dominio_super_administrador'));
        }

        if(!in_array(Auth::user()->role, [session('id_dominio_super_administrador'), session('id_dominio_administrador')])) {
            $querybuilder->whereNotIn('id_dominio_tipo_tercero', [session('id_dominio_super_administrador'), session('id_dominio_administrador')]);
        }

        return $querybuilder;
    }

    private function getAdminRoles() {
        return Auth::user()->role == session('id_dominio_super_administrador')
                ? [0]
                : (
                    Auth::user()->role == session('id_dominio_administrador')
                    ? [session('id_dominio_super_administrador')]
                    : [session('id_dominio_super_administrador'), session('id_dominio_administrador')]
                );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view', new TblTercero);

        return $this->getView('terceros.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', new TblTercero);

        return view('terceros._form', [
            'tercero' => new TblTercero,
            'tipo_documentos' => TblDominio::where('estado', '=', 1)
                ->wherein('id_dominio_padre', [session('id_dominio_tipo_documento')])
                ->pluck('nombre', 'id_dominio'),
            'tipo_terceros' => TblDominio::where('estado', '=', 1)
            ->whereNotIn('id_dominio', $this->getAdminRoles())
            ->wherein('id_dominio_padre', [session('id_dominio_tipo_tercero')])
            ->pluck('nombre', 'id_dominio'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveTerceroRequest $request)
    {
        try {
            $tercero = TblTercero::create($request->validated());
            $this->authorize('create', $tercero);

            return response()->json([
                'success' => 'Tercero creado exitosamente!',
                'response' => [
                    'value' => $tercero->id_tercero,
                    'option' => $tercero->nombres.' '.$tercero->apellidos,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(TblTercero $client)
    {
        $this->authorize('view', $client);

        return view('terceros._form', [
            'edit' => false,
            'tercero' => $client
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(TblTercero $client)
    {
        $this->authorize('update', $client);

        return view('terceros._form', [
            'edit' => true,
            'tercero' => $client,
            'tipo_documentos' => TblDominio::where('estado', '=', 1)
                ->wherein('id_dominio_padre', [session('id_dominio_tipo_documento')])
                ->pluck('nombre', 'id_dominio'),
            'tipo_terceros' => TblDominio::where('estado', '=', 1)
                ->whereNotIn('id_dominio', $this->getAdminRoles())
                ->wherein('id_dominio_padre', [session('id_dominio_tipo_tercero')])
                ->pluck('nombre', 'id_dominio'),
            'estados' => [
                0 => 'Inactivo',
                1 => 'Activo'
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TblTercero $client, SaveTerceroRequest $request)
    {
        try {
            $client->update($request->validated());
            $this->authorize('update', $client);

            $usuario = TblUsuario::where('id_tercero', '=', $client->id_tercero)->get()->first();
            if($usuario) {
                $usuario->email = $client->correo;
                $usuario->save();
            }

            return response()->json([
                'success' => 'Tercero actualizado correctamente!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
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

    public function search_servicios($id_tercero) {
        try {
            $servicios = explode(',', TblTercero::findOrFail($id_tercero)->Servicios);
            // $model = TblTercero::findOrFail($id_tercero);

            // return $model->Servicios;
            return response()->json([
                'servicios' => TblDominio::where('estado', '=', 1)
                    ->wherein('id_dominio', $servicios)
                    ->pluck('nombre', 'id_dominio')
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'erros' => 'revise su conexión a Internet'
            ]);
        }
    }

    public function get_habitaciones($id_tercero, $fecha_inicio, $fecha_fin) {
        try {
            return response()->json([
                'habitaciones' => DB::select(
                    "WITH ordenes AS (
                        SELECT
                            h.id_habitacion,
                            COUNT(o.id_orden) AS habitaciones
                        FROM tbl_ordenes AS o
                        INNER JOIN tbl_habitaciones AS h ON(o.id_habitacion = h.id_habitacion)
                        WHERE o.id_tercero_cliente = $id_tercero
                        AND o.id_dominio_tipo_orden = ".session('id_dominio_reserva_hotel')."
                        AND o.estado IN(".session('id_dominio_orden_cola').", ".session('id_dominio_orden_aceptada').")
                        AND o.fecha_inicio <= '$fecha_fin'
                        AND o.fecha_fin >= '$fecha_inicio'
                        GROUP BY 1
                    )
    
                    SELECT
                        h.id_habitacion,
                        h.nombre as habitacion,
                        (h.cantidad - COALESCE(o.habitaciones, 0)) AS disponibles
                    FROM tbl_habitaciones AS h
                    LEFT JOIN ordenes AS o ON(h.id_habitacion = o.id_habitacion);
                    "
                )
            ]);
        } catch (\Throwable $th) {
            Log::error("Error consultando disponiblidad: ".$th->__toString());

            return response()->json([
                'erros' => 'revise su conexión a Internet'
            ]);
        }
    }

    public function grid() {
        return $this->getView('terceros.grid');
    }

    private function getView($view) {
        $tercero = new TblTercero;

        return view($view, [
            'model' => TblTercero::where(function ($q) {
                $this->dinamyFilters($q);
            })->latest()->paginate(10),
            'tipo_terceros' => TblDominio::where(['estado' => 1])
                ->whereNotIn('id_dominio', $this->getAdminRoles())
                ->wherein('id_dominio_padre', [session('id_dominio_tipo_tercero')])
                ->pluck('nombre', 'id_dominio'),
            'create' => Gate::allows('create', $tercero),
            'edit' => Gate::allows('update', $tercero),
            'view' => Gate::allows('view', $tercero),
            'request' => $this->filtros,
        ]);
    }
}
