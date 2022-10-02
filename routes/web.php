<?php

use App\Http\Controllers\ActividadController;
use App\Http\Controllers\ConsolidadoController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\DominioController;
use App\Http\Controllers\EstadoCotizacionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListaPrecioController;
use App\Http\Controllers\MenuTipoTerceroController;
use App\Http\Controllers\MessagesController;
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

// Controlador terceros
Route::get('clients/export', [TerceroController::class, 'export'])->name('clients.export');
Route::get('clients/template', [TerceroController::class, 'download_template'])->name('clients.template');
Route::get('/clients/search', [TerceroController::class, 'search'])->name('clients.search');
Route::resource('clients', TerceroController::class);
Route::post('clients/grid', [TerceroController::class, 'grid'])->name('clients.grid');
Route::post('clients/import', [TerceroController::class, 'import'])->name('clients.import');

// Controlador puntos interes
Route::get('sites/export', [PuntosInteresController::class, 'export'])->name('sites.export');
Route::get('sites/template', [PuntosInteresController::class, 'download_template'])->name('sites.template');
Route::get('sites/{client}/get_puntos_interes_client', [PuntosInteresController::class, 'get_puntos_interes_client'])->name('sites.get_puntos_interes_client');
Route::resource('sites', PuntosInteresController::class);
Route::post('sites/grid', [PuntosInteresController::class, 'grid'])->name('sites.grid');
Route::post('sites/import', [PuntosInteresController::class, 'import'])->name('sites.import');

// Controlador lista de precios
Route::get('priceList/export', [ListaPrecioController::class, 'export'])->name('priceList.export');
Route::get('priceList/template', [ListaPrecioController::class, 'download_template'])->name('priceList.template');
Route::resource('priceList', ListaPrecioController::class);
Route::get('priceList/{type}/{client}', [ListaPrecioController::class, 'search'])->name('priceList.search');
Route::post('priceList/grid',[ListaPrecioController::class, 'grid'])->name('priceList.grid');
Route::post('priceList/import', [ListaPrecioController::class, 'import'])->name('priceList.import');

// Controlador cotizaciones
Route::get('quotes/exportQuote', [CotizacionController::class, 'exportQuote'])->name('quotes.exportQuote');
Route::get('quotes/export', [CotizacionController::class, 'export'])->name('quotes.export');
Route::get('quotes/template', [CotizacionController::class, 'download_template'])->name('quotes.template');
Route::get('quotes/{quote}/getquote', [CotizacionController::class, 'getCotizacion'])->name('quotes.getquote');
Route::resource('quotes', CotizacionController::class);
Route::post('quotes/grid', [CotizacionController::class, 'grid'])->name('quotes.grid');
Route::post('quotes/{quote}/handleQuote', [CotizacionController::class, 'handleQuote'])->name('quotes.handleQuote');
Route::post('quotes/import', [CotizacionController::class, 'import'])->name('quotes.import');

// Controlador estado cotizaciones
Route::resource('statequotes', EstadoCotizacionController::class);
Route::post('statequotes/grid', [EstadoCotizacionController::class, 'grid'])->name('statequotes.grid');

// Controlador de actividades
Route::resource('activities', ActividadController::class);
Route::post('activities/grid', [ActividadController::class, 'grid'])->name('activities.grid');

// Controlador de consolidado
Route::resource('deals', ConsolidadoController::class);
Route::post('deals/grid', [ConsolidadoController::class, 'grid'])->name('deals.grid');

// Controlador usuarios
Route::resource('users', UsuarioController::class);
Route::post('users/grid', [UsuarioController::class, 'grid'])->name('users.grid');
Route::post('users/{user}/update_password', [UsuarioController::class, 'update_password'])->name('users.update_password');
Route::get('users/{user}/password', [UsuarioController::class, 'password'])->name('users.password');

// Controlador menu tipo tercero
Route::resource('profiles', MenuTipoTerceroController::class);
Route::post('profiles/grid', [MenuTipoTerceroController::class, 'grid'])->name('profiles.grid');

// Controlador dominios
Route::resource('domains', DominioController::class);
Route::post('domains/grid', [DominioController::class, 'grid'])->name('domains.grid');

// Controlador parametros
Route::resource('params', ParametroController::class);
Route::post('params/grid', [ParametroController::class, 'grid'])->name('params.grid');

Auth::routes();