<?php

namespace App\Http\Controllers;

use App\Models\TblConsolidado;
use App\Models\TblDetalleConsolidado;
use App\Models\TblTercero;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ConsolidadoController extends Controller
{
    protected $filtros;
    protected $excel;

    public function __construct(Excel $excel)
    {
        $this->middleware('auth');
        $this->excel = $excel;
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
        $this->authorize('view', new TblConsolidado);
        return $this->getView('consolidados.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', new TblConsolidado);

        return view('consolidados._form', [
            'consolidado' => new TblConsolidado,
            'clientes' => TblTercero::where([
                'estado' => 1,
                'id_dominio_tipo_tercero' => session('id_dominio_representante_cliente')
            ])->where('id_responsable_cliente', '>', 0)->get(),
            'contratistas' => TblTercero::where([
                'estado' => 1,
                'id_dominio_tipo_tercero' => session('id_dominio_coordinador')
            ])->where('id_responsable_cliente', '>', 0)->get(),
            'detalle_consolidado' => TblDetalleConsolidado::where(['id_consolidado' => -1])->orderBy('id_detalle_consolidado', 'desc')->paginate(10)
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
     * @param  \App\Models\Consolidado  $consolidado
     * @return \Illuminate\Http\Response
     */
    public function show(TblConsolidado $consolidado)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Consolidado  $consolidado
     * @return \Illuminate\Http\Response
     */
    public function edit(TblConsolidado $consolidado)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Consolidado  $consolidado
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TblConsolidado $consolidado)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Consolidado  $consolidado
     * @return \Illuminate\Http\Response
     */
    public function destroy(TblConsolidado $consolidado)
    {
        //
    }

    public function grid() {
        return $this->getView('consolidados.grid');
    }

    private function getView($view) {
        $consolidado = new TblConsolidado;

        return view($view, [
            'model' => TblConsolidado::where(function ($q) {
                $this->dinamyFilters($q);
            })->orderBy('id_consolidado', 'desc')->paginate(10),

            'create' => Gate::allows('create', $consolidado),
            'edit' => Gate::allows('update', $consolidado),
            'view' => Gate::allows('view', $consolidado),
            'request' => $this->filtros
        ]);
    }
}
