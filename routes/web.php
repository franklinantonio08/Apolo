<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

/*use App\Http\Controllers\Dist\DepartamentoController;
use App\Http\Controllers\Dist\TipoatencionController;
use App\Http\Controllers\Dist\PosicionesController;
use App\Http\Controllers\Dist\ColaboradoresController;
HEAD<<<<<<< 
use App\Http\Controllers\Dist\DashboardController;
use App\Http\Controllers\Dist\SolicitudController; */

use App\Http\Controllers\Dist\{
    DashboardController,
    SolicitudController,
    DepartamentoController,
    TipoatencionController,
    PosicionesController,
    ColaboradoresController,
    MotivoController,
    SubmotivoController
};
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
        Route::get('dist/test', [DepartamentosController::class, 'Index']) ->name('Index');  

        // solicitud

        Route::get('dist/solicitud', [SolicitudController::class, 'Index']) ->name('Index'); 
        Route::post('dist/solicitud', [SolicitudController::class, 'PostIndex']) ->name('PostIndex');
        Route::get('dist/missolicitudes/{Id}', [SolicitudController::class, 'Missolicitudes']) ->name('Missolicitudes'); 
        Route::post('dist/missolicitudes/{Id}', [SolicitudController::class, 'PostMissolicitudes']) ->name('PostMissolicitudes'); 

        Route::get('dist/solicitud/nuevo', [SolicitudController::class, 'Nuevo']) ->name('Nuevo'); 
        Route::post('dist/solicitud/nuevo', [SolicitudController::class, 'PostNuevo']) ->name('PostNuevo'); 
        Route::get('dist/solicitud/editar/{Id}', [SolicitudController::class, 'Editar']) ->name('Editar');
        Route::post('dist/solicitud/editar/{Id}', [SolicitudController::class, 'PostEditar']) ->name('PostEditar'); 
        Route::get('dist/solicitud/mostrar/{Id}', [SolicitudController::class, 'Mostrar']) ->name('Mostrar');
        Route::post('dist/solicitud/desactivar', [SolicitudController::class, 'Desactivar']) ->name('Desactivar'); 
        
        Route::post('dist/solicitud/nuevo/buscatipoatencion', [SolicitudController::class, 'postBuscatipoatencion']) ->name('postBuscatipoatencion');
        Route::post('dist/solicitud/nuevo/buscamotivo', [SolicitudController::class, 'postBuscamotivo']) ->name('postBuscamotivo');

        // Departamento
        Route::get('dist/departamento', [DepartamentoController::class, 'Index']) ->name('Index');  
        Route::post('dist/departamento', [DepartamentoController::class, 'PostIndex']) ->name('PostIndex'); 
        Route::get('dist/departamento/nuevo', [DepartamentoController::class, 'Nuevo']) ->name('Nuevo'); 
        Route::post('dist/departamento/nuevo', [DepartamentoController::class, 'PostNuevo']) ->name('PostNuevo'); 
        Route::get('dist/departamento/editar/{Id}', [DepartamentoController::class, 'Editar']) ->name('Editar');
        Route::post('dist/departamento/editar/{Id}', [DepartamentoController::class, 'PostEditar']) ->name('PostEditar'); 
        Route::get('dist/departamento/mostrar/{Id}', [DepartamentoController::class, 'Mostrar']) ->name('Mostrar');
        Route::post('dist/departamento/desactivar', [DepartamentoController::class, 'Desactivar']) ->name('Desactivar');
    
        // Tipo de Atencion
        Route::get('dist/tipoatencion', [TipoatencionController::class, 'Index']) ->name('Index');  
        Route::post('dist/tipoatencion', [TipoatencionController::class, 'PostIndex']) ->name('PostIndex'); 
        Route::get('dist/tipoatencion/nuevo', [TipoatencionController::class, 'Nuevo']) ->name('Nuevo'); 
        Route::post('dist/tipoatencion/nuevo', [TipoatencionController::class, 'PostNuevo']) ->name('PostNuevo'); 
        Route::get('dist/tipoatencion/editar/{Id}', [TipoatencionController::class, 'Editar']) ->name('Editar');
        Route::post('dist/tipoatencion/editar/{Id}', [TipoatencionController::class, 'PostEditar']) ->name('PostEditar'); 
        Route::get('dist/tipoatencion/mostrar/{Id}', [TipoatencionController::class, 'Mostrar']) ->name('Mostrar');
        Route::post('dist/tipoatencion/desactivar', [TipoatencionController::class, 'Desactivar']) ->name('Desactivar');

        // Motivo
        Route::get('dist/motivo', [MotivoController::class, 'Index']) ->name('Index');  
        Route::post('dist/motivo', [MotivoController::class, 'PostIndex']) ->name('PostIndex'); 
        Route::get('dist/motivo/nuevo', [MotivoController::class, 'Nuevo']) ->name('Nuevo'); 
        Route::post('dist/motivo/nuevo', [MotivoController::class, 'PostNuevo']) ->name('PostNuevo'); 
        Route::get('dist/motivo/editar/{Id}', [MotivoController::class, 'Editar']) ->name('Editar');
        Route::post('dist/motivo/editar/{Id}', [MotivoController::class, 'PostEditar']) ->name('PostEditar'); 
        Route::get('dist/motivo/mostrar/{Id}', [MotivoController::class, 'Mostrar']) ->name('Mostrar');
        Route::post('dist/motivo/desactivar', [MotivoController::class, 'Desactivar']) ->name('Desactivar');

        // SubMotivo
        Route::get('dist/submotivo', [SubmotivoController::class, 'Index']) ->name('Index');  
        Route::post('dist/submotivo', [SubmotivoController::class, 'PostIndex']) ->name('PostIndex'); 
        Route::get('dist/submotivo/nuevo', [SubmotivoController::class, 'Nuevo']) ->name('Nuevo'); 
        Route::post('dist/submotivo/nuevo', [SubmotivoController::class, 'PostNuevo']) ->name('PostNuevo'); 
        Route::get('dist/submotivo/editar/{Id}', [SubmotivoController::class, 'Editar']) ->name('Editar');
        Route::post('dist/submotivo/editar/{Id}', [SubmotivoController::class, 'PostEditar']) ->name('PostEditar'); 
        Route::get('dist/submotivo/mostrar/{Id}', [SubmotivoController::class, 'Mostrar']) ->name('Mostrar');
        Route::post('dist/submotivo/desactivar', [SubmotivoController::class, 'Desactivar']) ->name('Desactivar');

         //posiciones
         Route::get('dist/posiciones', [PosicionesController::class, 'Index']) ->name('Index'); 
         Route::post('dist/posiciones', [PosicionesController::class, 'PostIndex']) ->name('PostIndex'); 
         Route::get('dist/posiciones/nuevo', [PosicionesController::class, 'Nuevo']) ->name('Nuevo'); 
         Route::post('dist/posiciones/nuevo', [PosicionesController::class, 'PostNuevo']) ->name('PostNuevo'); 
         Route::get('dist/posiciones/editar/{Id}', [PosicionesController::class, 'Editar']) ->name('Editar');
         Route::post('dist/posiciones/editar/{Id}', [PosicionesController::class, 'PostEditar']) ->name('PostEditar'); 
         Route::get('dist/posiciones/mostrar/{Id}', [PosicionesController::class, 'Mostrar']) ->name('Mostrar');
         Route::post('dist/posiciones/desactivar', [PosicionesController::class, 'Desactivar']) ->name('Desactivar');

        //Colaboradores
        Route::get('dist/colaboradores', [ColaboradoresController::class, 'Index']) ->name('Index'); 
        Route::post('dist/colaboradores', [ColaboradoresController::class, 'PostIndex']) ->name('PostIndex'); 
        Route::get('dist/colaboradores/nuevo', [ColaboradoresController::class, 'Nuevo']) ->name('Nuevo'); 
        Route::post('dist/colaboradores/nuevo', [ColaboradoresController::class, 'PostNuevo']) ->name('PostNuevo'); 
        Route::get('dist/colaboradores/editar/{Id}', [ColaboradoresController::class, 'Editar']) ->name('Editar');
        Route::post('dist/colaboradores/editar/{Id}', [ColaboradoresController::class, 'PostEditar']) ->name('PostEditar'); 
        Route::get('dist/colaboradores/mostrar/{Id}', [ColaboradoresController::class, 'Mostrar']) ->name('Mostrar');
        Route::post('dist/colaboradores/desactivar', [ColaboradoresController::class, 'Desactivar']) ->name('Desactivar');

        Route::post('dist/colaboradores/nuevo/buscadistrito', [ColaboradoresController::class, 'postBuscadistrito']) ->name('postBuscadistrito');
        Route::post('dist/colaboradores/nuevo/buscaposiciones', [ColaboradoresController::class, 'postBuscaposiciones']) ->name('postBuscaposiciones');


        /*Route::get('dist/organizacion/importar', [OrganizacionController::class, 'Importar']) ->name('Importar'); 
        Route::post('dist/organizacion/importar', [OrganizacionController::class, 'PostImportar']) ->name('PostImportar'); 
*/

    });


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
