@if (!$item['submenus'])
<li class="dd-item dd3-item" data-id="{{$item["id"]}}">
  <div class="dd-handle dd3-handle"></div>
  <div class="dd3-content">
    <i class="{{$item['icon']}}"></i>
    <span>{{$item['name']}}</span>
    <div class="float-right">
      <a href="#" class="btn-action-table" wire:click="edit({{$item['id']}})">
        <i class="fas fa-pencil-alt text-success" title="Editar categoría"></i>
      </a>

      <a href="#" class="btn-action-table" wire:click="destroy({{$item['id']}})">
        <i class="fas fa-trash text-danger" title="Eliminar categoría"></i>
      </a>
    </div>
  </div>
</li>
@else
<li class="dd-item dd3-item" data-id="{{$item['id']}}">
  <div class="dd-handle dd3-handle"></div>
  <div class="dd3-content">
    <i class="{{$item['icon']}}"></i>
    <span>{{$item['name']}}</span>
    <div class="float-right">
      <a href="#" class="btn-action-table" wire:click="edit({{$item['id']}})">
        <i class="fas fa-pencil-alt text-success" title="Editar categoría"></i>
      </a>
    </div>
  </div>

  <ol class="dd-list">
    @foreach ($item['submenus'] as $submenu)
    @include('admin.menu.menu-item', ["item" => $submenu])
    @endforeach
  </ol>
</li>
@endif