@extends("theme.$theme.layout")
@section('title', 'Menu')

@section('contentTitle', "Sistema de menus")
@section('contentBreadcrum')
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item"><a href="{{route('admin.admin')}}">Home</a></li>
  <li class="breadcrumb-item active">Menu</li>
</ol>
@endsection