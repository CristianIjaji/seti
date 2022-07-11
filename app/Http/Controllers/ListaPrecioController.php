<?php

namespace App\Http\Controllers;

use App\Models\TblDominio;
use App\Models\TblListaPrecio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ListaPrecioController extends Controller
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
        return $querybuilder;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->getView('lista_precios.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('lista_precios._form', [
            'lista_precio' => new TblListaPrecio, //modelo
            'clientes' => DB::table('tbl_terceros', 't')
                ->select('t.id_tercero',
                    DB::raw("CONCAT(t.nombres,' ', t.apellidos) as nombre")
                )->where('t.id_dominio_tipo_tercero', '=', session('id_dominio_asociado'))->get(),
            'tipo_items' => TblDominio::where('estado', "=", 1)
                ->where('id_dominio_padre', "=", session('id_dominio_tipo_items'))->pluck('nombre', 'id_dominio')
        ]);
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
        return $this->getView('lista_precios.grid');
    }

    private function getView($view) {
        $listaPrecios = new TblListaPrecio;

        return view($view, [
            'model' => TblListaPrecio::where(function ($q) {
                $this->dinamyFilters($q);
            })->latest()->paginate(10),
            // 'tipo_terceros' => TblDominio::where(['estado' => 1])
            //     ->whereNotIn('id_dominio', $this->getAdminRoles())
            //     ->wherein('id_dominio_padre', [session('id_dominio_tipo_tercero')])
            //     ->pluck('nombre', 'id_dominio'),
            'create' => true,//Gate::allows('create', $tercero),
            'edit' => true,//Gate::allows('update', $tercero),
            'view' => true,//Gate::allows('view', $tercero),
            'request' => $this->filtros,
        ]);
    }
}
