@extends("theme.$theme.layout")
@section('title', 'Productos')

@section('styles')
@livewireStyles
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
<!-- Select2 -->
<link rel="stylesheet" href="{{asset("assets/$theme/plugins/select2/css/select2.min.css")}}">
<link rel="stylesheet" href="{{asset("assets/$theme/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css")}}">

@endsection

@section('scripts')
@livewireScripts
<script src="{{asset("assets/$theme/plugins/select2/js/select2.full.min.js")}}"></script>
@stack('scripts')
@endsection

@section('contentTitle', "Administración de productos")
@section('contentBreadcrum')
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item"><a href="{{route('admin.admin')}}">Admin</a></li>
  <li class="breadcrumb-item active">Productos</li>
</ol>
@endsection

@section('content')
  {{-- <livewire:admin.shop.product-component/> --}}
  {{$slot}}
@endsection