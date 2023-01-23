<?php

namespace App\Http\Controllers;

use App\Exports\ReportsExport;
use App\Models\TblDominio;
use App\Models\TblMovimiento;
use App\Models\TblTercero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Excel;

class MovimientoController extends Controller
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

                if(!in_array($key, ['nombre'])){
                    $querybuilder->where($key, (count($operador) > 1 ? $operador[0] : 'like'), (count($operador) > 1 ? $operador[1] : strtolower("%$value%")));
                } else if($key == 'nombre' && $value) {
                    // $querybuilder->wherehas('tbltipomovimiento', function($q) use($value) {
                    //     $q->where('tbl_dominios.nombre', 'like', strtolower("%$value%"));
                    // });
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
        $this->authorize('view', new TblMovimiento);
        return $this->getView('movimientos.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', new TblMovimiento);

        return view('movimientos._form', [
            'movimiento' => new TblMovimiento,
            'tipo_movimientos' => TblDominio::with(['tbldominio'])
                ->where([
                    'estado' => 1,
                ])->whereIn('id_dominio_padre', [session('id_dominio_entrada'), session('id_dominio_salida')])
                ->whereNotIn('id_dominio', [
                    session('id_dominio_movimiento_entrada_inicial'),
                    session('id_dominio_movimiento_salida_ajuste'),
                    session('id_dominio_movimiento_entrada_ajuste')
                ])
                ->get(),
            'impuestos' => TblDominio::getListaDominios(session('id_dominio_impuestos')),
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
    public function show(TblMovimiento $move)
    {
        $this->authorize('view', $move);

        return view('movimientos._form', [
            'edit' => false,
            'movimiento' => $move,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(TblMovimiento $move)
    {
        $this->authorize('update', $move);

        return view('movimientos._form', [
            'edit' => (in_array($move->id_dominio_estado, [session('id_dominio_movimiento_pendiente')]) ? true : false),
            'movimiento' => $move,
            'tipo_movimientos' => TblDominio::with(['tbldominio'])
                ->where([
                    'estado' => 1,
                ])->whereIn('id_dominio_padre', [session('id_dominio_entrada'), session('id_dominio_salida')])
                ->whereNotIn('id_dominio', [
                    session('id_dominio_movimiento_entrada_inicial'),
                    session('id_dominio_movimiento_salida_ajuste'),
                    session('id_dominio_movimiento_entrada_ajuste')
                ])
                ->get(),
            'impuestos' => TblDominio::getListaDominios(session('id_dominio_impuestos')),
        ]);
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
        return $this->getView('movimientos.grid');
    }

    public function getView($view) {
        $movimiento = new TblMovimiento;

        return view($view, [
            'model' => TblMovimiento::with(['tblmovimientodetalle', 'tbltipomovimiento', 'tbltercerorecibe', 'tblterceroentrega', 'tblestado'])
                ->where(function ($q) {
                    $this->dinamyFilters($q);
                })->orderBy('id_movimiento', 'desc')->paginate(10),
            'estados' => TblDominio::getListaDominios(session('id_dominio_estados_movimiento')),
            'entregan' => TblMovimiento::select(
                DB::raw("
                    t.id_tercero,
                    CASE WHEN t.razon_social != ''
                        THEN t.razon_social
                        ELSE CONCAT(t.nombres, ' ', t.apellidos)
                    END as nombre
                ")
            )->join('tbl_terceros as t', 'tbl_movimientos.id_tercero_entrega', '=', 't.id_tercero')
            ->pluck('nombre', 'id_tercero'),
            'reciben' => TblMovimiento::select(
                DB::raw("
                    t.id_tercero,
                    CASE WHEN t.razon_social != ''
                        THEN t.razon_social
                        ELSE CONCAT(t.nombres, ' ', t.apellidos)
                    END as nombre
                ")
            )->join('tbl_terceros as t', 'tbl_movimientos.id_tercero_recibe', '=', 't.id_tercero')
            ->pluck('nombre', 'id_tercero'),
            'tipo_movimientos' => TblDominio::select(
                DB::raw("
                    tbl_dominios.id_dominio,
                    CONCAT(padre.nombre, ' ', tbl_dominios.nombre) as nombre
                ")
            )->join('tbl_dominios as padre', 'tbl_dominios.id_dominio_padre', '=', 'padre.id_dominio')
            ->where('padre.id_dominio_padre', '=', session('id_dominio_tipo_movimiento'))
            ->pluck('nombre', 'id_dominio'),
            'export' => Gate::allows('export', $movimiento),
            'import' => Gate::allows('import', $movimiento),
            'create' => Gate::allows('create', $movimiento),
            'edit' => Gate::allows('update', $movimiento),
            'view' => Gate::allows('view', $movimiento),
            'request' => $this->filtros
        ]);
    }

    private function generateDownload($option) {
        return TblMovimiento::select(
            DB::raw("
                tbl_movimientos.id_movimiento,
                tbl_movimientos.created_at as fecha,
                CASE WHEN entrega.razon_social != ''
                        THEN entrega.razon_social
                        ELSE CONCAT(entrega.nombres, ' ', entrega.apellidos)
                END as nombre_entrega,
                CASE WHEN recibe.razon_social != ''
                        THEN recibe.razon_social
                        ELSE CONCAT(recibe.nombres, ' ', recibe.apellidos)
                END as nombre_recibe,
                CONCAT(p.nombre, ' ', d.nombre) as tipo_movimiento,
                tbl_movimientos.documento,
                tbl_movimientos.total as total_movimiento,
                tbl_movimientos.saldo as saldo_movimiento,
                tbl_movimientos.observaciones,
                estado.nombre
            ")
        )->join('tbl_terceros as entrega', 'tbl_movimientos.id_tercero_entrega', 'entrega.id_tercero')
        ->join('tbl_terceros as recibe', 'tbl_movimientos.id_tercero_recibe', 'recibe.id_tercero')
        ->join('tbl_dominios as d', 'tbl_movimientos.id_dominio_tipo_movimiento', '=', 'd.id_dominio')
        ->join('tbl_dominios as p', 'd.id_dominio_padre', '=', 'p.id_dominio')
        ->join('tbl_dominios as estado', 'tbl_movimientos.id_dominio_estado', '=', 'estado.id_dominio')
        ->where(function($q) use($option) {
            if($option == 1) {
                $this->dinamyFilters($q, [
                    'tbl_movimientos.created_at' => 'created_at',
                    'tbl_movimientos.documento' => 'documento'
                ]);
            } else {
                $q->where('tbl_movimientos.id_movimiento', '=', '-1');
            }
        })->get();
    }

    public function export() {
        $headers = ['#', 'Fecha', 'Entrega', 'Recibe', 'Tipo movimiento', 'Documento',
            'Total', 'Saldo', 'Observaciones', 'Estado'
        ];

        return $this->excel->download(new ReportsExport($headers, $this->generateDownload(1)), 'Reporte movimientos inventario.xlsx');
    }
}
