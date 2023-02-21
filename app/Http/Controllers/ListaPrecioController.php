<?php

namespace App\Http\Controllers;

use App\Exports\ReportsExport;
use App\Http\Requests\SaveListaPrecioRequest;
use App\Imports\DataImport;
use App\Models\TblDominio;
use App\Models\TblListaPrecio;
use App\Models\TblTercero;
use App\Models\TblUsuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Excel;

class ListaPrecioController extends Controller
{
    protected $filtros;
    protected $excel;

    public function __construct(Excel $excel)
    {
        $this->middleware('auth');
        $this->excel = $excel;
    }
    
    private function dinamyFilters($querybuilder, $fields = []) {
        foreach (request()->all() as $key => $value) {
            if($value !== null && !in_array($key, ['_token', 'table', 'page'])) {
                $query = getValoresConsulta($value);

                $key = (array_search($key, $fields) ? array_search($key, $fields) : $key);

                if(!in_array($key, ['full_name'])){
                    $querybuilder->where($key, $query['operator'], $query['value']);
                } else if($key == 'full_name' && $value) {
                    $querybuilder->whereHas('tbltercerocliente', function($q) use($query){
                        $q->where('razon_social', $query['operator'], $query['value']);
                        $q->orwhere('nombres', $query['operator'], $query['value']);
                        $q->orwhere('apellidos', $query['operator'], $query['value']);
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
        $this->authorize('view', new TblListaPrecio);

        return $this->getView('lista_precios.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', new TblListaPrecio);

        return view('lista_precios._form', [
            'lista_precio' => new TblListaPrecio, //modelo
            'clientes' => TblTercero::where(['estado' => 1, 'id_dominio_tipo_tercero' => session('id_dominio_cliente')])
                ->orderBy(DB::raw("COALESCE(razon_social, CONCAT(nombres, ' ', apellidos))"), 'asc')
                ->get(),
            'tipo_items' => TblDominio::where('estado', "=", 1)
                ->where('id_dominio_padre', "=", session('id_dominio_tipo_items'))
                ->pluck('nombre', 'id_dominio'),
            'unidades' => TblListaPrecio::pluck('unidad', 'unidad'),
            'create_client' => isset(TblUsuario::getPermisosMenu('clients.index')->create) ? TblUsuario::getPermisosMenu('clients.index')->create : false,
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
            $this->authorize('create', new TblListaPrecio);
            $listaPrecios = TblListaPrecio::create($request->validated());

            return response()->json([
                'success' => 'Ítem creado exitosamente!',
                'response' => [
                    'value' => $listaPrecios->id_lista_precio,  //llave primaria
                    'option' => $listaPrecios->codigo //Item a mostrar creado
                ],
            ]);
        } catch (\Exception $e) {
            Log::error("Error creando lista de precio: ".$e->__toString());
            return response()->json([
                'errors' => 'Error creando lista de precio.'
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
        $this->authorize('view', $priceList);

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
        $this->authorize('update', $priceList);

        return view('lista_precios._form', [
            'edit' => true,
            'lista_precio' => $priceList, //modelo
            'clientes' => TblTercero::where(['estado' => 1, 'id_dominio_tipo_tercero' => session('id_dominio_cliente')])->get(),
            'tipo_items' => TblDominio::getListaDominios(session('id_dominio_tipo_items')),
            'unidades' => TblListaPrecio::pluck('unidad', 'unidad'),
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
    public function update(TblListaPrecio $priceList, SaveListaPrecioRequest $request)
    {
        try {
            $this->authorize('update', $priceList);
            $priceList->update($request->validated());

            return response()->json([
                'success' => 'Ítem actualizado correctamente!'
            ]);
        } catch (\Throwable $th) {
            Log::error("Error editando lista de precio: ".$th->__toString());
            return response()->json([
                'errors' => 'Error editando lista de precio.'
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

    public function search($type, $client, $tipo_carrito) {
        if(empty($type) || empty($client) || empty($tipo_carrito)) {
            return response()->json(['errors' => 'Error consultando lista de precios.']);
        }

        return view('partials._search', [
            'type' => $type,
            'multiple' => true,
            'tipo_carrito' => $tipo_carrito,
            'items' => TblListaPrecio::where(['estado' => 1, 'id_dominio_tipo_item' => $type, 'id_tercero_cliente' => $client])->get()
        ]);
    }

    public function grid() {
        return $this->getView('lista_precios.grid');
    }

    private function getView($view) {
        $listaPrecios = new TblListaPrecio;

        return view($view, [
            'model' => TblListaPrecio::with(['tbltercerocliente', 'tbldominioitem', 'tblusuario'])
                ->where(function ($q) {
                    $this->dinamyFilters($q);
                })->orderBy('id_lista_precio', 'desc')->paginate(10),
            'listaTipoItemPrecio' => TblDominio::getListaDominios(session('id_dominio_tipo_items'), 'nombre'),
            'clientes' => TblTercero::getTercerosTipo(session('id_dominio_cliente')),
            'export' => Gate::allows('export', $listaPrecios),
            'import' => Gate::allows('import', $listaPrecios),
            'create' => Gate::allows('create', $listaPrecios),
            'edit' => Gate::allows('update', $listaPrecios),
            'view' => Gate::allows('view', $listaPrecios),
            'request' => $this->filtros,
        ]);
    }

    private function generateDownload($option) {
        return TblListaPrecio::select(
            DB::raw("
                tbl_lista_precios.id_lista_precio,
                CONCAT(t.nombres, ' ', t.apellidos) as nombre,
                ti.nombre as tipo_item,
                tbl_lista_precios.codigo,
                tbl_lista_precios.descripcion,
                tbl_lista_precios.unidad,
                tbl_lista_precios.cantidad,
                tbl_lista_precios.valor_unitario,
                CASE WHEN tbl_lista_precios.estado = 1 THEN 'Activo' ELSE 'Inactivo' END estado_lista
            ")
        )
        ->join('tbl_terceros as t', 'tbl_lista_precios.id_tercero_cliente', '=', 't.id_tercero')
        ->join('tbl_dominios as ti', 'tbl_lista_precios.id_dominio_tipo_item', '=', 'ti.id_dominio')
        ->where(function ($q) use($option) {
            if($option == 1) {
                $this->dinamyFilters($q, [
                    'tbl_lista_precios.estado' => 'estado'
                ]);
            } else {
                $q->where('tbl_lista_precios.estado', '=', '-1');
            }
        })
        ->get();
    }

    public function export() {
        $headers = ['#', 'Cliente', 'Tipo ítem', 'Código', 'Descripción', 'Unidad', 'Cantidad', 'Valor unitario', 'Estado'];
        return $this->excel->download(new ReportsExport($headers, $this->generateDownload(1)), 'Reporte lista precios.xlsx');
    }

    public function downloadTemplate() {
        $headers = ['Documento cliente', 'Tipo ítem', 'Código', 'Descripción', 'Unidad', 'Cantidad', 'Valor unitario'];
        return $this->excel->download(new ReportsExport($headers, $this->generateDownload(2)), 'Template lista precios.xlsx');
    }

    public function import() {
        (new DataImport(new TblListaPrecio))->import(request()->file('input_file'));
        return back();
    }
}
