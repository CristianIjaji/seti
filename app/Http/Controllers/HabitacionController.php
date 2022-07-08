<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveHabitacionRequest;
use App\Models\TblHabitacion;
use App\Models\TblTercero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class HabitacionController extends Controller
{
    protected $filtros;

    public function __construct()
    {
        $this->middleware('auth');
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

                if(array_search($key, array_keys($fields))) {
                    $key = $fields[$key];
                }

                if(!in_array($key, ['razon_social'])){
                    $querybuilder->where($key, (count($operador) > 1 ? $operador[0] : 'like'), (count($operador) > 1 ? $operador[1] : strtolower("%$value%")));
                } else if($key == 'razon_social') {
                    $querybuilder->whereHas('tbltercero', function($q) use($value){
                        $q->where('razon_social', 'like', strtolower("%$value%"));
                    });
                }
            }
            $this->filtros[$key] = $value;
        }

        if(in_array(Auth::user()->role, [session('id_dominio_asociado')])){
            $querybuilder->where('id_tercero_cliente', Auth::user()->id_usuario);
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
        $this->authorize('view', new TblHabitacion);

        return $this->getView('habitaciones.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', new TblHabitacion);

        return view('habitaciones._form', [
            'asociados' => DB::table('tbl_terceros', 't')
                ->join('tbl_usuarios as u', 't.id_tercero', '=', 'u.id_tercero')
                ->join('tbl_configuracion_cliente as cc', 't.id_tercero', '=', 'cc.id_tercero_cliente')
                ->select('t.id_tercero',
                    'cc.servicios',
                    DB::raw("CASE WHEN TRIM(t.razon_social) = '' THEN CONCAT(t.nombres, ' ', t.apellidos) ELSE t.razon_social END as nombre")
                )->where('t.estado', '=', 1)
                ->where('cc.servicios', 'LIKE', '%'.session('id_dominio_reserva_hotel').'%')
                ->get(),
            'habitacion' => new TblHabitacion,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveHabitacionRequest $request)
    {
        try {
            $habitacion = TblHabitacion::create($request->validated());
            $this->authorize('create', $habitacion);

            return response()->json([
                'success' => 'Habitación creado exitosamente!',
                'response' => [
                    'value' => $habitacion->id_habitacion,
                    'option' => $habitacion->nombre,
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
    public function show(TblHabitacion $room)
    {
        $this->authorize('view', $room);

        return view('habitaciones._form', [
            'edit' => false,
            'habitacion' => $room
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(TblHabitacion $room)
    {
        $this->authorize('update', $room);

        return view('habitaciones._form', [
            'edit' => true,
            'habitacion' => $room,
            'asociados' => DB::table('tbl_terceros', 't')
                ->join('tbl_usuarios as u', 't.id_tercero', '=', 'u.id_tercero')
                ->join('tbl_configuracion_cliente as cc', 't.id_tercero', '=', 'cc.id_tercero_cliente')
                ->select('t.id_tercero',
                    'cc.servicios',
                    DB::raw("CASE WHEN TRIM(t.razon_social) = '' THEN CONCAT(t.nombres, ' ', t.apellidos) ELSE t.razon_social END as nombre")
                )->where('t.estado', '=', 1)
                ->where('cc.servicios', 'LIKE', '%'.session('id_dominio_reserva_hotel').'%')
                ->get(),
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
    public function update(TblHabitacion $room, SaveHabitacionRequest $request)
    {
        try {
            $room->update($request->validated());
            $this->authorize('update', $room);

            return response()->json([
                'success' => 'Habitación actualizada correctamente!'
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
        return $this->getView('habitaciones.grid');
    }

    private function getView($view) {
        $habitacion = new TblHabitacion;

        return view($view, [
            'model' => TblHabitacion::with(['tbltercero', 'tblusuario'])
            ->where(function($q) {
                $this->dinamyFilters($q); 
            })->latest()->paginate(10),
            'create' => Gate::allows('create', $habitacion),
            'edit' => Gate::allows('update', $habitacion),
            'view' => Gate::allows('view', $habitacion),
            'request' => $this->filtros,
        ]);
    }
}
