<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

use App\Models\RIDMigrantes;
use App\Models\RIDPaises;

use DB;
use Excel;

class RIDMigrantesController extends Controller
{
    private $request;
    private $common;

    public function __construct(Request $request){
        $this->request = $request;
    }

    public function Index(){

        return \view('admin/RIDmigrantes/index');
    }      
    
    public function PostIndex(){    
    
        $request = $this->request->all();

        //return $request;
        
        $columnsOrder = isset($request['order'][0]['column']) ? $request['order'][0]['column'] : '0';
        $orderBy=isset($request['columns'][$columnsOrder]['data']) ? $request['columns'][$columnsOrder]['data'] : 'id';
        $order = isset($request['order'][0]['dir']) ? $request['order'][0]['dir'] : 'ASC';
        $length = isset($request['length']) ? $request['length'] : '15';

        $currentPage = $request['currentPage'];  
        Paginator::currentPageResolver(function() use ($currentPage){
            return $currentPage;
        });

        $query = DB::table('RID_migrante')
        ->leftjoin('rid_puestocontrol', 'RID_migrante.puestoId', '=', 'rid_puestocontrol.id')
        ->leftjoin('rid_paises', 'RID_migrante.paisId', '=', 'rid_paises.id')
        ->leftjoin('rid_regiones', 'rid_paises.region_id', '=', 'rid_regiones.id')
        ->leftjoin('rid_afinidad', 'RID_migrante.afinidadId', '=', 'rid_afinidad.id')

            ->select(
                    'RID_migrante.*', 
                    'rid_puestocontrol.descripcion AS puesto_control',
                    'rid_paises.pais AS pais',
                    'rid_regiones.continente AS region_continente',
                    'rid_regiones.region AS region',
                    'rid_afinidad.descripcion AS afinidad'
                    )
            ->orderBy($orderBy,$order);

        if(isset($request['searchInput']) && trim($request['searchInput']) != ""){
            $query->where(
                function ($query) use ($request) {
                    $query->orWhere('RID_migrante.descripcion', 'like', '%'.trim($request['searchInput']).'%');
                    $query->orWhere('RID_migrante.codigo', 'like', '%'.trim($request['searchInput']).'%');
                }
                );		 
        }
            
        $migrantes = $query->paginate($length); 
    
        $result = $migrantes->toArray();
        $data = array();
        foreach($result['data'] as $value){

            // if($value->estatus == 'Activo'){
            //     $detalle = '<a href="/admin/RIDmigrantes/mostrar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-warning text-white m-b-5"> <i class="bi bi-eye"></i> </a>
            //                     <a href="/admin/RIDmigrantes/editar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-secondary text-white m-b-5"> <i class="bi bi-pencil"></i> </a>
            //                     <!--a href="#" attr-id="'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-danger text-white m-b-5 desactivar"> <i class="bi bi-trash"></i> </a-->';
            // }else{
                $detalle = '<a href="/admin/RIDmigrantes/mostrar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-warning text-white m-b-5"> <i class="bi bi-eye"></i> </a>
                                <a href="/admin/RIDmigrantes/editar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-secondary text-white m-b-5"> <i class="bi bi-pencil"></i> </a>
                                <!--a href="#" attr-id="'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-primary text-white m-b-5 desactivar"> <i class="bi bi-check2-square"></i> </a-->';
            //}

            $data[] = array(
                    "DT_RowId" => $value->id,
                    "id" => $value->id,
                    "nombre"=> $value->primerNombre . ' ' . $value->primerApellido,                         
                    "codigo"=> $value->documento,
                    "genero" => $value->genero,
                    "tipo" => $value->tipo,
                    "pais"=> $value->pais,
                    "region"=> $value->region,
                    "puesto_control"=> $value->puesto_control,
                    "afinidad"=> $value->afinidad,                          
                    "detalle"=> $detalle
            );
        }

        $response = array(
                'draw' => isset($request['draw']) ? $request['draw'] : '1',
                'recordsTotal' => $result['total'],
                'recordsFiltered' => $result['total'],
                'data' => $data,
            );
        
        return response()->json($response);    
    
    }
        
    public function Nuevo(){
        
        /*if(!$this->common->usuariopermiso('004')){
            return redirect('dist/dashboard')->withErrors($this->common->message);
        }*/

        $rid_paises = DB::table('rid_paises')
    	->where('estatus', '=', 'Activo')
    	->select('id', 'pais', 'region_id' , 'nacionalidad')
		->get();

		if(empty($rid_paises)){
    		return redirect('admin/RIDmigrantes')->withErrors("ERROR REGISTRO DE MIGRANTES ESTA VACIA CODE-0017");
    	}	
		view()->share('RIDPaises', $rid_paises);	

        $rid_puestocontrol = DB::table('rid_puestocontrol')
    	->where('estatus', '=', 'Activo')
    	->select('id', 'descripcion')
		->get();

		if(empty($rid_puestocontrol)){
    		return redirect('admin/RIDmigrantes')->withErrors("ERROR REGISTRO DE MIGRANTES ESTA VACIA CODE-0018");
    	}	
		view()->share('RIDPuestocontrol', $rid_puestocontrol);	

        $rid_estaciontemporal = DB::table('rid_estaciontemporal')
    	->where('estatus', '=', 'Activo')
    	->select('id', 'descripcion')
		->get();

		if(empty($rid_estaciontemporal)){
    		return redirect('admin/RIDmigrantes')->withErrors("ERROR REGISTRO DE MIGRANTES ESTA VACIA CODE-0018");
    	}	
		view()->share('RIDEstaciontemporal', $rid_estaciontemporal);	

        $rid_afinidad = DB::table('rid_afinidad')
    	->where('estatus', '=', 'Activo')
    	->select('id', 'descripcion')
		->get();

		if(empty($rid_afinidad)){
    		return redirect('admin/RIDmigrantes')->withErrors("ERROR REGISTRO DE MIGRANTES ESTA VACIA CODE-0019");
    	}	
		view()->share('RIDAfinidad', $rid_afinidad);	



        
        return \View::make('admin/RIDmigrantes/nuevo');

    }
        
    public function postNuevo(){
        
        /*if(!$this->common->usuariopermiso('004')){
            return redirect('dist/dashboard')->withErrors($this->common->message);
        }*/

        //return $this->request->all();

        $RIDMigrantesExiste = RIDMigrantes::where('documento', $this->request->documento)
        //->where('distribuidorId', Auth::user()->distribuidorId)
        ->first();

        if(!empty($RIDMigrantesExiste)){
            return redirect('admin/RIDmigrantes/nuevo')->withErrors("ERROR AL GUARDAR REGISTRO DE MIGRANTES CODE-0020");
        }

        DB::beginTransaction();
        try { 
            
            $nacionalidad = RIDPaises::where('id', $this->request->nacionalidad)      
            ->first();

            $fechaNacimiento = $this->request->fechaNacimiento;

            // Convierte la fecha de nacimiento a un objeto DateTime
            $fechaNacimientoDateTime = new \DateTime($fechaNacimiento);

            // Obtén la fecha actual
            $fechaActual = new \DateTime();

            // Calcula la diferencia de años entre la fecha de nacimiento y la fecha actual
            $diferencia = $fechaActual->diff($fechaNacimientoDateTime);

            $RIDMigrantes = new RIDMigrantes;
            $RIDMigrantes->primerNombre             = trim($this->request->primerNombre);
            $RIDMigrantes->segundoNombre            = trim($this->request->segundoNombre);
            $RIDMigrantes->primerApellido           = trim($this->request->primerApellido);
            $RIDMigrantes->segundoApellido          = trim($this->request->segundoApellido);
            $RIDMigrantes->fechaNacimiento          = $fechaNacimientoDateTime->format('Y-m-d'); //trim($fechaNacimientoDateTime);
            $RIDMigrantes->documento                = trim($this->request->documento);
            $RIDMigrantes->regionId                 = trim($nacionalidad->region_id);
            $RIDMigrantes->paisId                   = trim($this->request->pais);
            $RIDMigrantes->nacionalidadId           = trim($this->request->nacionalidad);
            $RIDMigrantes->genero                   = trim($this->request->genero);

            if ($diferencia->y >= 18) {
                $RIDMigrantes->tipo                 = 'Adulto';
            } else {
                $RIDMigrantes->tipo                 = 'Menor';
            }            

            $RIDMigrantes->puestoId                 = trim($this->request->puestoControl);
            $RIDMigrantes->estacionTemporalId       = trim($this->request->estacionTemporal);
            $RIDMigrantes->afinidadId               = trim($this->request->afinidad);

            if(isset($this->request->comentario)){
                $RIDMigrantes->infoextra            = trim($this->request->comentario); 
            }
             
            $RIDMigrantes->estatus                  = 'Pendiente';
            $RIDMigrantes->created_at               = date('Y-m-d H:i:s');
            $RIDMigrantes->usuarioId                = Auth::user()->id; 
            
            $result = $RIDMigrantes->save();

            $RIDMigrantesId = $RIDMigrantes->id;

            if(empty($RIDMigrantesId)){
                DB::rollBack();
                return redirect('admin/RIDmigrantes/nuevo')->withErrors("ERROR AL GUARDAR EL REGISTRO DE MIGRANTES CODE-0021");
            }
            
            $RIDMigrantesCode = str_pad($RIDMigrantesId,5, "0",STR_PAD_LEFT);
            //return $RIDMigrantesCode;
            $RIDMigrantesUpdate = RIDMigrantes::find($RIDMigrantesId);
            $RIDMigrantesUpdate->codigo = $RIDMigrantesCode;
            $result = $RIDMigrantesUpdate->save();	

        } catch(\Illuminate\Database\QueryException $ex){ 
            DB::rollBack();
            return redirect('admin/RIDmigrantes/nuevo')->withErrors('ERROR AL GUARDAR EL REGISTRO DE MIGRANTES  CODE-0022'.$ex);
        }
        
        if($result != 1){
            DB::rollBack();
            return redirect('admin/RIDmigrantes/nuevo')->withErrors("ERROR AL GUARDAR EL REGISTRO DE MIGRANTES  CODE-0023");
        }
        DB::commit();

        return redirect('admin/RIDmigrantes')->with('alertSuccess', 'EL REGISTRO DE MIGRANTES HA SIDO INGRESADA');
    }

    public function Editar($migranteId) {
        $RID_migrante = DB::table('RID_migrante')
            ->where('RID_migrante.id', '=', $migranteId)
            ->leftJoin('rid_puestocontrol', 'RID_migrante.puestoId', '=', 'rid_puestocontrol.id')
            ->leftJoin('rid_paises as nacionalidad', 'RID_migrante.nacionalidadId', '=', 'nacionalidad.id')
            ->leftJoin('rid_paises', 'RID_migrante.paisId', '=', 'rid_paises.id')
            ->leftJoin('rid_regiones', 'rid_paises.region_id', '=', 'rid_regiones.id')
            ->leftJoin('rid_afinidad', 'RID_migrante.afinidadId', '=', 'rid_afinidad.id')
            ->select('RID_migrante.*', 'rid_afinidad.descripcion as afinidad', 'rid_regiones.region', 'rid_paises.pais', 'nacionalidad.nacionalidad', 'rid_puestocontrol.descripcion as puestoControl')
            ->first();
    
        if (empty($RID_migrante)) {
            return redirect('admin/RIDmigrantes')->withErrors("ERROR REGISTRO DE MIGRANTES NO EXISTE CODE-0024");
        }
    
        if (!empty($RID_migrante->fechaNacimiento)) {
            $fechaNacimiento = new \DateTime($RID_migrante->fechaNacimiento);
            $RID_migrante->fechaNacimiento = $fechaNacimiento->format('Y-m-d');
        }
    
        $paises = DB::table('rid_paises')->orderBy('pais')->get();
        $puestosControl = DB::table('rid_puestocontrol')->orderBy('descripcion')->get();
        $afinidades = DB::table('rid_afinidad')->orderBy('descripcion')->get();
    
        return view('admin/RIDmigrantes/editar', [
            'RIDmigrantes' => $RID_migrante,
            'paises' => $paises,
            'puestosControl' => $puestosControl,
            'afinidades' => $afinidades
        ]);
    }
    
    
    

    public function PostEditar(){
        /*if(!$this->common->usuariopermiso('004')){
            return redirect('admin/dashboard')->withErrors($this->common->message);
        }*/

        //$request = $this->request->all();

        //return $request;

        $migranteId = $this->request->migranteId;

        $RIDMigrantesExiste = RIDMigrantes::where('id', $migranteId)
        //->where('distribuidorId', Auth::user()->distribuidorId)
        ->first();

        if(empty($RIDMigrantesExiste)){
            return redirect('admin/RIDmigrantes/editar/'.$migranteId)->withErrors("ERROR AL GUARDAR REGISTRO DE MIGRANTES CODE-0025");
        }


        DB::beginTransaction();
        try { 


            $nacionalidad = RIDPaises::where('id', $this->request->nacionalidad)      
            ->first();

            $fechaNacimiento = $this->request->fechaNacimiento;

            // Convierte la fecha de nacimiento a un objeto DateTime
            $fechaNacimientoDateTime = new \DateTime($fechaNacimiento);

            // Obtén la fecha actual
            $fechaActual = new \DateTime();

            // Calcula la diferencia de años entre la fecha de nacimiento y la fecha actual
            $diferencia = $fechaActual->diff($fechaNacimientoDateTime);


            $migrantesUpdate = RIDMigrantes::find($migranteId);

            $migrantesUpdate->primerNombre              = trim($this->request->primerNombre);
            $migrantesUpdate->segundoNombre             = trim($this->request->segundoNombre);
            $migrantesUpdate->primerApellido            = trim($this->request->primerApellido);
            $migrantesUpdate->segundoApellido           = trim($this->request->segundoApellido);
            $migrantesUpdate->fechaNacimiento           = $fechaNacimientoDateTime->format('Y-m-d'); //trim($fechaNacimientoDateTime);
            $migrantesUpdate->documento                 = trim($this->request->documento);
            $migrantesUpdate->genero                    = trim($this->request->genero);
            $migrantesUpdate->afinidadId                = trim($this->request->afinidad);
            $migrantesUpdate->regionId                  = trim($nacionalidad->region_id);
            $migrantesUpdate->paisId                    = trim($this->request->pais);
            $migrantesUpdate->nacionalidadId            = trim($this->request->nacionalidad);
            $migrantesUpdate->puestoId                  = trim($this->request->puestoControl);
            $migrantesUpdate->infoextra                 = $this->request->comentario;
            
            $result = $migrantesUpdate->save();

        } catch(\Illuminate\Database\QueryException $ex){ 
            DB::rollBack();
            return redirect('admin/RIDmigrantes/editar/'.$migranteId)->withErrors('ERROR AL GUARDAR EL REGISTRO DE MIGRANTES  CODE-0026');
        }
        
        if($result != 1){
            DB::rollBack();
            return redirect('admin/RIDmigrantes/editar/'.$migranteId)->withErrors("ERROR AL GUARDAR EL REGISTRO DE MIGRANTES  CODE-0027");
        }
        DB::commit();


        return redirect('admin/RIDmigrantes/')->with('alertSuccess', 'EL REGISTRO DE MIGRANTES HA SIDO EDITADO');
    }

    public function Mostrar($migranteId){
        /*if(!$this->common->usuariopermiso('004')){
            return redirect('admin/dashboard')->withErrors($this->common->message);
        }*/
    
        $RID_migrante = DB::table('RID_migrante')
            ->where('RID_migrante.id', '=', $migranteId)
            ->leftJoin('rid_puestocontrol', 'RID_migrante.puestoId', '=', 'rid_puestocontrol.id')
            ->leftJoin('rid_paises as nacionalidad', 'RID_migrante.nacionalidadId', '=', 'nacionalidad.id')
            ->leftJoin('rid_paises', 'RID_migrante.paisId', '=', 'rid_paises.id')
            ->leftJoin('rid_regiones', 'rid_paises.region_id', '=', 'rid_regiones.id')
            ->leftJoin('rid_afinidad', 'RID_migrante.afinidadId', '=', 'rid_afinidad.id')
            ->select('RID_migrante.*', 'rid_afinidad.descripcion as afinidad', 'rid_regiones.region', 'rid_paises.pais', 'nacionalidad.nacionalidad')
            ->first();
    
        if(empty($RID_migrante)){
            return redirect('admin/RIDafinidad')->withErrors("ERROR EL REGISTRO DE MIGRANTES NO EXISTE CODE-0028");
        }
    
        if (!empty($RID_migrante->fechaNacimiento)) {
            $fechaNacimiento = new \DateTime($RID_migrante->fechaNacimiento);
            $RID_migrante->fechaNacimiento = $fechaNacimiento->format('Y-m-d');
        }
    
        view()->share('RIDmigrantes', $RID_migrante);
    
        return \View::make('admin/RIDmigrantes/mostrar');
    }
    
            
}
