@extends("theme.$theme.layout")
@section('title', 'Menu')

@section('contentTitle', "Crear un nuevo menú")
@section('contentBreadcrum')
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item"><a href="{{route('admin.admin')}}">Home</a></li>
  <li class="breadcrumb-item"><a href="{{route('admin.menu')}}">Menu</a></li>
  <li class="breadcrumb-item active">Crear</li>
</ol>
@endsection

@section('content')
@include('admin.menu.form')
@endsection