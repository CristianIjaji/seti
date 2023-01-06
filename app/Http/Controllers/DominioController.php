<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveDominioRequest;
use App\Models\TblDominio;
use Illuminate\Support\Facades\Gate;

class DominioController extends Controller
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
        $this->authorize('view', new TblDominio);

        return $this->getView('dominios.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', new TblDominio);

        return view('dominios._form', [
            'dominio' => new TblDominio,
            'dominios_padre' => TblDominio::where(['id_dominio_padre' => null])->pluck('nombre', 'id_dominio'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveDominioRequest $request)
    {
        try {
            $this->authorize('create', new TblDominio);
            $dominio = TblDominio::create($request->validated());

            return response()->json([
                'success' => 'Dominio creado exitosamente!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(TblDominio $domain)
    {
        $this->authorize('view', $domain);

        return view('dominios._form', [
            'edit' => false,
            'dominio' => $domain
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(TblDominio $domain)
    {
        $this->authorize('update', $domain);

        return view('dominios._form', [
            'edit' => true,
            'dominio' => $domain,
            'dominios_padre' => TblDominio::where(['estado' => 1, 'id_dominio_padre' => null])
                ->orderby('nombre')
                ->pluck('nombre', 'id_dominio'),
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
    public function update(TblDominio $domain, SaveDominioRequest $request)
    {
        try {
            $this->authorize('update', $domain);
            $domain->update($request->validated());

            return response()->json([
                'success' => 'Dominio actualizado correctamente!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage(),
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
        return $this->getView('dominios.grid');
    }

    private function getView($view) {
        return view($view, [
            'model' => TblDominio::with(['tblusuario', 'tbldominio'])
                ->where(function ($q) {
                    $this->dinamyFilters($q);
                })->orderBy('id_dominio', 'desc')->paginate(10),
            'dominios_padre' => TblDominio::where(['estado' => 1, 'id_dominio_padre' => null])->orderby('nombre')->pluck('nombre', 'id_dominio'),
            'create' => Gate::allows('create', new TblDominio),
            'edit' => Gate::allows('update', new TblDominio),
            'view' => Gate::allows('view', new TblDominio),
            'request' => $this->filtros,
        ]);
    }
}
