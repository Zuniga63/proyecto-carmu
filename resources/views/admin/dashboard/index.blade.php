@extends("theme/$theme/layout")

@section('title', 'Dashboard')

@section('contentTitle', "Dashboard")

@section('contentBreadcrum')
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item"><a href="{{route('admin.admin')}}">Home</a></li>
  <li class="breadcrumb-item active">Dashboard</li>
</ol>
@endsection

@section('content')
<livewire:admin.dashboard-component>
@endsection