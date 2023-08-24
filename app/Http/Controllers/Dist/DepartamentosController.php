<?php

namespace App\Http\Controllers\Dist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Test;

use DB;

class DepartamentosController extends Controller
{
    //

    public function Index(){


        $usuario = DB::table('provincia')

        ->first();


    view()->share('provincia', $usuario);
    	return \View::make('dist/test/index');
}

}
