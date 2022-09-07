<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveUsuarioRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Http\Controllers\MessagesController;
use App\Models\TblTercero;
use App\Models\TblUsuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class UsuarioController extends Controller
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
                } else if($key == 'full_name') {
                    $querybuilder->whereHas('tbltercero', function($q) use($value){
                        $q->where('nombres', 'like', strtolower("%$value%"));
                        $q->orwhere('apellidos', 'like', strtolower("%$value%"));
                    });
                }
            }
            $this->filtros[$key] = $value;
        }

        if(Auth::user()->role !== session('id_dominio_super_administrador')) {
            $value = session('id_dominio_super_administrador');
            $querybuilder->whereHas('tbltercero', function($q) use($value){
                $q->where('id_dominio_tipo_tercero', '!=', $value);
            });
        }

        if(!in_array(Auth::user()->role, [session('id_dominio_super_administrador'), session('id_dominio_administrador')])) {
            $value = session('id_dominio_super_administrador');
            $querybuilder->whereHas('tbltercero', function($q) use($value){
                $q->whereNotIn('id_dominio_tipo_tercero', [session('id_dominio_super_administrador'), session('id_dominio_administrador')]);
            });
        }

        return $querybuilder;
    }

    private function getAdminRoles() {
        return Auth::user()->role == session('id_dominio_super_administrador')
            ? [0]
            : (
                Auth::user()->role == session('id_dominio_administrador')
                    ? [session('id_dominio_super_administrador')]
                    : [session('id_dominio_super_administrador'), session('id_dominio_administrador')]
            );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view-user');

        return $this->getView('usuarios.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create-user');

        return view('usuarios._form', [
            'usuario' => new TblUsuario,
            'terceros' => TblTercero::where(['estado' => '1'])
                ->whereNotIn('id_dominio_tipo_tercero', $this->getAdminRoles())->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveUsuarioRequest $request)
    {
        try {
            $this->authorize('create-user');

            $user = TblUsuario::create($request->validated());
            $logo = $request->hasFile('logo') ? $request->file('logo')->store('images') : '';

            (new MessagesController)->newUser($request->email, $request->password);

            return response()->json([
                'success' => 'Usuario creado exitosamente!',
            ]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(TblUsuario $user)
    {
        $this->authorize('view-user');

        return view('usuarios._form', [
            'edit' => false,
            'usuario' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(TblUsuario $user)
    {
        $this->authorize('update-user');

        return view('usuarios._form', [
            'edit' => true,
            'usuario' => $user,
            'terceros' => TblTercero::where(['estado' => '1'])
                ->whereNotIn('id_dominio_tipo_tercero', $this->getAdminRoles())->get(),
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
    public function update(TblUsuario $user, UpdateUsuarioRequest $request)
    {
        try {
            $this->authorize('update-user');

            $password_anterior = trim($user->password);
            $password_nueva = trim($request->password);

            $user->update($request->validated());

            if($password_nueva !== '' && $password_nueva !== $password_anterior) {
                (new MessagesController)->changePassword($user->email, $password_nueva);
            }

            return response()->json([
                'success' => 'Usuario actualizado exitosamente!',
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

    public function password(TblUsuario $user) {
        return view('usuarios.password', [
            'usuario' => $user
        ]);
    }

    public function update_password(TblUsuario $user, UpdatePasswordRequest $request) {
        try {
            $user->update($request->validated());
            (new MessagesController)->changePassword($user->email, $request->password);

            return response()->json([
                'success' => 'Contraseña actualizada exitosamente!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ]);
        }
    }

    public function grid() {
        $this->authorize('view-user');

        return $this->getView('usuarios.grid');
    }

    private function getView($view) {
        return view($view, [
            'model' => TblUsuario::with(['tbltercero', 'tblusuario'])
                ->where(function ($q) {
                    $this->dinamyFilters($q);
                })->latest()->paginate(10),
            'create' => Gate::allows('create-user', TblUsuario::class),
            'edit' => Gate::allows('update-user', TblUsuario::class),
            'view' => Gate::allows('view-user', TblUsuario::class),
            'request' => $this->filtros,
        ]);
    }
}
