@if (!$item['subcategories'])
<li class="dd-item dd3-item" data-id="{{$item["id"]}}">
  <div class="dd-handle dd3-handle"></div>
  <div class="dd3-content">
    <i class="{{$item['icon']}}"></i>
    <span>{{$item['name']}}</span>
    <span>&nbsp;[{{$item['slug']}}]</span>
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
    <span>&nbsp;[{{$item['slug']}}]</span>
    <div class="float-right">
      <a href="#" class="btn-action-table" wire:click="edit({{$item['id']}})">
        <i class="fas fa-pencil-alt text-success" title="Editar categoría"></i>
      </a>
      
      <a href="#" class="btn-action-table" wire:click="destroy({{$item['id']}})">
        <i class="fas fa-trash text-danger" title="Eliminar categoría"></i>
      </a>
    </div>
  </div>
  <ol class="dd-list">
    @foreach ($item['subcategories'] as $key => $subcategory)
    @include('admin.shop.category.category-item', ["item" => $subcategory])
    @endforeach
  </ol>
</li>
@endif