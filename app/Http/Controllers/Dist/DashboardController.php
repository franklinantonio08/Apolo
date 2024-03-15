<?php

namespace App\Http\Controllers\Dist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

use App\Models\Dashboard;
use App\Models\Cubiculo;

use DB;
use Excel;

class DashboardController extends Controller
{
    //


    private $request;
    private $common;

    public function __construct(Request $request){
        $this->request = $request;
    }

    public function Index(){

        $cubiculo = DB::table('cubiculo')
        ->where('cubiculo.estatus', '=', 'Activo')
        //->where('organizacionId', '=', '1')
        ->leftjoin('solicitud', 'solicitud.id', '=', 'cubiculo.solicitudId')
        ->leftjoin('colaboradores', 'colaboradores.id', '=', 'cubiculo.funcionarioId')
        ->select('cubiculo.id', 'cubiculo.codigo', 'cubiculo.llamado',
       // DB::raw("SUBSTRING(departamento.codigo, 1, 1) as codigo_departamento"),
         DB::raw('SUBSTRING(colaboradores.cubico,8,1) as posicion'))
        ->get();
        //return  $solicitud;

		if(empty($cubiculo)){
    		return redirect('dist/dashboard')->withErrors("ERROR LA PROVINCIA ESTA VACIA CODE-0226");
    	}	
		view()->share('cubiculo', $cubiculo);	


        return \view('dist/dashboard/index');
    }

    public function PostIndex(){


        $cubiculo = DB::table('cubiculo')
        ->where('cubiculo.estatus', '=', 'Activo')
        ->where('cubiculo.llamado', '<=', '3')
        //->leftjoin('solicitud', 'solicitud.id', '=', 'cubiculo.solicitudId')
        //->leftjoin('colaboradores', 'colaboradores.id', '=', 'cubiculo.funcionarioId')
        //->select('cubiculo.id', 'cubiculo.codigo', 'cubiculo.llamado',
       // DB::raw("SUBSTRING(departamento.codigo, 1, 1) as codigo_departamento"),
         //DB::raw('(ROW_NUMBER() OVER (ORDER BY cubiculo.id)) as posicion'))
         //DB::raw('SUBSTRING(colaboradores.cubico,8,1) as posicion'))
        ->get();

        foreach ($cubiculo as $key => $value) {
            $cubiculoId = $value->id;
            $cubiculollamado = $value->llamado + 1;
    
            // Actualiza el campo llamado en la tabla cubiculo
            DB::table('cubiculo')
                ->where('id', $cubiculoId)
                ->update(['llamado' => $cubiculollamado]);
        }

        $cubiculo = DB::table('cubiculo')
        ->where('cubiculo.estatus', '=', 'Activo')
       // ->where('cubiculo.llamado', '<', '3')
        ->leftjoin('solicitud', 'solicitud.id', '=', 'cubiculo.solicitudId')
        ->leftjoin('colaboradores', 'colaboradores.id', '=', 'cubiculo.funcionarioId')
        ->select('cubiculo.id', 'cubiculo.codigo', 'cubiculo.llamado',
       // DB::raw("SUBSTRING(departamento.codigo, 1, 1) as codigo_departamento"),
         //DB::raw('(ROW_NUMBER() OVER (ORDER BY cubiculo.id)) as posicion'))
         DB::raw('SUBSTRING(colaboradores.cubico,8,1) as posicion')
         )
        ->get();

        return $cubiculo;

        return view('dist/dashboard/listado', compact('cubiculo'));


    }
}
