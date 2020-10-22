@extends("theme.$theme.layout")
@section('title', 'Marcas')

@section('styles')
@livewireStyles
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
@endsection

@section('scripts')
@livewireScripts
@endsection

@section('contentTitle', "Marcas")
@section('contentBreadcrum')
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item"><a href="{{route('admin.admin')}}">Admin</a></li>
  <li class="breadcrumb-item active">Marcas</li>
</ol>
@endsection

@section('content')
  <livewire:admin.shop.brand-component/>
@endsection