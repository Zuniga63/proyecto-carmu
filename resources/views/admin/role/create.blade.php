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
  <li class="breadcrumb-item active">Crear</li>
</ol>
@endsection

@section('content')
<div class="card card-success">
  <div class="card-header">
    <h3 class="card-title">Nuevo Rol</h3>
    <div class="card-tools">
      <a href="{{route('admin.role')}}" class="btn btn-block btn-success btn-sm">
        <i class="fas fa-plus-circle"></i> Volver al listado
      </a>
    </div>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    @include('admin.role.form')
  </div>
  <!-- /. card-body -->
</div>
@endsection