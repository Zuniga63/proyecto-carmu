@extends("theme.$theme.layout")
@section('title', 'Roles')

@section('styles')
  @livewireStyles
  <link rel="stylesheet" href="{{asset("assets/js/jquery-nestable/jquery.nestable.css")}}">
  <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
@endsection

@section('scripts')
{{-- <script src="{{asset("assets/pages/js/admin/role/index.js")}}"></script> --}}
  @livewireScripts
@endsection

@section('contentTitle', "Sistema de roles")
@section('contentBreadcrum')
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item"><a href="{{route('admin.admin')}}">Admin</a></li>
  <li class="breadcrumb-item active">Rol</li>
</ol>
@endsection

@section('content')
<livewire:admin.role-component/>
@endsection