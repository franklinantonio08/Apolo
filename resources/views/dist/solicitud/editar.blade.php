@section('scripts')

<script>
	var token = '{{ csrf_token() }}';
</script>
	
<script type="text/javascript" src="{{ asset('../js/dist/departamento/departamento.js') }}"></script>
<script src="{{ asset('../js/comun/messagebasicModal.js') }}"></script>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"> 
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

@stop

@extends('layouts.admin')

@section('content')

    <!-- ACTION BUTTONS -->
<div class="row">

    @include('includes/errors')
    @include('includes/success')

</div>
   
	<div class="col-lg-12">
        <div class="card mb-4">
			
            <div class="card-body p-4">
                <div class="row">
                    <div class="col">
                        <div class="card-title fs-4 fw-semibold">Solicitud</div>
                    </div>
                </div>
			</div>

            <div class="table-responsive">

                
                <!-- Formulario -->

                <div class="container-fluid px-2 my-2">
                    <form id="nuevoregistro" name="editarregistro" method="POST" action="{{ url()->current('/dist/solicitud/nuevo') }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            
                            <input type="hidden" id="solicitudId" name="solicitudId" value="{{$solicitud->id}}" class="form-control text-right" >

                        <div class="col-lg-5 m-b-6">

                                <div class="input-group mb-3">
                                    <span class="input-group-text" style="width: 130px;" >Tipo de Atencion</span>
                                    <input type="text" class="form-control" id="tipoAtencion" name="tipoAtencion" placeholder="" value="{{$solicitud->descripcion}}">
                                    <input type="hidden" id="IdTipoAtencion" name="IdTipoAtencion" value="{{$solicitud->IdTipoAtencion}}" class="form-control text-right" >
                                </div>

                                <div class="input-group mb-3">
                                    <span class="input-group-text" style="width: 130px;" >Departamento</span>
                                    <input type="text" class="form-control" id="departamento" name="departamento" placeholder="" value="{{$solicitud->nombre}}">
                                    <input type="hidden" id="departamentoId" name="departamentoId" value="{{$solicitud->departamentoId}}" class="form-control text-right" >
                                </div>

                                <div class="input-group mb-3">
                                    <label class="input-group-text" style="width: 150px;" for="inputGroupSelect01">Estatus</label>
                                    <select class="form-select" id="estatus" name="estatus">
                                        <option value="Activo" {{ $solicitud->estatus === 'Activo' ? 'selected' : '' }}>Activo</option>
                                        <option value="Resuelto" {{ $solicitud->estatus === 'Resuelto' ? 'selected' : '' }}>Resuelto</option>
                                    </select>
                                </div>
                                
                                <div class="input-group mb-3">
                                    <span class="input-group-text" style="width: 150px;" >Ingresar Consumidor</span>
                                    <input type="text" class="form-control" id="consumidor" name="consumidor" placeholder="" value="" >
                                    <span class="input-group-btn">
                                        <button type="button" id="botonconsumidor" name="agregarSingleConsumidor" class="btn waves-effect waves-light btn-warning"><i class="fa fa-plus"></i></button>
                                    </span>
                                </div>

                                <div class="form-floating mb-3">
                                    <textarea class="form-control" id="comentario" name="comentario" type="text" placeholder="Comentario" style="height: 10rem;" ></textarea>
                                    <label for="comentario">Comentario</label>
                                </div>

                                <!-- ACTION BUTTONS -->
                                    <div class="form-group row">
                                        <div class="offset-12 col-12">
                                            <button id="submitForm" name="submitForm" type="submit" class="btn btn-primary text-white"><i class="fa fa-check m-r-5"></i> Guardar</button>
                                            <a href="{{ url()->previous() }}"  class="btn btn-danger text-white"><i class="fa fa-remove m-r-5"></i> Cancelar</a>
                                        </div>
                                    </div>
                                <!-- end ACTION BUTTONS -->

                               
                        </div>
                    </form>
                </div>
            
                <!-- Fin Formulario-->

            </div>
	    </div>    
    </div>  



</div>

@include('includes/messagebasicmodal')
@endsection



