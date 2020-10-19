@extends("theme.$theme.layout")
@section('title', 'Categorias')

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
@endsection

@section('contentTitle', "Permisos")
@section('contentBreadcrum')
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item"><a href="{{route('admin.admin')}}">Admin</a></li>
  <li class="breadcrumb-item active">Categorías de productos</li>
</ol>
@endsection

@section('content')
  <livewire:admin.shop.category-component/>
@endsection