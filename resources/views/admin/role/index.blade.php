@extends("theme.$theme.layout")
@section('title', 'Roles')

@section('scripts')
<script src="{{asset("assets/pages/js/admin/role/index.js")}}"></script>
@endsection

@section('contentTitle', "Sistema de roles")
@section('contentBreadcrum')
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item"><a href="{{route('admin.admin')}}">Home</a></li>
  <li class="breadcrumb-item active">Rol</li>
</ol>
@endsection

@section('content')
@include('includes.message')
@csrf
<div class="card card-success">
  <div class="card-header">
    <h3 class="card-title">Listado de roles</h3>
    <div class="card-tools">
      <a href="{{route('admin.create_role')}}" class="btn btn-block btn-success btn-sm">
        <i class="fas fa-plus-circle"></i> Nuevo Rol
      </a>
    </div>
  </div>
  <!-- /.card-header -->
  <div class="card-body table-responsive p-0" style="height: 300px;">
    <table class="table table-head-fixed table-hover table-striped text-nowrap">
      <thead>
        <tr>
          <th>Nombre </th>
          <th class="width70"></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($roles as $role)
        <tr>
          <td>{{$role->name}}</td>
          <td>
            <a href="{{route('admin.edit_role', ['id' => $role->id])}}" class="btn-action-table tooltipsC"
              title="Editar este registro">
              <i class="fas fa-pencil-alt"></i>
            </a>
            <form action="{{route('admin.delete_role', ['id' => $role->id])}}" class="d-inline form-delete">
              @csrf @method('delete')
              <button type="submit" class="btn-action-table delete tooltipsC" title="Eliminar registro">
                <i class="fas fa-trash text-danger"></i>
              </button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <!-- /. card-body -->
</div>
@endsection