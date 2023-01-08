<?php

namespace App\Http\Controllers;

use App\Models\TblEstadoActividad;
use Illuminate\Http\Request;

class EstadoActividadController extends Controller
{
    protected $filtros;

    public function __construct()
    {
        $this->middleware('auth');
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

                if(!in_array($key, ['nombre', 'full_name'])){
                    $querybuilder->where($key, (count($operador) > 1 ? $operador[0] : 'like'), (count($operador) > 1 ? $operador[1] : strtolower("%$value%")));
                } else if($key == 'nombre' && $value) {
                    $querybuilder->whereHas('tblestado', function($q) use($value) {
                        $q->where('tbl_dominios.nombre', 'like', strtolower("%$value%"));
                    });
                } else if($key == 'full_name' && $value) {
                    $querybuilder->whereHas('tblusuario', function($q) use($value) {
                        $q->whereHas('tbltercero', function($q2) use($value) {
                            $q2->where('tbl_terceros.razon_social', 'like', strtolower("%$value%"));
                            $q2->orwhere('tbl_terceros.nombres', 'like', strtolower("%$value%"));
                            $q2->orwhere('tbl_terceros.apellidos', 'like', strtolower("%$value%"));
                        });
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
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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

    public  function grid() {
        return $this->getView('actividades._track');
    }

    public function getView($view){
        $estados = new TblEstadoActividad;

        return view($view, [
            'edit' => true,
            'model' => TblEstadoActividad::with(['tblactividad', 'tblestado', 'tblusuario'])
                ->where(function ($q) {
                    $this->dinamyFilters($q);
                })->orderBy('created_at', 'desc')->paginate(10),
            'request' => $this->filtros
        ]);

    }
}
