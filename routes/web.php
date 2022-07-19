<?php

use App\Http\Controllers\DominioController;
use App\Http\Controllers\HabitacionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListaPrecioController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\OrdenController;
use App\Http\Controllers\ParametroController;
use App\Http\Controllers\PuntosInteresController;
use App\Http\Controllers\TerceroController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::get('home', [HomeController::class, 'index'])->name('home.index');
Route::post('contact', [MessagesController::class, 'contact'])->name('contact');

// Controlador puntos interes
Route::resource('sites', PuntosInteresController::class);
Route::post('sites/grid', [PuntosInteresController::class, 'grid'])->name('sites.grid');

//Controlador lista de precios 
Route::resource('priceList', ListaPrecioController::class);
Route::post('priceList/grid',[ListaPrecioController::class, 'grid'])->name('priceList.grid');


// Controlador Ordenes
Route::get('orden/export', [OrdenController::class, 'export'])->name('orden.export');
Route::post('orden/{orden}/gestionarOrden', [OrdenController::class, 'gestionarOrden'])->name('orden.gestionarOrden');
Route::resource('orden', OrdenController::class);
Route::post('orden/grid', [OrdenController::class, 'grid'])->name('orden.grid');

// Controlador terceros
Route::get('clients/{id}/search_servicios', [TerceroController::class, 'search_servicios'])->name('clients.search_servicios');
Route::get('clients/{id}/{fecha_inicio}/{fecha_fin}/get_habitaciones', [TerceroController::class, 'get_habitaciones'])->name('clients.get_habitaciones');
Route::resource('clients', TerceroController::class);
Route::post('clients/grid', [TerceroController::class, 'grid'])->name('clients.grid');

// Controlador habitaciones
Route::resource('rooms', HabitacionController::class);
Route::post('rooms/grid', [HabitacionController::class, 'grid'])->name('rooms.grid');

// Controlador usuarios
Route::resource('users', UsuarioController::class);
Route::post('users/grid', [UsuarioController::class, 'grid'])->name('users.grid');
Route::post('users/{user}update_printer', [UsuarioController::class, 'update_printer'])->name('users.update_printer');
Route::get('users/{user}/printer', [UsuarioController::class, 'printer'])->name('users.printer');
Route::post('users/{user}/update_password', [UsuarioController::class, 'update_password'])->name('users.update_password');
Route::get('users/{user}/password', [UsuarioController::class, 'password'])->name('users.password');

// Controlador dominios
Route::resource('domains', DominioController::class);
Route::post('domains/grid', [DominioController::class, 'grid'])->name('domains.grid');

// Controlador parametros
Route::resource('params', ParametroController::class);
Route::post('params/grid', [ParametroController::class, 'grid'])->name('params.grid');

Auth::routes();