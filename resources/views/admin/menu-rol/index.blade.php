@extends("theme.$theme.layout")
@section('title', 'Menu - Rol')

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.12"></script>
<script>
  const baseRoles = @json($roles);
  const baseMenus = @json($menus);
  const rolHasMenu = @json($rolHasMenu);
  const url = "{{route('admin.store_menu_rol')}}";
  const _token = "{{ csrf_token() }}"
</script>
<script src="{{asset('assets/pages/js/admin/menu-role/index.js')}}"></script>
@endsection

@section('contentTitle', "Asignacion de menus")
@section('contentBreadcrum')
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item"><a href="{{route('admin.admin')}}">Home</a></li>
  <li class="breadcrumb-item active">Asignacion de menús</li>
</ol>
@endsection

@section('content')
<div id="app">
  <menu-list :roles="roles" :menus="menus" :rol-menu="rolHasMenu" @@menu-checked="onMenuChecked"></menu-list>
</div>
@endsection