<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveListaPrecioRequest;
use App\Models\TblDominio;
use App\Models\TblListaPrecio;
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
                    $querybuilder->whereHas('tbltercerocliente', function($q) use($value){
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
                )->where('t.id_dominio_tipo_tercero', '=', session('id_dominio_cliente'))->get(),
            'tipo_items' => TblDominio::where('estado', "=", 1)
                ->where('id_dominio_padre', "=", session('id_dominio_tipo_items'))->pluck('nombre', 'id_dominio'),
            'unidades' => TblListaPrecio::pluck('unidad', 'unidad'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveListaPrecioRequest $request)
    {
        try {
            $listaPrecios = TblListaPrecio::create($request->validated());
            //$this->authorize('create', $listaPrecios);

            return response()->json([
                'success' => 'Ítem creado exitosamente!',
                'response' => [
                    'value' => $listaPrecios->id_lista_precio,  //llave primaria
                    'option' => $listaPrecios->codigo //Item a mostrar creado
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
    public function show(TblListaPrecio $priceList)//parametro de la lista de turas
    {
        return view('lista_precios._form', [
            'edit' => false,
            'lista_precio' => $priceList //llave sale del form y el value la nueva instancia del modelo. 
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(TblListaPrecio $priceList)
    {
        return view('lista_precios._form', [
            'edit' => true,
            'lista_precio' => $priceList, //modelo
            'clientes' => DB::table('tbl_terceros', 't')
                ->select('t.id_tercero',
                    DB::raw("CONCAT(t.nombres,' ', t.apellidos) as nombre")
                )->where('t.id_dominio_tipo_tercero', '=', session('id_dominio_cliente'))->get(),
            'tipo_items' => TblDominio::where('estado', "=", 1)
                ->where('id_dominio_padre', "=", session('id_dominio_tipo_items'))->pluck('nombre', 'id_dominio'),
            'unidades' => TblListaPrecio::pluck('unidad', 'unidad'),
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
    public function update(TblListaPrecio $priceList, SaveListaPrecioRequest $request)
    {
        try {
            $priceList->update($request->validated());
            return response()->json([
            'success' => 'Ítem actualizado correctamente!'
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

    public function search($type) {
        if(empty($type)) {
            return response()->json(['errors' => 'Error consultando lista de precios.']);
        }
        return view('lista_precios._search', [
            'type' => $type,
            'items' => TblListaPrecio::where(['estado' => 1, 'id_tipo_item' => $type])->get()
        ]);
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
            'listaTipoItemPrecio' => TblDominio::where(['estado' => 1, 'id_dominio_padre' => session('id_dominio_tipo_items')])
                ->pluck('nombre', 'id_dominio'),
            'create' => true,//Gate::allows('create', $tercero),
            'edit' => true,//Gate::allows('update', $tercero),
            'view' => true,//Gate::allows('view', $tercero),
            'request' => $this->filtros,
        ]);
    }
}
