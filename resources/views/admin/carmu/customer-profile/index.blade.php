@extends("theme.$theme.layout")
@section('title', 'Perfil Cliente')

@section('styles')
@livewireStyles
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
<style>
  :target::before {
    content: "";
    display: block;
    height: 3.75rem; /* aquí la altura de la cabecera fija*/
    margin: -3.75rem 0 0; /*altura negativa de la cabecera fija*/
  }
</style>
@endsection

@section('scripts')
@livewireScripts
<!-- ChartJS -->
<script src="{{asset("assets/$theme/plugins/chart.js/Chart.min.js")}}"></script>
@stack('scripts')
@endsection

@section('contentTitle', "Perfil del cliente")
@section('contentBreadcrum')
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item"><a href="{{route('admin.admin')}}">Admin</a></li>
  <li class="breadcrumb-item"><a href="{{route('admin.carmu_profile')}}">Clientes</a></li>
  <li class="breadcrumb-item active">Perfil</li>
</ol>
@endsection

@section('content')
  {{-- <livewire:admin.carmu.customers-component/> --}}
  {{ $slot }}
@endsection