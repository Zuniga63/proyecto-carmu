@extends("theme.$theme.layout")
@section('title', 'Administración de menus')

@section('styles')
  @livewireStyles
  <link rel="stylesheet" href="{{asset("assets/js/jquery-nestable/jquery.nestable.css")}}">
  <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
@endsection

@section('scriptPlugins')
<script src="{{asset("assets/js/jquery-nestable/jquery.nestable.js")}}"></script>
@endsection

@section('scripts')
  @livewireScripts
  {{-- <script src="{{asset("assets/pages/js/admin/menu/index.js")}}"></script> --}}
  <script>
    // window.addEventListener('load', () => {
    //   $('#nestable').nestable().on('change', function () {
    //   let menu = JSON.stringify($('#nestable').nestable('serialize'));
    //   let token = document.querySelector('input[name=_token]').value;
      
    //   fetch('/admin/menu/guardar-orden', {
    //   headers: {
    //   "Content-Type": "application/json",
    //   "Accept": "application/json, text-plain, */*",
    //   "X-Requested-With": "XMLHttpRequest",
    //   "X-CSRF-TOKEN": token
    //   },
    //   method: 'post',
    //   credentials: "same-origin",
    //   body: JSON.stringify({menu})
    //   }).then((data) => data.json())
    //   .then(data => {
    //   let message = "La nueva distribucion se ha guardado en el sistema";
    //   let title = "Orden guardado!"
    //   functions.notifications(message, title, 'success');
    //   console.log(data);
    //   })
    //   .catch(function (error) {
    //   console.log(error);
    //   });
      
    //   })
    // })
  </script>
@endsection

@section('contentTitle', "Sistema de Administracion de Menús")
@section('contentBreadcrum')
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item"><a href="{{route('admin.admin')}}">Admin</a></li>
  <li class="breadcrumb-item active">Administrar menus</li>
</ol>
@endsection

@section('content')
{{-- @include('includes.message')
@csrf
<div class="card card-success">
  <div class="card-header">
    <h3 class="card-title">Sistema de Administracion de Menús</h3>
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
</div> --}}

<livewire:admin.menu-component>
@endsection