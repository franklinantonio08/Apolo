<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

use App\Http\Controllers\Dist\DepartamentoController;
use App\Http\Controllers\Dist\PosicionesController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
    Route::get('/', function () {
        return view('welcome');
    }); 


*/



Route::middleware('guest')->group(function () {
    Route::get('/', [AuthenticatedSessionController::class, 'create'])
    ->name('login');
    });


    Route::middleware('auth')->group(function () {

    

        //departamento
        Route::get('dist/departamento', [DepartamentoController::class, 'Index']) ->name('Index'); 
        Route::post('dist/departamento', [DepartamentoController::class, 'PostIndex']) ->name('PostIndex'); 
        Route::get('dist/departamento/nuevo', [DepartamentoController::class, 'Nuevo']) ->name('Nuevo'); 
        Route::post('dist/departamento/nuevo', [DepartamentoController::class, 'PostNuevo']) ->name('PostNuevo'); 
        Route::get('dist/departamento/editar/{Id}', [DepartamentoController::class, 'Editar']) ->name('Editar');
        Route::post('dist/departamento/editar/{Id}', [DepartamentoController::class, 'PostEditar']) ->name('PostEditar'); 
        Route::get('dist/departamento/mostrar/{Id}', [DepartamentoController::class, 'Mostrar']) ->name('Mostrar');
        Route::post('dist/departamento/desactivar', [DepartamentoController::class, 'Desactivar']) ->name('Desactivar');
    

         //posiciones
         Route::get('dist/posiciones', [PosicionesController::class, 'Index']) ->name('Index'); 
         Route::post('dist/posiciones', [PosicionesController::class, 'PostIndex']) ->name('PostIndex'); 
         Route::get('dist/posiciones/nuevo', [PosicionesController::class, 'Nuevo']) ->name('Nuevo'); 
         Route::post('dist/posiciones/nuevo', [PosicionesController::class, 'PostNuevo']) ->name('PostNuevo'); 
         Route::get('dist/posiciones/editar/{Id}', [PosicionesController::class, 'Editar']) ->name('Editar');
         Route::post('dist/posiciones/editar/{Id}', [PosicionesController::class, 'PostEditar']) ->name('PostEditar'); 
         Route::get('dist/posiciones/mostrar/{Id}', [PosicionesController::class, 'Mostrar']) ->name('Mostrar');
         Route::post('dist/posiciones/desactivar', [PosicionesController::class, 'Desactivar']) ->name('Desactivar');



        /*Route::get('dist/organizacion/importar', [OrganizacionController::class, 'Importar']) ->name('Importar'); 
        Route::post('dist/organizacion/importar', [OrganizacionController::class, 'PostImportar']) ->name('PostImportar'); 
*/

    });


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
