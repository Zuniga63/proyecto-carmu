@extends("theme.$theme.layout")
@section('title', 'Menu')

@section('scripts')
<script src="{{asset("assets/pages/js/admin/menu/create.js")}}"></script>
@endsection

@section('contentTitle', "Editar menú")
@section('contentBreadcrum')
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item"><a href="{{route('admin.admin')}}">Admin</a></li>
  <li class="breadcrumb-item"><a href="{{route('admin.menu')}}">Menu</a></li>
  <li class="breadcrumb-item active">Editar</li>
</ol>
@endsection

@section('content')
@include('includes.form-error')
@include('includes.message')
<div class="card card-success">
  <div class="card-header">
    <h3 class="card-title">Formulario de edicion</h3>
    <div class="card-tools">
      <a href="{{route('admin.menu')}}" class="btn btn-block btn-success btn-sm">
        <i class="fas fa-plus-circle"></i> Volver al listado
      </a>
    </div>
  </div>
  <!-- /.card-header -->
  <!-- form start -->
  <form class="form-horizontal" id="form-general" method="POST" action="{{route('admin.update_menu', ['id' => $menu->id])}}">
    @csrf @method('put')
    <div class="card-body">
      <div class="form-group row">
        <label for="name" class="col-lg-2 col-form-label required">Nombre</label>
        <div class="col-lg-9">
          <input type="text" class="form-control" id="name" name="name" placeholder="Escribe el nombre aquí"
            value="{{old('name', $menu->name ?? '')}}" required>
        </div>
      </div>
      <div class="form-group row">
        <label for="url" class="col-lg-2 col-form-label required">Url</label>
        <div class="col-lg-9">
          <input type="text" class="form-control" id="url" name="url" placeholder="Escribe la ruta aquí" required
            value="{{old('url', $menu->url ?? '')}}">
        </div>
      </div>
      <div class="form-group row">
        <label for="icon" class="col-lg-2 col-form-label">Icono</label>
        <div class="col-lg-9">
          <input type="text" class="form-control" id="icon" name="icon" placeholder="Escribe la clase del icono"
            value="{{old('icon', $menu->icon ?? '')}}">
        </div>
        <span class="col-lg-1">
          <i class="{{old('icon', $menu->icon ?? '')}}" id="show-icon"></i>
        </span>
      </div>
    </div>
    <!-- /.card-body -->
    <div class="card-footer">
      <button type="submit" class="btn btn-success">Editar</button>
      <a href="{{route('admin.menu')}}" class="btn btn-danger float-right">Eliminar</a>
    </div>
    <!-- /.card-footer -->
  </form>
</div>
@endsection