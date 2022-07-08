<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveParametroRequest;
use App\Models\TblDominio;
use App\Models\TblParametro;
use Illuminate\Support\Facades\Gate;

class ParametroController extends Controller
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

                if(!in_array($key, ['nombre'])) {
                    $querybuilder->where($key, (count($operador) > 1 ? $operador[0] : 'like'), (count($operador) > 1 ? $operador[1] : strtolower("%$value%")));
                } else {
                    $querybuilder->whereHas('tbldominio', function($q) use($value){
                        $q->where('nombre', 'like', strtolower("%$value%"));
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
        $this->authorize('view', new TblParametro);

        return $this->getView('parametros.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', new TblParametro);

        return view('parametros._form', [
            'parametro' => new TblParametro,
            'dominios' => TblDominio::where('estado', 1)
                ->orderby('nombre')
                ->pluck('nombre', 'id_dominio'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveParametroRequest $request)
    {
        try {
            $parametro = TblParametro::create($request->validated());
            $this->authorize('create', $parametro);

            return response()->json(([
                'success' => 'Parametro creado exitosamente!',
            ]));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(TblParametro $param)
    {
        $this->authorize('view', $param);

        return view('parametros._form', [
            'edit' => false,
            'parametro' => $param
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(TblParametro $param)
    {
        $this->authorize('update', $param);

        return view('parametros._form', [
            'edit' => true,
            'parametro' => $param,
            'dominios' => TblDominio::where('estado', 1)
                ->orderby('nombre')
                ->pluck('nombre', 'id_dominio'),
            'dominios_parametros' => [],
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
    public function update(TblParametro $param, SaveParametroRequest $request)
    {
        try {
            $param->update($request->validated());
            $this->authorize('update', $param);

            return response()->json([
                'success' => 'Parametro actualizado correctamente!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage(),
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

    public function grid(){
        return $this->getView('parametros.grid');
    }

    private function getView($view) {
        return view($view, [
            'model' => TblParametro::with('tbldominio')->where(function ($q) {
                $this->dinamyFilters($q);
            })->latest()->paginate(10),
            'create' => Gate::allows('create', new TblParametro),
            'edit' => Gate::allows('update', new TblParametro),
            'view' => Gate::allows('view', new TblParametro),
            'request' => $this->filtros,
        ]);
    }
}
