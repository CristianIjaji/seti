<?php

namespace App\Http\Controllers;

use App\Models\TblConfiguracion;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $configuracion = TblConfiguracion::where('id_tercero_cliente', '=', Auth::user()->id_tercero)->first();
        return view('home', ['configuracion' => $configuracion]);
    }
}
