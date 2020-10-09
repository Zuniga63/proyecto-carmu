@if (!$item['submenus'])
<li class="dd-item dd3-item" data-id="{{$item["id"]}}">
  <div class="dd-handle dd3-handle"></div>
  <div class="dd3-content">
    <a href="{{url("admin/menu/".$item['id']."/editar")}}">
      {{$item['name']}} ||
      url -> {{$item['url']}} ||
      icono -> <i class="fas {{$item['icon']}}"></i>
    </a>
    <form action="{{route('admin.delete_menu', ['id' => $item['id']])}}" class="d-inline form-delete float-right" method="POST">
      @csrf @method('delete')
      <button type="submit" class="btn-action-table delete tooltipsC" title="Eliminar registro">
        <i class="fas fa-trash text-danger"></i>
      </button>
    </form>
  </div>
</li>
@else
<li class="dd-item dd3-item" data-id="{{$item['id']}}">
  <div class="dd-handle dd3-handle"></div>
  <div class="dd3-content">
    <a href="{{url("admin/menu/".$item['id']."/editar")}}">
      {{$item['name']}}
      || url -> {{$item['url']}}
      || icono -> <i class="fas {{$item['icon']}}"></i>
    </a>
  </div>
  <ol class="dd-list">
    @foreach ($item['submenus'] as $submenu)
    @include('admin.menu.menu-item', ["item" => $submenu])
    @endforeach
  </ol>
</li>
@endif