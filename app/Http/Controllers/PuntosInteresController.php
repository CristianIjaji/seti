<?php

namespace App\Http\Controllers;

use App\Http\Requests\SavePuntosInteresRequest;
use App\Models\TblDominio;
use App\Models\TblPuntosInteres;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->getView('puntos_interes.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('puntos_interes._form', [
            'site' => new TblPuntosInteres,
            'zonas' => TblDominio::where('estado', '=', 1)
                ->wherein('id_dominio_padre', [session('id_dominio_zonas')])
                ->pluck('nombre', 'id_dominio'),
            'transportes' => TblDominio::where('estado', '=', 1)
                ->wherein('id_dominio_padre', [session('id_dominio_transportes')])
                ->pluck('nombre', 'id_dominio'),
            'accesos' => TblDominio::where('estado', '=', 1)
                ->wherein('id_dominio_padre', [session('id_dominio_accesos')])
                ->pluck('nombre', 'id_dominio'),
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

            return response()->json([
                'success' => 'Punto de interes creado exitosamente!',
                'response' => [
                    'value' => $sitio->id_punto_interes,
                    'option' => $sitio->nombres,
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
        return view('puntos_interes._form', [
            'edit' => true,
            'site' => $site,
            'zonas' => TblDominio::where('estado', '=', 1)
                ->wherein('id_dominio_padre', [session('id_dominio_zonas')])
                ->pluck('nombre', 'id_dominio'),
            'transportes' => TblDominio::where('estado', '=', 1)
                ->wherein('id_dominio_padre', [session('id_dominio_transportes')])
                ->pluck('nombre', 'id_dominio'),
            'accesos' => TblDominio::where('estado', '=', 1)
                ->wherein('id_dominio_padre', [session('id_dominio_accesos')])
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
    public function update(TblPuntosInteres $site, SavePuntosInteresRequest $request)
    {
        try {
            $site->update($request->validated());

            return response()->json([
                'success' => 'Punto de interes actualizado correctamente!'
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
            'model' => TblPuntosInteres::where(function ($q) {
                $this->dinamyFilters($q);
            })->latest()->paginate(10),
            'zonas' => TblDominio::where('estado', '=', 1)
                ->wherein('id_dominio_padre', [session('id_dominio_zonas')])
                ->pluck('nombre', 'id_dominio'),
            'transportes' => TblDominio::where('estado', '=', 1)
                ->wherein('id_dominio_padre', [session('id_dominio_transportes')])
                ->pluck('nombre', 'id_dominio'),
            'accesos' => TblDominio::where('estado', '=', 1)
                ->wherein('id_dominio_padre', [session('id_dominio_accesos')])
                ->pluck('nombre', 'id_dominio'),
            'create' => true,//Gate::allows('create', $punto),
            'edit' => true,//Gate::allows('update', $punto),
            'view' => true,//Gate::allows('view', $punto),
            'request' => $this->filtros,
        ]);
    }
}
