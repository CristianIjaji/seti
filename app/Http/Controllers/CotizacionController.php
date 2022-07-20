<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveCotizacionRequest;
use App\Models\TblCotizacion;
use App\Models\TblDominio;
use App\Models\TblPuntosInteres;
use App\Models\TblTercero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CotizacionController extends Controller
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
                    // $querybuilder->where('nombres', 'like', strtolower("%$value%"));
                    // $querybuilder->orWhere('apellidos', 'like', strtolower("%$value%"));
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
        return $this->getView('cotizaciones.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('cotizaciones._form', [
            'cotizacion' => new TblCotizacion,
            'clientes' => TblTercero::getClientes(),
            'tipos_trabajo' => TblDominio::getListaDominios(session('id_dominio_tipos_trabajo')),
            'prioridades' => TblDominio::getListaDominios(session('id_dominio_tipos_prioridad')),
            'impuestos' => TblDominio::getListaDominios(session('id_dominio_impuestos')),
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

            return response()->json([
                'success' => 'Punto de interes creado exitosamente!',
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
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
        return $this->getView('terceros.grid');
    }

    private function getView($view) {
        $cotizacion = new TblCotizacion;

        return view($view, [
            'model' => TblCotizacion::where(function ($q) {
                $this->dinamyFilters($q);
            })->latest()->paginate(10),
            'clientes' => TblTercero::getClientes(),
            'estaciones' => TblPuntosInteres::where('estado', '=', 1)->pluck('nombre', 'id_punto_interes'),
            'create' => true,//Gate::allows('create', $tercero),
            'edit' => true,//Gate::allows('update', $tercero),
            'view' => true,//Gate::allows('view', $tercero),
            'request' => $this->filtros,
        ]);
    }
}
