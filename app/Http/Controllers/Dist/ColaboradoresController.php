<?php

namespace App\Http\Controllers\Dist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use App\Models\Colaboradores;

use DB;
use Excel;

class ColaboradoresController extends Controller
{
    //
    private $request;
    private $common;

    public function __construct(Request $request){
        $this->request = $request;
    }

    public function Index(){

        return \view('dist/colaboradores/index');
    }


public function PostIndex(){

    $request = $this->request->all();
    $columnsOrder = isset($request['order'][0]['column']) ? $request['order'][0]['column'] : '0';
    $orderBy=isset($request['columns'][$columnsOrder]['data']) ? $request['columns'][$columnsOrder]['data'] : 'id';
    $order = isset($request['order'][0]['dir']) ? $request['order'][0]['dir'] : 'ASC';
    $length = isset($request['length']) ? $request['length'] : '15';

    $currentPage = $request['currentPage'];  
    Paginator::currentPageResolver(function() use ($currentPage){
        return $currentPage;
    });

    $query = DB::table('posiciones')
    ->leftjoin('departamento', 'departamento.id', '=', 'posiciones.departamentoId')
     ->select('posiciones.*', 'departamento.nombre as departamento')
     ->orderBy($orderBy,$order);


    if(isset($request['searchInput']) && trim($request['searchInput']) != ""){
        $query->where(
            function ($query) use ($request) {
                $query->orWhere('posiciones.nombre', 'like', '%'.trim($request['searchInput']).'%');
                $query->orWhere('posiciones.codigo', 'like', '%'.trim($request['searchInput']).'%');
                $query->orWhere('departamento.nombre', 'like', '%'.trim($request['searchInput']).'%');
            }
         );		
    }
       
    $posiciones = $query->paginate($length); 

    $result = $posiciones->toArray();
    $data = array();
    foreach($result['data'] as $value){

        if($value->estatus == 'Activo'){
            $detalle = '<a href="/dist/posiciones/mostrar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-warning text-white m-b-5"> <i class="bi bi-eye"></i> </a>
                            <a href="/dist/posiciones/editar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-secondary text-white m-b-5"> <i class="bi bi-pencil"></i> </a>
                            <a href="#" attr-id="'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-danger text-white m-b-5 desactivar"> <i class="bi bi-trash"></i> </a>';
        }else{
            $detalle = '<a href="/dist/posiciones/mostrar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-warning text-white m-b-5"> <i class="bi bi-eye"></i> </a>
                            <a href="/dist/posiciones/editar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-secondary text-white m-b-5"> <i class="bi bi-pencil"></i> </a>
                            <a href="#" attr-id="'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-primary text-white m-b-5 desactivar"> <i class="bi bi-check2-square"></i> </a>';
        }

        $data[] = array(
              "DT_RowId" => $value->id,
              "id" => $value->id,
              "nombre"=> $value->nombre,
              "codigo"=> $value->codigo,
              "correo"=> $value->correo,
              "telefono"=> $value->telefono,
              "tipousuario"=> $value->tipoUsuario,
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
    /*if(!$this->common->usuariopermiso('004')){
        return redirect('dist/dashboard')->withErrors($this->common->message);
    }*/
        $departamento = DB::table('departamento')
    	->where('estatus', '=', 'Activo')
    	->where('organizacionId', '=', '1')
		->select('id', 'nombre', 'codigo')
		->get();

		if(empty($departamento)){
    		return redirect('dist/dashboard')->withErrors("ERROR LA PROVINCIA ESTA VACIA CODE-0226");
    	}	
		view()->share('departamento', $departamento);	


    return \View::make('dist/colaboradores/nuevo');
}

}
