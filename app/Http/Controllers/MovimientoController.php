<?php

namespace App\Http\Controllers;

use App\Models\TblDominio;
use App\Models\TblMovimiento;
use App\Models\TblTercero;
use Illuminate\Http\Request;
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

                if(!in_array($key, ['full_name'])){
                    $querybuilder->where($key, (count($operador) > 1 ? $operador[0] : 'like'), (count($operador) > 1 ? $operador[1] : strtolower("%$value%")));
                } else if($key == 'full_name' && $value) {
                    // $querybuilder->whereHas('tblterceroalmacen', function($q) use($value){
                    //     $q->where('tbl_terceros.razon_social', 'like', strtolower("%$value%"));
                    //     $q->orwhere('tbl_terceros.nombres', 'like', strtolower("%$value%"));
                    //     $q->orwhere('tbl_terceros.apellidos', 'like', strtolower("%$value%"));
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
                ->get()
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
                ->get()
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
            // 'entregan' => TblTercero::,
            // 'reciben' => [],
            'export' => Gate::allows('export', $movimiento),
            'import' => Gate::allows('import', $movimiento),
            'create' => Gate::allows('create', $movimiento),
            'edit' => Gate::allows('update', $movimiento),
            'view' => Gate::allows('view', $movimiento),
            'request' => $this->filtros
        ]);
    }

    private function generateDownload($option) {

    }

    public function export() {

    }

    public function downloadTemplate() {

    }

    public function import() {

    }
}
