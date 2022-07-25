<?php

namespace App\Http\Controllers;

use App\Http\Requests\SavePuntosInteresRequest;
use App\Models\TblDominio;
use App\Models\TblPuntosInteres;
use App\Models\TblTercero;
use App\Models\TblUsuario;
use Illuminate\Support\Facades\Gate;

class PuntosInteresController extends Controller
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
                    $querybuilder->whereHas('tblcliente', function($q) use($value){
                        $q->where('nombres', 'like', strtolower("%$value%"));
                        $q->orwhere('apellidos', 'like', strtolower("%$value%"));
                    });
                }
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
        $this->authorize('view', new TblPuntosInteres);

        return $this->getView('puntos_interes.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', new TblPuntosInteres);

        return view('puntos_interes._form', [
            'site' => new TblPuntosInteres,
            'clientes' => TblTercero::getClientesTipo(session('id_dominio_cliente')),
            'zonas' => TblDominio::getListaDominios(session('id_dominio_zonas')),
            'transportes' => TblDominio::getListaDominios(session('id_dominio_transportes')),
            'accesos' => TblDominio::getListaDominios(session('id_dominio_accesos')),
            'create_client' => isset(TblUsuario::getPermisosMenu('clients.index')->create) ? TblUsuario::getPermisosMenu('clients.index')->create : false,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SavePuntosInteresRequest $request)
    {
        try {
            $sitio = TblPuntosInteres::create($request->validated());
            $this->authorize('create', $sitio);

            return response()->json([
                'success' => 'Punto de interés creado exitosamente!',
                'response' => [
                    'value' => $sitio->id_punto_interes,
                    'option' => $sitio->nombre,
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
    public function show(TblPuntosInteres $site)
    {
        $this->authorize('view', $site);

        return view('puntos_interes._form', [
            'edit' => false,
            'site' => $site
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(TblPuntosInteres $site)
    {
        $this->authorize('update', $site);

        return view('puntos_interes._form', [
            'edit' => true,
            'site' => $site,
            'clientes' => TblTercero::getClientesTipo(session('id_dominio_cliente')),
            'zonas' => TblDominio::getListaDominios(session('id_dominio_zonas')),
            'transportes' => TblDominio::getListaDominios(session('id_dominio_transportes')),
            'accesos' => TblDominio::getListaDominios(session('id_dominio_accesos')),
            'estados' => [
                0 => 'Inactivo',
                1 => 'Activo'
            ],
            'create_client' => isset(TblUsuario::getPermisosMenu('clients.index')->create) ? TblUsuario::getPermisosMenu('clients.index')->create : false,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TblPuntosInteres $site, SavePuntosInteresRequest $request)
    {
        try {
            $site->update($request->validated());
            $this->authorize('update', $site);

            return response()->json([
                'success' => 'Punto de interés actualizado correctamente!'
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
        return $this->getView('puntos_interes.grid');
    }

    private function getView($view) {
        $punto = new TblPuntosInteres;

        return view($view, [
            'model' => TblPuntosInteres::with(['tblcliente', 'tbldominiozona', 'tbldominiotransporte', 'tbldominioacceso', 'tblusuario'])
                ->where(function ($q) {
                    $this->dinamyFilters($q);
                })->latest()->paginate(10),
            'zonas' => TblDominio::getListaDominios(session('id_dominio_zonas')),
            'transportes' => TblDominio::getListaDominios(session('id_dominio_transportes')),
            'accesos' => TblDominio::getListaDominios(session('id_dominio_accesos')),
            'create' => Gate::allows('create', $punto),
            'edit' => Gate::allows('update', $punto),
            'view' => Gate::allows('view', $punto),
            'request' => $this->filtros,
        ]);
    }

    public static function get_puntos_interes_client($client) {
        return response()->json([
            'estaciones' => TblPuntosInteres::where(['estado' => 1, 'id_cliente' => $client])->pluck('nombre', 'id_punto_interes'),
        ]);
    }
}
