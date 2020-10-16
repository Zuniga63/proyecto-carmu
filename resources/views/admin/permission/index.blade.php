@extends("theme.$theme.layout")
@section('title', 'Permisos')

@section('styles')
@livewireStyles
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
@endsection

@section('scripts')
@livewireScripts
<script src="{{asset("assets/pages/js/admin/permission/index.js")}}"></script>
@endsection

@section('contentTitle', "Permisos")
@section('contentBreadcrum')
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item"><a href="{{route('admin.admin')}}">Admin</a></li>
  <li class="breadcrumb-item active">Permiso</li>
</ol>
@endsection

@section('content')
    @livewire('permission-component')
@endsection