<?php

namespace App\Http\Controllers;

use App\Models\TblEstado;
use Illuminate\Http\Request;

class EstadoController extends Controller
{
    protected $filtros;

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function dinamyFilters($querybuilder, $fields = []) {
        foreach (request()->all() as $key => $value) {
            if($value !== null && !in_array($key, ['_token', 'table', 'page'])) {
                $query = getValoresConsulta($value);

                $key = (array_search($key, $fields) ? array_search($key, $fields) : $key);

                if(!in_array($key, ['nombre', 'full_name'])){
                    $querybuilder->where($key, $query['operator'], $query['value']);
                } else if($key == 'nombre' && $value) {
                    $querybuilder->whereHas('tblestado', function($q) use($query) {
                        $q->where('tbl_dominios.nombre', $query['operator'], $query['value']);
                    });
                } else if($key == 'full_name' && $value) {
                    $querybuilder->whereHas('tblusuario', function($q) use($query) {
                        $q->whereHas('tbltercero', function($q2) use($query) {
                            $q2->where('tbl_terceros.razon_social', $query['operator'], $query['value']);
                            $q2->orwhere('tbl_terceros.nombres', $query['operator'], $query['value']);
                            $q2->orwhere('tbl_terceros.apellidos', $query['operator'], $query['value']);
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

    public function grid() {
        return $this->getView('partials._track');
    }

    private function getView($view) {
        return view($view, [
            'edit' => true,
            'model' => TblEstado::with(['tblestado', 'tblusuario'])
                ->where(function ($q) {
                    $this->dinamyFilters($q);
                })->orderBy('created_at', 'desc')->paginate(1000000),
            'request' => $this->filtros,
            'title' => 'Estados cotización',
            'route' => 'statequotes',
        ]);
    }
}
