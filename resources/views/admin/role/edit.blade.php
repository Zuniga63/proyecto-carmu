@extends("theme.$theme.layout")
@section('title', 'Roles')

@section('scripts')
<script src="{{asset("assets/pages/js/admin/role/create.js")}}"></script>
@endsection

@section('contentTitle', "Creación de rol")
@section('contentBreadcrum')
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item"><a href="{{route('admin.admin')}}">Home</a></li>
  <li class="breadcrumb-item"><a href="{{route('admin.role')}}">Rol</a></li>
  <li class="breadcrumb-item active">Editar</li>
</ol>
@endsection

@section('content')
@include('includes.message')
@include('includes.form-error')
<div class="card card-success">
  <div class="card-header">
    <h3 class="card-title">Editar el rol {{$data->name}}</h3>
    <div class="card-tools">
      <a href="{{route('admin.role')}}" class="btn btn-block btn-success btn-sm">
        <i class="fas fa-plus-circle"></i> Volver al listado
      </a>
    </div>
  </div>
  <!-- /.card-header -->
  <form class="form-horizontal" id="form-general" action="{{route('admin.update_role', ['id' => $data->id])}}" method="POST">
    @csrf @method('put')
    <div class="card-body">
      <div class="form-group row">
        <label for="name" class="col-lg-2 col-form-label required">Nombre</label>
        <div class="col-lg-9">
          <input type="text" class="form-control" id="name" name="name" placeholder="Escribe el nombre aquí"
            value="{{old('name', $data->name ?? '')}}" required>
          {{-- si el dato existe y es nulo pone por defecto '' --}}
        </div>
      </div>
    </div>
    <!-- /.card-body -->
    <div class="card-footer">
      <button type="submit" class="btn btn-success">Editar</button>
      <button type="reset" class="btn btn-default float-right">Cancelar</button>
    </div>
    <!-- /.card-footer -->
  </form>
</div>
@endsection