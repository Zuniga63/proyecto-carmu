@extends("theme.$theme.layout")
@section('title', 'Menu')

@section('styles')
<link rel="stylesheet" href="{{asset("assets/js/jquery-nestable/jquery.nestable.css")}}">
@endsection

@section('scriptPlugins')
<script src="{{asset("assets/js/jquery-nestable/jquery.nestable.js")}}"></script>
@endsection

@section('scripts')
<script src="{{asset("assets/pages/js/admin/menu/index.js")}}"></script>
@endsection

@section('contentTitle', "Sistema de menus")
@section('contentBreadcrum')
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item"><a href="{{route('admin.admin')}}">Home</a></li>
  <li class="breadcrumb-item active">Menu</li>
</ol>
@endsection

@section('content')
@include('includes.message')
@csrf
<div class="card card-success">
  <div class="card-header">
    <h3 class="card-title">Ordenar Menus</h3>
    <div class="card-tools">
      <a href="{{route('admin.menu_create')}}" class="btn btn-block btn-success btn-sm">
        <i class="fas fa-plus-circle"></i> Nuevo Menú
      </a>
    </div>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <div class="row">
      <!-- Contenido de nestable -->
      <div class="dd" id="nestable">
        <ol class="dd-list">
          @foreach ($menus as $key => $item)
          @include('admin.menu.menu-item', ["item" => $item])
          @endforeach
        </ol>
      </div><!-- /.nestable -->
    </div>
  </div>
  <!-- /. card-body -->
</div>
@endsection