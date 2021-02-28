@extends("theme/$theme/layout")

@section('title', 'Ver Cajas')

@section('contentTitle', "Administra Cajas")

@section('styles')
@livewireStyles
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
@endsection

@section('scripts')
@livewireScripts
@stack('scripts')
@endsection



@section('contentBreadcrum')
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item"><a href="{{route('admin.admin')}}">Home</a></li>
  <li class="breadcrumb-item active">Ver Cajas</li>
</ol>
@endsection

@section('content')
{{ $slot }}
@endsection