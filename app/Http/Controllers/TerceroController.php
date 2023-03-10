<?php

namespace App\Http\Controllers;

use App\Exports\ReportsExport;
use App\Http\Requests\SaveTerceroRequest;
use App\Imports\DataImport;
use App\Models\TblDominio;
use App\Models\TblTercero;
use App\Models\TblUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel;

class TerceroController extends Controller
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
                    $querybuilder->where('tbl_terceros.nombres', $query['operator'], $query['value']);
                    $querybuilder->orWhere('tbl_terceros.apellidos', $query['operator'], $query['value']);
                    $querybuilder->orWhere('tbl_terceros.razon_social', $query['operator'], $query['value']);
                }
            }

            $this->filtros[$key] = $value;
        }

        if(auth()->user()->role !== session('id_dominio_super_administrador')) {
            $querybuilder->where('tbl_terceros.id_dominio_tipo_tercero', '<>', session('id_dominio_super_administrador'));
        }

        if(!in_array(auth()->user()->role, [session('id_dominio_super_administrador'), session('id_dominio_administrador')])) {
            $querybuilder->whereNotIn('tbl_terceros.id_dominio_tipo_tercero', [session('id_dominio_super_administrador'), session('id_dominio_administrador')]);
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
        $this->authorize('view', new TblTercero);
        return $this->getView('terceros.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', new TblTercero);
        return view('terceros._form', [
            'tercero' => new TblTercero,
            'tipo_documentos' => TblDominio::getListaDominios(session('id_dominio_tipo_documento'), 'nombre'),
            'tipo_terceros' => TblTercero::getTipoTerceros(),
            'tipo_documento' => isset(request()->tipo_documento)
                ? TblDominio::where('id_dominio', '=', request()->tipo_documento)->first() 
                : '',
            'tipo_tercero' => isset(request()->tipo_tercero)
                ? TblDominio::where('id_dominio', '=', request()->tipo_tercero)->first()
                : '',
            'terceros' => TblTercero::where(['estado' => 1])->wherein('id_dominio_tipo_tercero', [
                session('id_dominio_cliente'),
                session('id_dominio_contratista'),
                session('id_dominio_analista'),
                session('id_dominio_coordinador')
            ])->get(),
            'ciudades' => TblTercero::pluck('ciudad', 'ciudad'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveTerceroRequest $request)
    {
        try {
            $this->authorize('create', new TblTercero);
            $tercero = TblTercero::create($request->validated());
            $tercero->logo = $request->hasFile('logo') ? $request->file('logo')->store('images') : '';
            $tercero->save();

            return response()->json([
                'success' => 'Tercero creado exitosamente!',
                'response' => [
                    'value' => $tercero->id_tercero,
                    'option' => $tercero->nombres.' '.$tercero->apellidos,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error("Error creando tercero: ".$e->__toString());
            return response()->json([
                'errors' => 'Error creando tercero.'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(TblTercero $client)
    {
        $this->authorize('view', $client);

        return view('terceros._form', [
            'edit' => false,
            'tercero' => $client
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(TblTercero $client)
    {
        $this->authorize('update', $client);

        return view('terceros._form', [
            'edit' => true,
            'tercero' => $client,
            'tipo_documentos' => TblDominio::getListaDominios(session('id_dominio_tipo_documento')),
            'tipo_terceros' => TblTercero::getTipoTerceros(),
            'terceros' => TblTercero::where(['estado' => 1])->where('id_tercero', '<>', $client->id_tercero)->wherein('id_dominio_tipo_tercero', [session('id_dominio_cliente'), session('id_dominio_contratista'), session('id_dominio_coordinador')])->get(),
            'estados' => [
                0 => 'Inactivo',
                1 => 'Activo'
            ],
            'ciudades' => TblTercero::pluck('ciudad', 'ciudad'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TblTercero $client, SaveTerceroRequest $request)
    {
        try {
            $this->authorize('update', $client);
            if($request->hasFile('logo')) {
                Storage::delete($client->logo);
                $client->fill($request->validated());
                $client->logo = $request->file('logo')->store('images');
                $client->save();
            } else {
                $client->update($request->validated());
            }

            $usuario = TblUsuario::where('id_tercero', '=', $client->id_tercero)->get()->first();
            if($usuario) {
                $usuario->email = $client->correo;
                $usuario->save();
            }

            return response()->json([
                'success' => 'Tercero actualizado correctamente!'
            ]);
        } catch (\Exception $e) {
            Log::error("Error actualizando tercero: ".$e->__toString());
            return response()->json([
                'errors' => 'Error actualizando tercero.'
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

    public function search(Request $request) {
        $data = TblTercero::select('name')
            ->where("documento", 'LIKE', "%{$request->query}%")
            ->get();

        return response()->json($data);
    }

    public function grid() {
        return $this->getView('terceros.grid');
    }

    private function getView($view) {
        $tercero = new TblTercero;

        return view($view, [
            'model' => TblTercero::with(['tbldominiodocumento', 'tbldominiotercero', 'tbluser', 'tblusuario'])
                ->where(function ($q) {
                    $this->dinamyFilters($q);
                })->orderBy('id_tercero', 'desc')->paginate(10),
            'tipo_terceros' => TblTercero::getTipoTerceros(),
            'export' => Gate::allows('export', $tercero),
            'import' => Gate::allows('import', $tercero),
            'create' => Gate::allows('create', $tercero),
            'edit' => Gate::allows('update', $tercero),
            'view' => Gate::allows('view', $tercero),
            'request' => $this->filtros,
        ]);
    }

    private function generateDownload($option) {
        return TblTercero::select(
            DB::raw("
                tbl_terceros.id_tercero,
                td.nombre as id_dominio_tipo_documento,
                tbl_terceros.documento,
                tbl_terceros.dv,
                tbl_terceros.razon_social,
                CONCAT(tbl_terceros.nombres, ' ', tbl_terceros.apellidos) as nombres,
                tbl_terceros.ciudad,
                tbl_terceros.direccion,
                tbl_terceros.correo,
                tbl_terceros.telefono,
                tt.nombre as id_dominio_tipo_tercero,
                CASE WHEN tbl_terceros.estado = 1 THEN 'Activo' ELSE 'Inactivo' END estado_tercero,
                COALESCE(t.razon_social, CONCAT(t.nombres, ' ', t.apellidos)) as dependencia
            ")
        )
        ->join('tbl_dominios as td', 'tbl_terceros.id_dominio_tipo_documento', '=', 'td.id_dominio')
        ->join('tbl_dominios as tt', 'tbl_terceros.id_dominio_tipo_tercero', '=', 'tt.id_dominio')
        ->leftjoin('tbl_terceros as t', 'tbl_terceros.id_tercero_responsable', '=', 't.id_tercero')
        ->where(function ($q) use($option) {
            if($option == 1) {
                $this->dinamyFilters($q, [
                    'tbl_terceros.id_dominio_tipo_documento' => 'id_dominio_tipo_documento',
                    'tbl_terceros.documento' => 'documento',
                    'tbl_terceros.nombres' => 'nombres',
                    'tbl_terceros.apellidos' => 'apellidos',
                    'tbl_terceros.ciudad' => 'ciudad',
                    'tbl_terceros.id_dominio_tipo_tercero' => 'id_dominio_tipo_tercero',
                    'tbl_terceros.estado' => 'estado'
                ]);
            } else {
                $q->where('tbl_terceros.estado', '=', '-1');
            }
        })
        ->get();
    }

    public function export() {
        $headers = ['#', 'Tipo documento', 'Documento', 'DV', 'Raz??n social', 'Nombre', 'Ciudad', 'Direcci??n',
            'Correo', 'Tel??fono', 'Tipo tercero', 'Estado', 'Dependencia'
        ];

        return $this->excel->download(new ReportsExport($headers, $this->generateDownload(1)), 'Reporte terceros.xlsx');
    }

    public function downloadTemplate() {
        $headers = ['Tipo documento', 'Documento', 'D??gito Verificaci??n', 'Raz??n social', 'Nombres', 'Apellidos', 'Ciudad', 'Direcci??n',
            'Correo', 'Tel??fono', 'Tipo tercero', 'Dependencia'
        ];
        return $this->excel->download(new ReportsExport($headers, $this->generateDownload(2)), 'Template terceros.xlsx');
    }

    public function import() {
        (new DataImport(new TblTercero))->import(request()->file('input_file'));
        return back();
    }

    public function getTercerosByTipo($tipo) {
        return response()->json([
            'terceros' => TblTercero::getTercerosTipo($tipo)
        ]);
    }
}
