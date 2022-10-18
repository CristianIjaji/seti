<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveMenuTipoTerceroRequest;
use App\Models\TblDominio;
use App\Models\TblMenu;
use App\Models\TblMenuTipoTercero;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class MenuTipoTerceroController extends Controller
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

        if(Auth::user()->role !== session('id_dominio_super_administrador')) {
            $querybuilder->where('id_tipo_tercero', '<>', session('id_dominio_super_administrador'));
        }

        return $querybuilder;
    }

    private function savePermisosTercero() {
        TblMenuTipoTercero::where('id_tipo_tercero', '=', request()->id_tipo_tercero)->wherenotin('id_menu', request()->id_menu)->delete();

        foreach (request()->id_menu as $index => $valor) {
            $permiso = TblMenuTipoTercero::where(['id_tipo_tercero' => request()->id_tipo_tercero, 'id_menu' => request()->id_menu[$index]])->first();
            if(!$permiso) {
                $permiso = new TblMenuTipoTercero;
            }

            $permiso->id_menu = request()->id_menu[$index];
            $permiso->id_tipo_tercero = request()->id_tipo_tercero;
            $permiso->crear = isset(request()->crear[$index]) ? request()->crear[$index] : false;
            $permiso->editar = isset(request()->editar[$index]) ? request()->editar[$index] : false;
            $permiso->ver = isset(request()->ver[$index]) ? request()->ver[$index] : false;
            $permiso->importar = isset(request()->importar[$index]) ? request()->importar[$index] : false;
            $permiso->exportar = isset(request()->exportar[$index]) ? request()->exportar[$index] : false;

            $permiso->save();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view', new TblMenuTipoTercero);

        return $this->getView('menus.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', new TblMenuTipoTercero);

        return view('menus._form', [
            'profile' => new TblMenuTipoTercero,
            'tipo_terceros' => TblDominio::getListaDominios(session('id_dominio_tipo_tercero')),
            'menus_disponibles' => TblMenu::where('estado', '=', 1)
            ->wherenotin('id_menu', (Auth::user()->role !== session('id_dominio_super_administrador') ? [8, 9] : []))->orderBy('orden')->get(),
            'menus_asignados' => []
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveMenuTipoTerceroRequest $request)
    {
        try {
            $this->authorize('create', new TblMenuTipoTercero);

            $request->validated();
            $this->savePermisosTercero();

            return response()->json([
                'success' => 'Permiso creado exitosamente!',
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
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
    public function show(TblMenuTipoTercero $profile)
    {
        $this->authorize('view', $profile);

        return view('menus._form', [
            'edit' => false,
            'profile' => $profile,
            'tipo_terceros' => TblDominio::getListaDominios(session('id_dominio_tipo_tercero')),
            'menus_disponibles' => TblMenu::where('estado', '=', 1)
            ->wherenotin('id_menu', (Auth::user()->role !== session('id_dominio_super_administrador') ? [8, 9] : []))->orderBy('orden')->get(),
            'menus_asignados' => $this->getPermisosMenu($profile),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(TblMenuTipoTercero $profile)
    {
        $this->authorize('update', $profile);

        return view('menus._form', [
            'edit' => true,
            'profile' => $profile,
            'tipo_terceros' => TblDominio::getListaDominios(session('id_dominio_tipo_tercero')),
            'menus_disponibles' => TblMenu::where('estado', '=', 1)
            ->wherenotin('id_menu', (Auth::user()->role !== session('id_dominio_super_administrador') ? [8, 9] : []))->orderBy('orden')->get(),
            'menus_asignados' => $this->getPermisosMenu($profile),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SaveMenuTipoTerceroRequest $request, TblMenuTipoTercero $profile)
    {
        try {
            $this->authorize('update', $profile);

            $request->validated();
            $this->savePermisosTercero();

            return response()->json([
                'success' => 'Permiso actualizado exitosamente!',
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

    private function getPermisosMenu($profile) {
        $menus_asignados = [];
        $menus = TblMenu::select(
            'tbl_menus.id_menu',
            'tbl_menus.nombre',
            'tbl_menus.nombre as nombre_form',
            'tbl_menus.icon',
            'ttm.crear',
            'ttm.editar',
            'ttm.ver',
            'ttm.importar',
            'ttm.exportar'
        )->join('tbl_menu_tipo_tercero as ttm', 'tbl_menus.id_menu', '=', 'ttm.id_menu')
        ->where(['tbl_menus.estado' => 1, 'ttm.id_tipo_tercero' => $profile->id_tipo_tercero])
        ->orderBy('tbl_menus.orden')->get();

        foreach ($menus as $menu) {
            $menus_asignados[$menu->id_menu] = [
                'nombre' => $menu->nombre_form,
                'permisos' => [
                    "crear" => $menu->crear,
                    "editar" => $menu->editar,
                    "ver" => $menu->ver,
                    "importar" => $menu->importar,
                    "exportar" => $menu->exportar
                ]
            ];
        }

        return $menus_asignados;
    }

    public function grid() {
        return $this->getView('menus.grid');
    }

    private function getView($view) {
        $menu = new TblMenuTipoTercero;

        return view($view, [
            'model' => TblMenuTipoTercero::select(
                DB::raw("
                    tbl_menu_tipo_tercero.id_tipo_tercero,
                    d.nombre as tipo_tercero,
                    min(tbl_menu_tipo_tercero.id_menu_tipo_tercero) as id_menu_tipo_tercero
                ")
            )->join('tbl_dominios as d', 'tbl_menu_tipo_tercero.id_tipo_tercero', '=', 'd.id_dominio')
            ->where(function ($q) {
                $this->dinamyFilters($q);
            })->groupBy('id_tipo_tercero', 'd.nombre')
            ->orderBy('d.nombre', 'asc')->paginate(10),
            'tipo_terceros' => TblDominio::getListaDominios(session('id_dominio_tipo_tercero')),
            'create' => Gate::allows('create', $menu),
            'edit' => Gate::allows('update', $menu),
            'view' => Gate::allows('view', $menu),
            'request' => $this->filtros,
        ]);
    }
}
