<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveMenuTipoTerceroRequest;
use App\Models\TblDominio;
use App\Models\TblMenuTipoTercero;
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

        if(auth()->user()->role !== session('id_dominio_super_administrador')) {
            $querybuilder->where('id_dominio_tipo_tercero', '<>', session('id_dominio_super_administrador'));
        }

        return $querybuilder;
    }

    private function savePermisosTercero() {
        TblMenuTipoTercero::where('id_dominio_tipo_tercero', '=', request()->id_dominio_tipo_tercero)->wherenotin('id_menu', request()->id_menu)->delete();

        foreach (request()->id_menu as $index => $valor) {
            $permiso = TblMenuTipoTercero::where(['id_dominio_tipo_tercero' => request()->id_dominio_tipo_tercero, 'id_menu' => request()->id_menu[$index]])->first();
            if(!$permiso) {
                $permiso = new TblMenuTipoTercero;
            }

            $permiso->id_menu = request()->id_menu[$index];
            $permiso->id_dominio_tipo_tercero = request()->id_dominio_tipo_tercero;
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

    private function getMenusDisponibles() {
        $menus = DB::table('tbl_menus', 'm')
        ->select('m.id_menu', 'm.url', 'm.icon', 'm.nombre', 'm.id_menu_padre', 'm.orden')
        ->where(['m.estado' => 1])
        ->wherenotin('m.id_menu', (auth()->user()->role !== session('id_dominio_super_administrador') ? [8, 9] : []))
        ->orderBy(DB::raw('COALESCE(id_menu_padre, orden)', 'asc'))
        ->get();
        
        $menus_disponibles = [];

        foreach ($menus as $menu) {
            if(!isset($menus_disponibles[$menu->id_menu]) && !$menu->id_menu_padre) {
                $menus_disponibles[$menu->id_menu] = [];
                $menus_disponibles[$menu->id_menu] = $menu;
            }

            if($menu->id_menu_padre && isset($menus_disponibles[$menu->id_menu_padre])) {
                if(!isset($menus_disponibles[$menu->id_menu_padre]->submenu)) {
                    $menus_disponibles[$menu->id_menu_padre]->submenu = [];
                }

                $menus_disponibles[$menu->id_menu_padre]->submenu[] = $menu;
            }
        }

        return $menus_disponibles;
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
            'menus_disponibles' => $this->getMenusDisponibles(),
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
            DB::beginTransaction();

            $request->validated();
            $this->savePermisosTercero();

            DB::commit();
            return response()->json([
                'success' => 'Permiso creado exitosamente!',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Error creando perfil: ".$th->__toString());
            return response()->json([
                'errors' => "Error creando perfil."
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
            'menus_disponibles' => $this->getMenusDisponibles(),
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
            'menus_disponibles' => $this->getMenusDisponibles(),
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

            DB::beginTransaction();
            $request->validated();
            $this->savePermisosTercero();

            DB::commit();
            return response()->json([
                'success' => 'Permiso actualizado exitosamente!',
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Error editando perfil: ".$th->__toString());
            return response()->json([
                'errors' => 'Error editando perfil.'
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
        return TblMenuTipoTercero::where(['id_dominio_tipo_tercero' => $profile->id_dominio_tipo_tercero])->get();
    }

    public function grid() {
        return $this->getView('menus.grid');
    }

    private function getView($view) {
        $menu = new TblMenuTipoTercero;

        return view($view, [
            'model' => TblMenuTipoTercero::select(
                DB::raw("
                    tbl_menu_tipo_tercero.id_dominio_tipo_tercero,
                    d.nombre as tipo_tercero,
                    min(tbl_menu_tipo_tercero.id_menu_tipo_tercero) as id_menu_tipo_tercero
                ")
            )->join('tbl_dominios as d', 'tbl_menu_tipo_tercero.id_dominio_tipo_tercero', '=', 'd.id_dominio')
            ->where(function ($q) {
                $this->dinamyFilters($q);
            })->groupBy('id_dominio_tipo_tercero', 'd.nombre')
            ->orderBy('d.nombre', 'asc')->paginate(10),
            'tipo_terceros' => TblDominio::getListaDominios(session('id_dominio_tipo_tercero')),
            'create' => Gate::allows('create', $menu),
            'edit' => Gate::allows('update', $menu),
            'view' => Gate::allows('view', $menu),
            'request' => $this->filtros,
        ]);
    }
}
