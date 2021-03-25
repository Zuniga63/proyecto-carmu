@extends("theme.$theme.layout")
@section('title', 'Tallas')

@section('styles')
@livewireStyles
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
@endsection

@section('scripts')
@livewireScripts
@stack('scripts')
@endsection

@section('contentTitle', "Tallas")
@section('contentBreadcrum')
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item"><a href="{{route('admin.admin')}}">Admin</a></li>
  <li class="breadcrumb-item active">Tallas</li>
</ol>
@endsection

@section('content')
  {{-- <livewire:admin.shop.size-component/> --}}
  {{$slot}}
@endsection