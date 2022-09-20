<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveActividadRequest;
use App\Models\TblActividad;
use App\Models\TblTercero;
use App\Models\TblUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ActividadController extends Controller
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

                $querybuilder->where($key, (count($operador) > 1 ? $operador[0] : 'like'), (count($operador) > 1 ? $operador[1] : strtolower("%$value%")));
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
        $this->authorize('view', new TblActividad);
        
        return $this->getView('actividades.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', new TblActividad);

        return view('actividades._form', [
            'activity' => new TblActividad,
            'create_client' => isset(TblUsuario::getPermisosMenu('clients.index')->create) ? TblUsuario::getPermisosMenu('clients.index')->create : false,
            'create_site' => isset(TblUsuario::getPermisosMenu('sites.index')->create) ? TblUsuario::getPermisosMenu('sites.index')->create : false,
            'contratistas' => TblTercero::where([
                'estado' => 1,
                'id_dominio_tipo_tercero' => session('id_dominio_coordinador')
            ])->where('id_responsable_cliente', '>', 0)->get(),

        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveActividadRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(TblActividad $activity)
    {
        $this->authorize('view', $activity);

        return view('actividades._form', [
            'activity' => $activity
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
        return $this->getView('actividades.grid');
    }

    private function getView($view) {
        $actividad = new TblActividad;

        return view($view, [
            'model' => TblActividad::with(['tblencargado', 'tblcliente', 'tbltipoactividad', 'tblmes',
                'tblestacion', 'tblpermiso', 'tblestadoactividad', 'tblcotizacion', 'tblordencompra',
                'tblinforme', 'tblreponsablecliente', 'tblmesconsolidado', 'tblfactura', 'tblusuario'])
                ->where(function($q) {
                    $this->dinamyFilters($q);
                })->latest()->paginate(10),

            'status' => [],
            'create' => Gate::allows('create', $actividad),
            'edit' => Gate::allows('update', $actividad),
            'view' => Gate::allows('view', $actividad),
            'request' => $this->filtros,
        ]);
    }
}
