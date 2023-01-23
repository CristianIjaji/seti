<?php

namespace App\Http\Controllers;

use App\Exports\ReportsExport;
use App\Models\TblDominio;
use App\Models\TblKardex;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Excel;

class KardexController extends Controller
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

                if(!in_array($key, ['descripcion', 'tipo_movimiento'])){
                    $querybuilder->where($key, (count($operador) > 1 ? $operador[0] : 'like'), (count($operador) > 1 ? $operador[1] : strtolower("%$value%")));
                } else if($key == 'descripcion' && $value) {
                    $querybuilder->whereHas('tblinventario', function($q) use($value){
                        $q->where('tbl_inventario.descripcion', 'like', strtolower("%$value%"));
                    });
                } else if($key == 'tipo_movimiento') {
                    $querybuilder->whereHas('tblmovimientodetalle', function($q) use($operador) {
                        $q->whereHas('tblmovimiento', function($q2) use($operador) {
                            $q2->whereHas('tbltipomovimiento', function($q3) use($operador) {
                                $q3->whereHas('tbldominio', function($q4) use($operador) {
                                    $q4->where('laravel_reserved_0.id_dominio', '=', $operador[1]);
                                });
                            });
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
        $this->authorize('view', new TblKardex);
        return $this->getView('kardex.index');
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
    public function show(TblKardex $kardex)
    {
        $this->authorize('view', $kardex);

        return view('kardex._form', [
            'kardex' => $kardex
        ]);
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
        return $this->getView('kardex.grid');
    }

    private function getView($view) {
        $kardex = new TblKardex;

        return view($view, [
            'model' => TblKardex::with(['tblinventario', 'tblusuario'])
                ->where(function ($q) {
                    $this->dinamyFilters($q);
                })->orderBy('id_kardex', 'desc')->paginate(10),
            'tipos_movimientos' => TblDominio::getListaDominios(session('id_dominio_tipo_movimiento')),
            'export' => Gate::allows('export', $kardex),
            'import' => Gate::allows('import', $kardex),
            'create' => Gate::allows('create', $kardex),
            'edit' => Gate::allows('edit', $kardex),
            'view' => Gate::allows('view', $kardex),
            'request' => $this->filtros
        ]);
    }

    private function generateDownload($option) {
        $fecha = (env('DB_CONNECTION') == 'mysql'
            ? "DATE_FORMAT(tbl_kardex.created_at, '%Y-%m-%d')"
            : "tbl_kardex.created_at::date"
        );

        return TblKardex::select(
            DB::raw("
                tbl_kardex.id_kardex,
                tbl_kardex.created_at as fecha,
                CONCAT(entrega.nombres, ' ', entrega.apellidos) as nombre_entrega,
                CONCAT(recibe.nombres, ' ', recibe.apellidos) as nombre_recibe,
                tbl_kardex.concepto,
                detalle.id_movimiento,
                CASE WHEN tm.id_dominio_padre = ".session('id_dominio_entrada')." THEN detalle.cantidad ELSE '' END entra,
                CASE WHEN tm.id_dominio_padre = ".session('id_dominio_entrada')." THEN tbl_kardex.valor_unitario ELSE '' END as valor_unitario_entra,
                CASE WHEN tm.id_dominio_padre = ".session('id_dominio_entrada')." THEN tbl_kardex.valor_total ELSE '' END as valor_total_entra,
                CASE WHEN tm.id_dominio_padre = ".session('id_dominio_salida')." THEN detalle.cantidad ELSE '' END sale,
                CASE WHEN tm.id_dominio_padre = ".session('id_dominio_salida')." THEN tbl_kardex.valor_unitario ELSE '' END as valor_unitario_sale,
                CASE WHEN tm.id_dominio_padre = ".session('id_dominio_salida')." THEN tbl_kardex.valor_total ELSE '' END as valor_total_salida,
                tbl_kardex.saldo_cantidad,
                tbl_kardex.saldo_valor_unitario as valor_saldo_unitario,
                tbl_kardex.saldo_valor_total as valor_saldo_total
            ")
        )->join('tbl_movimientos_detalle as detalle', 'tbl_kardex.id_movimiento_detalle', '=', 'detalle.id_movimiento_detalle')
        ->join('tbl_movimientos as movimiento', 'detalle.id_movimiento', '=', 'movimiento.id_movimiento')
        ->join('tbl_terceros as entrega', 'movimiento.id_tercero_entrega', 'entrega.id_tercero')
        ->join('tbl_terceros as recibe', 'movimiento.id_tercero_recibe', 'recibe.id_tercero')
        ->join('tbl_dominios as tm', 'movimiento.id_dominio_tipo_movimiento', 'tm.id_dominio')
        ->where(function ($q) use($option) {
            if($option == 1) {
                $this->dinamyFilters($q, [
                    'tbl_kardex.created_at' => 'created_at',
                    'tbl_kardex.documento' => 'documento',
                    'tbl_kardex.cantidad' => 'cantidad',
                    'tbl_kardex.valor_unitario' => 'valor_unitario',
                    'tbl_kardex.valor_total' => 'valor_total',
                ]);
            } else {
                $q->where('tbl_kardex.id_kardex', '=', '-1');
            }
        })->get();
    }

    public function export() {
        $headers = ['#', 'Fecha', 'Entrega', 'Recibe', 'Concepto', 'Movimiento', "Entra\nCantidad", "Entra\nValor Unitario",
            "Entra\nTotal", "Sale\nCantidad", "Sale\nValor unitario", "Sale\nTotal", "Saldo\nCantidad", "Saldo\nValor unitario", "Saldo\nTotal"
        ];

        return $this->excel->download(new ReportsExport($headers, $this->generateDownload(1)), 'Reporte Kardex.xlsx');
    }
}
