<?php

namespace App\Http\Controllers\Dist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

use App\Models\Solicitud;
use App\Models\Colaboradores;
use App\Models\Cubiculo;
use App\Models\Motivo;
use App\Models\Submotivo;

use DB;
use Excel;

class SolicitudController extends Controller
{
    //



    private $request;
    private $common;

    public function __construct(Request $request){
        $this->request = $request;
    }

    public function Index(){
        
     

        return \view('dist/solicitud/index');

    }

    public function PostIndex(){

                //return $colaboradoresId;
        
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
        
                $query = DB::table('solicitud')
                ->leftjoin('departamento', 'departamento.id', '=', 'solicitud.departamentoId')
                ->leftjoin('tipoAtencion', 'tipoAtencion.id', '=', 'solicitud.idTipoAtencion');

                // Agrega la variable de usuario para simular ROW_NUMBER()
                $query->select([
                        DB::raw('@row_num := @row_num + 1 AS row_number'),
                        'solicitud.*',
                        'departamento.nombre',
                        'tipoAtencion.descripcion'
                    ])
                    ->from(DB::raw('(SELECT @row_num := 0) AS vars, solicitud'))
                    ->orderBy($orderBy, $order);

        
                if(isset($request['searchInput']) && trim($request['searchInput']) != ""){
                    $query->where(
                        function ($query) use ($request) {
                            $query->orWhere('solicitud.nombre', 'like', '%'.trim($request['searchInput']).'%');
                            $query->orWhere('solicitud.codigo', 'like', '%'.trim($request['searchInput']).'%');
                        }
                     );		
                }
                   
                $solicitud = $query->paginate($length); 
            
                $result = $solicitud->toArray();
                $data = array();
                foreach($result['data'] as $value){
        
                    if($value->estatus == 'Activo'){
                        $detalle = '<a href="/dist/solicitud/mostrar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-warning text-white m-b-5"> <i class="bi bi-eye"></i> </a>
                                        <a href="/dist/solicitud/editar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-secondary text-white m-b-5"> <i class="bi bi-pencil"></i> </a>
                                        <a href="#" attr-id="'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-danger text-white m-b-5 desactivar"> <i class="bi bi-trash"></i> </a>';
                    }else{
                        $detalle = '<a href="/dist/solicitud/mostrar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-warning text-white m-b-5"> <i class="bi bi-eye"></i> </a>
                                        <a href="/dist/solicitud/editar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-secondary text-white m-b-5"> <i class="bi bi-pencil"></i> </a>
                                        <a href="#" attr-id="'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-primary text-white m-b-5 desactivar"> <i class="bi bi-check2-square"></i> </a>';
                    }
        
                    $data[] = array(
                          "DT_RowId" => $value->row_number,
                          "id" => $value->id,
                          "TipoAtencion"=> $value->descripcion,
                          "codigo"=> $value->codigo,
                          "departamento"=> $value->nombre,
                          "estatus"=> $value->estatus,
                          "detalle"=> $detalle
                    );
                }
        
                $response = array(
                        'draw' => isset($request['draw']) ? $request['draw'] : '1',
                        'recordsTotal' => $result['total'],
                        'recordsFiltered' => $result['total'],
                        'data' => $data,
                    );
                return response()
                      ->json($response);
        
        
            }

            public function Missolicitudes(){
        
      
                return \view('dist/solicitud/missolicitudes');
        
            }

            public function PostMissolicitudes($colaboradoresId){

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
        
                /*$query = DB::table('solicitud')
                //->where('solicitud.funcionarioId', '=', $colaboradoresId)
                ->leftjoin('departamento', 'departamento.id', '=', 'solicitud.departamentoId')
                ->leftjoin('tipoAtencion', 'tipoAtencion.id', '=', 'solicitud.idTipoAtencion')
                
                 ->select('solicitud.*', 'departamento.nombre', 'tipoAtencion.descripcion' )
                 ->orderBy($orderBy,$order);
        */
                $query = DB::table('solicitud')
                ->leftjoin('departamento', 'departamento.id', '=', 'solicitud.departamentoId')
                ->leftjoin('tipoAtencion', 'tipoAtencion.id', '=', 'solicitud.idTipoAtencion');

                if ($colaboradoresId <> 0) {
                $query->where('solicitud.funcionarioId', '=', $colaboradoresId);
                }

                $query->select([
                    DB::raw('@row_num := @row_num + 1 AS row_number'),
                    'solicitud.*',
                    'departamento.nombre',
                    'tipoAtencion.descripcion'
                ])
                ->from(DB::raw('(SELECT @row_num := 0) AS vars, solicitud'))
                ->orderBy($orderBy, $order);
        
                if(isset($request['searchInput']) && trim($request['searchInput']) != ""){
                    $query->where(
                        function ($query) use ($request) {
                            $query->orWhere('solicitud.nombre', 'like', '%'.trim($request['searchInput']).'%');
                            $query->orWhere('solicitud.codigo', 'like', '%'.trim($request['searchInput']).'%');
                        }
                     );		
                }
                   
                $solicitud = $query->paginate($length); 
            
                $result = $solicitud->toArray();
                $data = array();
                foreach($result['data'] as $value){
        
                    if($value->estatus == 'Activo'){
                        $detalle = '<a href="/dist/solicitud/mostrar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-warning text-white m-b-5"> <i class="bi bi-eye"></i> </a>
                                        <a href="/dist/solicitud/editar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-secondary text-white m-b-5"> <i class="bi bi-pencil"></i> </a>
                                        <a href="#" attr-id="'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-danger text-white m-b-5 desactivar"> <i class="bi bi-trash"></i> </a>';
                    }else{
                        $detalle = '<a href="/dist/solicitud/mostrar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-warning text-white m-b-5"> <i class="bi bi-eye"></i> </a>
                                        <a href="/dist/solicitud/editar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-secondary text-white m-b-5"> <i class="bi bi-pencil"></i> </a>
                                        <a href="#" attr-id="'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-primary text-white m-b-5 desactivar"> <i class="bi bi-check2-square"></i> </a>';
                    }
        
                    $data[] = array(
                          "DT_RowId" => $value->row_number,
                          "id" => $value->id,
                          "TipoAtencion"=> $value->descripcion,
                          "codigo"=> $value->codigo,
                          "departamento"=> $value->nombre,
                          "estatus"=> $value->estatus,
                          "detalle"=> $detalle
                    );
                }
        
                $response = array(
                        'draw' => isset($request['draw']) ? $request['draw'] : '1',
                        'recordsTotal' => $result['total'],
                        'recordsFiltered' => $result['total'],
                        'data' => $data,
                    );
                return response()
                      ->json($response);

            }
        
            public function Nuevo(){
                

                //return 'hola';


                $departamento = DB::table('departamento')
                ->where('estatus', '=', 'Activo')
                ->where('organizacionId', '=', '1')
                ->select('id', 'nombre', 'codigo')
                ->get();

                if(empty($departamento)){
                    return redirect('dist/dashboard')->withErrors("ERROR LA PROVINCIA ESTA VACIA CODE-0226");
                }	
                view()->share('departamento', $departamento);	


                /*if(!$this->common->usuariopermiso('004')){
                    return redirect('dist/dashboard')->withErrors($this->common->message);
                }*/
                
                return \View::make('dist/solicitud/nuevo');
            }
        
            public function postNuevo(){
        
                
                /*if(!$this->common->usuariopermiso('004')){
                    return redirect('dist/dashboard')->withErrors($this->common->message);
                }*/
        
                //return $this->request->all();
        
                /*$solicitudExiste = Solicitud::where('nombre', $this->request->nombre)
                //->where('distribuidorId', Auth::user()->distribuidorId)
                ->first();
        
                if(!empty($solicitudExiste)){
                    return redirect('dist/solicitud/nuevo')->withErrors("ERROR AL GUARDAR STORE CEBECECO CODE-0001");
                }*/

                $departamento = DB::table('departamento')
                ->where('departamento.id', '=', trim($this->request->departamento))
                ->select(DB::raw("SUBSTRING(departamento.codigo, 1, 1) as cod_depart"))
                ->first();

                $cod_depart = $departamento->cod_depart;

                //return $departamento;
        
                DB::beginTransaction();
                try { 	
                    $solicitud = new Solicitud;
                    $solicitud->IdTipoAtencion          = trim($this->request->tipoAtencion);
                    $solicitud->departamentoId          = trim($this->request->departamento);
                    if(isset($this->request->comentario)){
                    $solicitud->infoextra               = trim($this->request->comentario); 
                    }
                    
                    $solicitud->estatus                 = 'Activo';
                    $solicitud->fechaAtencion           = date('Y-m-d H:i:s');
                    $solicitud->created_at              = date('Y-m-d H:i:s');
                    $solicitud->funcionarioId           = Auth::user()->id;
                    $solicitud->usuarioId               = Auth::user()->id;
                    //$solicitud->organizacionId          = 1;
                    $result = $solicitud->save();

                    $solicitudId = $solicitud->id;
        
                    if(empty($solicitudId)){
                        DB::rollBack();
                        return redirect('dist/solicitud/nuevo')->withErrors("ERROR AL GUARDAR EL CONTRATO NO SE GENERO UN # DE CONTRATO CORRECTO CODE-0196");
                    }
                    
                    $solicitudCode =  $cod_depart . str_pad($solicitudId,2, "0",STR_PAD_LEFT);
                    //return $solicitudCode;
                    $solicitudUpdate = Solicitud::find($solicitudId);
                    $solicitudUpdate->codigo = $solicitudCode;
                    $result = $solicitudUpdate->save();	
                    
                    $cubiculoCount = Cubiculo::count();

                    if ($cubiculoCount < 7) {
                        //return $cubiculoCount;

                    $colaboradorSinCubiculo = Colaboradores::where('estatus', 'Activo')
                    ->whereNotIn('id', function ($query) {
                    $query->select('funcionarioId')->from('cubiculo');
                    })
                    ->first();

                    if ($colaboradorSinCubiculo) {

                        $cubiculo = new Cubiculo;
                        $cubiculo->solicitudId      = $solicitudId;
                        $cubiculo->funcionarioId    = $colaboradorSinCubiculo->id;
                        $cubiculo->llamado          = 0;
                        $cubiculo->estatus          = 'Activo';
                        $cubiculo->codigo           = $solicitudCode;
                        $cubiculo->usuarioId        = Auth::user()->id;
                        $result = $cubiculo->save();
                
                        if ($result) {
                            Solicitud::where('id', $solicitudId)
                                ->update([
                                    'usuarioId' => $colaboradorSinCubiculo->id,
                                    'funcionarioId' => $colaboradorSinCubiculo->id
                                ]);
                        }
                

                        // Puedes realizar alguna acción adicional después de asignar el cubículo si es necesario
                    }
                    
                
                    
                }


        
                } catch(\Illuminate\Database\QueryException $ex){ 
                    DB::rollBack();
                    return redirect('dist/solicitud/nuevo')->withErrors('ERROR AL GUARDAR STORE CEBECECO CODE-0002'.$ex);
                }
                
                if($result != 1){
                    DB::rollBack();
                    return redirect('dist/solicitud/nuevo')->withErrors("ERROR AL GUARDAR STORE CEBECECO CODE-0003");
                }
                DB::commit();
        
                return redirect('dist/solicitud')->with('alertSuccess', 'STORE CEBECECO HA SIDO INGRESADA');
            }
        
            public function Editar($solicitudId){
                /*if(!$this->common->usuariopermiso('004')){
                    return redirect('dist/dashboard')->withErrors($this->common->message);
                }*/
        
                $solicitud = DB::table('solicitud')
                 ->where('solicitud.id', '=', $solicitudId)
                 ->leftjoin('tipoAtencion', 'tipoAtencion.id', '=', 'solicitud.IdtipoAtencion')
                ->leftjoin('departamento', 'departamento.id', '=', 'solicitud.departamentoId')
                 //->where('rubro.distribuidorId', Auth::user()->distribuidorId)
                 ->select('solicitud.*','departamento.id as departamentoId', 'departamento.nombre', 'tipoAtencion.id as IdTipoAtencion', 'tipoAtencion.descripcion')->first();
        
                if(empty($solicitud)){
                    return redirect('dist/solicitud')->withErrors("ERROR STORE CEBECECO NO EXISTE CODE-0004");
                }
        
                view()->share('solicitud', $solicitud);
                return \View::make('dist/solicitud/editar');
            }
        
            public function PostEditar(){
                /*if(!$this->common->usuariopermiso('004')){
                    return redirect('dist/dashboard')->withErrors($this->common->message);
                }*/
        
                $request = $this->request->all();
        
                //return $request;
        
                $solicitudId = isset($this->request->solicitudId) ? $this->request->solicitudId: '';
        
                //return $solicitudId;
        
                /*$solicitud = solicitud::where('id', $solicitudId)
                //->where('distribuidorId',Auth::user()->distribuidorId)
                ->first();
        
                if(empty($solicitud)){
                    return redirect('dist/solicitud')->withErrors("ERROR STORE CEBECECO NO EXISTE CODE-0005");
                }*/
        
                DB::beginTransaction();
                    $solicitudUpdate = Solicitud::find($solicitudId);
                    $solicitudUpdate->estatus          = $this->request->estatus;
                    $solicitudUpdate->infoextra        = $this->request->comentario;
                    
                    $result = $solicitudUpdate->save();

                    if($this->request->estatus == 'Resuelto'){
                    DB::table('cubiculo')->where('solicitudId', $solicitudId)->delete();

                    $cubiculoCount = Cubiculo::count();


                    if ($cubiculoCount < 7) {

                        $solicitud = DB::table('solicitud')
                        ->where('estatus', '=', 'Activo' )
                        ->orderBy('id', 'asc') // o 'desc' para orden descendente
                        ->first();

                        //return $solicitud;
                    if(isset($solicitud)){ 
                                               //return $cubiculoCount;
                        $cubiculo = new Cubiculo;
                        $cubiculo->solicitudId      = $solicitud->id;
                        $cubiculo->funcionarioId    = Auth::user()->id;
                        $cubiculo->llamado          = 0;
                        $cubiculo->estatus          = 'Activo';	
                        $cubiculo->codigo           = $solicitud->codigo;
                        $cubiculo->usuarioId       = Auth::user()->id;
                        //$solicitud->organizacionId          = 1;
                        $result = $cubiculo->save();
                        
                        }
                    }


                    }
        
                if($result != 1){
                    DB::rollBack();
        
                    return redirect('dist/solicitud/editar/'.$solicitudId)->withErrors("ERROR AL EDITAR ELEMENTOS DE STORE CEBECECO CODE-0006");
                }
        
                DB::commit();
        
                return redirect('dist/solicitud/')->with('alertSuccess', 'STORE CEBECECO HA SIDO EDITADO');
            }
        
            public function Mostrar($solicitudId){
                /*if(!$this->common->usuariopermiso('004')){
                    return redirect('dist/dashboard')->withErrors($this->common->message);
                }*/
        
                $solicitud = DB::table('solicitud')
                 ->where('solicitud.id', '=', $solicitudId)
                 ->leftjoin('departamento', 'departamento.id', '=', 'solicitud.departamentoId')
                 ->leftjoin('tipoAtencion', 'tipoAtencion.id', '=', 'solicitud.idTipoAtencion')
                 ->select('solicitud.*', 'departamento.nombre', 'tipoAtencion.descripcion')->first();
        
                if(empty($solicitud)){
                    return redirect('dist/solicitud')->withErrors("ERROR STORE CEBECECO NO EXISTE CODE-0007");
                }
        
                //return $compania;
        
                 view()->share('solicitud', $solicitud);
        
                return \View::make('dist/solicitud/mostrar');
            }
            public function Desactivar(){
                /*if(!$this->common->usuariopermiso('004')){
                    return response()
                      ->json(['response' => false]);
                }*/
                
                $solicitudExiste = Departamento::where('id', $this->request->solicitudId)
                                //->where('distribuidorId', Auth::user()->distribuidorId)
                                ->first();
                if(!empty($solicitudExiste)){
        
                    $estatus = 'Inactivo';
                    if($solicitudExiste->estatus == 'Inactivo'){
                        $estatus = 'Activo';	
                    }
        
                    $affectedRows = Departamento::where('id', '=', $this->request->solicitudId)
                                    ->update(['estatus' => $estatus]);
                    
                    return response()
                      ->json(['response' => TRUE]);
                }
        
                return response()
                      ->json(['response' => false]);
            }


            public function postBuscatipoatencion(){

                $departamento = $this->request->departamento;
                
                $tipoAtencion = DB::table('tipoAtencion')
                ->where('estatus', '=', 'Activo')
                ->where('departamentoId', '=', $departamento)
                ->select('id', 'descripcion', 'codigo')
                ->get();

                $data[] = "";
            
                foreach ($tipoAtencion as $key => $value) {
                    
                    $tipoAtencionid = $value->id;
                    $tipoAtenciondescripcion = $value->descripcion;
                    $tipoAtencioncodigo = $value->codigo;
            
                    $data[] = array(
                        "detalle"=> "<option value='".$tipoAtencionid."' >".$tipoAtenciondescripcion."</option>"
                    );		  		 
                        
                }		
                    $response = array(
                        'response' => TRUE,
                        'data' => $data,
                    );
            
                    return response()
                    ->json($response);				
                        
            }


            public function postBuscamotivo(){

                $departamento = $this->request->departamento;
                
                $motivo = DB::table('motivo')
                ->where('estatus', '=', 'Activo')
                ->where('departamentoId', '=', $departamento)
                ->select('id', 'descripcion', 'codigo')
                ->get();

                $data[] = "";
            
                foreach ($motivo as $key => $value) {
                    
                    $motivoid = $value->id;
                    $motivodescripcion = $value->descripcion;
                    $motivocodigo = $value->codigo;
            
                    $data[] = array(
                        "detalle"=> "<option value='".$motivoid."' >".$motivodescripcion."</option>"
                    );		  		 
                        
                }		
                    $response = array(
                        'response' => TRUE,
                        'data' => $data,
                    );
            
                    return response()
                    ->json($response);				
                        
            }


}
