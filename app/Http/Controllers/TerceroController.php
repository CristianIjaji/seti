<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveTerceroRequest;
use App\Models\TblDominio;
use App\Models\TblTercero;
use App\Models\TblUsuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TerceroController extends Controller
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
                    $querybuilder->where('nombres', 'like', strtolower("%$value%"));
                    $querybuilder->orWhere('apellidos', 'like', strtolower("%$value%"));
                }
            }
            $this->filtros[$key] = $value;
        }

        if(Auth::user()->role !== session('id_dominio_super_administrador')) {
            $querybuilder->where('id_dominio_tipo_tercero', '<>', session('id_dominio_super_administrador'));
        }

        if(!in_array(Auth::user()->role, [session('id_dominio_super_administrador'), session('id_dominio_administrador')])) {
            $querybuilder->whereNotIn('id_dominio_tipo_tercero', [session('id_dominio_super_administrador'), session('id_dominio_administrador')]);
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
            'tipo_documentos' => TblDominio::getListaDominios(session('id_dominio_tipo_documento')),
            'tipo_terceros' => TblDominio::getListaDominios(session('id_dominio_tipo_tercero')),
            'tipo_tercero' => isset(request()->tipo_tercero)
                ? TblDominio::where('id_dominio', '=', request()->tipo_tercero)->first() :
                '',
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
            $tercero = TblTercero::create($request->validated());
            $this->authorize('create', $tercero);

            return response()->json([
                'success' => 'Tercero creado exitosamente!',
                'response' => [
                    'value' => $tercero->id_tercero,
                    'option' => $tercero->nombres.' '.$tercero->apellidos,
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
            'tipo_terceros' => TblDominio::getListaDominios(session('id_dominio_tipo_tercero')),
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
            $client->update($request->validated());
            $this->authorize('update', $client);

            $usuario = TblUsuario::where('id_tercero', '=', $client->id_tercero)->get()->first();
            if($usuario) {
                $usuario->email = $client->correo;
                $usuario->save();
            }

            return response()->json([
                'success' => 'Tercero actualizado correctamente!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
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
        return $this->getView('terceros.grid');
    }

    private function getView($view) {
        $tercero = new TblTercero;

        return view($view, [
            'model' => TblTercero::with(['tbldominiodocumento', 'tbldominiotercero', 'tbluser', 'tblusuario'])
                ->where(function ($q) {
                    $this->dinamyFilters($q);
                })->latest()->paginate(10),
            'tipo_terceros' => TblDominio::getListaDominios(session('id_dominio_tipo_tercero')),
            'create' => Gate::allows('create', $tercero),
            'edit' => Gate::allows('update', $tercero),
            'view' => Gate::allows('view', $tercero),
            'request' => $this->filtros,
        ]);
    }
}
