<?php

namespace App\Http\Controllers;

use App\Exports\ReportsExport;
use App\Http\Requests\SavePuntosInteresRequest;
use App\Imports\DataImport;
use App\Models\TblDominio;
use App\Models\TblPuntosInteres;
use App\Models\TblTercero;
use App\Models\TblUsuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Excel;

class PuntosInteresController extends Controller
{
    protected $filtros;
    protected $excel;

    public function __construct(Excel $excel)
    {
        $this->middleware('auth');
        $this->excel = $excel;
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

                if(!in_array($key, ['full_name'])){
                    $querybuilder->where($key, (count($operador) > 1 ? $operador[0] : 'like'), (count($operador) > 1 ? $operador[1] : strtolower("%$value%")));
                } else if($key == 'full_name' && $value) {
                    $querybuilder->whereHas('tblcliente', function($q) use($value){
                        $q->where('razon_social', 'like', strtolower("%$value%"));
                        $q->orwhere('nombres', 'like', strtolower("%$value%"));
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
            'clientes' => TblTercero::where(['estado' => 1, 'id_dominio_tipo_tercero' => session('id_dominio_cliente')])
                ->orderBy(DB::raw("COALESCE(razon_social, CONCAT(nombres, ' ', apellidos))"), 'asc')
                ->get(),
            'zonas' => TblDominio::getListaDominios(session('id_dominio_zonas'), 'nombre'),
            'transportes' => TblDominio::getListaDominios(session('id_dominio_transportes'), 'nombre'),
            'accesos' => TblDominio::getListaDominios(session('id_dominio_accesos'), 'nombre'),
            'create_client' => TblUsuario::getPermisosMenu('clients.index')->create,
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
            'clientes' => TblTercero::where(['estado' => 1, 'id_dominio_tipo_tercero' => session('id_dominio_cliente')])
                ->orderBy(DB::raw("COALESCE(razon_social, CONCAT(nombres, ' ', apellidos))"), 'asc')
                ->get(),
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
            $this->authorize('update', $site);
            $site->update($request->validated());

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
                })->orderBy('id_punto_interes', 'desc')->paginate(10),
            'clientes' => TblTercero::getTercerosTipo(session('id_dominio_cliente')),
            'zonas' => TblDominio::getListaDominios(session('id_dominio_zonas'), 'nombre'),
            'transportes' => TblDominio::getListaDominios(session('id_dominio_transportes'), 'nombre'),
            'accesos' => TblDominio::getListaDominios(session('id_dominio_accesos'), 'nombre'),
            'export' => Gate::allows('export', $punto),
            'import' => Gate::allows('import', $punto),
            'create' => Gate::allows('create', $punto),
            'edit' => Gate::allows('update', $punto),
            'view' => Gate::allows('view', $punto),
            'request' => $this->filtros,
        ]);
    }

    public static function get_puntos_interes_client($client) {
        return response()->json([
            'estaciones' => TblPuntosInteres::where(['estado' => 1, 'id_cliente' => $client])->orderBy('nombre', 'asc')->pluck('nombre', 'id_punto_interes'),
        ]);
    }

    private function generateDownload($option) {
        return TblPuntosInteres::select(
            DB::raw("
                tbl_puntos_interes.id_punto_interes,
                COALESCE(t.razon_social, CONCAT(t.nombres, ' ', t.apellidos)) as full_name,
                z.nombre as zona,
                tbl_puntos_interes.nombre,
                tbl_puntos_interes.latitud,
                tbl_puntos_interes.longitud,
                tbl_puntos_interes.descripcion,
                tt.nombre as tipo_transporte,
                ta.nombre as tipo_acceso,
                CASE WHEN tbl_puntos_interes.estado = 1 THEN 'Activo' ELSE 'Inactivo' END as estado_sitio
            ")
        )
        ->join('tbl_terceros as t', 'tbl_puntos_interes.id_cliente',  '=', 't.id_tercero')
        ->join('tbl_dominios as z', 'tbl_puntos_interes.id_zona', '=', 'z.id_dominio')
        ->join('tbl_dominios as tt', 'tbl_puntos_interes.id_tipo_transporte', '=', 'tt.id_dominio')
        ->join('tbl_dominios as ta', 'tbl_puntos_interes.id_tipo_accesso', '=', 'ta.id_dominio')
        ->where(function ($q) use($option) {
            if($option == 1) {
                $this->dinamyFilters($q, [
                    'tbl_puntos_interes.id_punto_interes' => 'id_punto_interes',
                    'tbl_puntos_interes.nombre' => 'nombre',
                    'tbl_puntos_interes.estado' => 'estado'
                ]);
            } else {
                $q->where('tbl_puntos_interes.estado', '=', '-1');
            }
        })
        ->get();
    }

    public function export() {
        $headers = ['#', 'Cliente', 'Zona', 'Sitio', 'Latitud', 'Longitud', 'Descripción', 'Tipo transporte',
            'Tipo acceso', 'Estado'
        ];
        return $this->excel->download(new ReportsExport($headers, $this->generateDownload(1)), 'Reporte sitios.xlsx');
    }

    public function downloadTemplate() {
        $headers = ['Documento cliente', 'Zona', 'Sitio', 'Latitud', 'Longitud', 'Descripción', 'Tipo transporte', 'Tipo acceso'];
        return $this->excel->download(new ReportsExport($headers, $this->generateDownload(2)), 'Template puntos interes.xlsx');
    }

    public function import() {
        (new DataImport(new TblPuntosInteres))->import(request()->file('input_file'));
        return back();
    }
}
