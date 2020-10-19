<div class="card card-success">
  <div class="card-header">
    <h3 class="card-title">Distribucion de categorías</h3>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <div class="row">
      <!-- Contenido de nestable -->
      <div class="dd" id="nestable">
        <ol class="dd-list">
          @foreach ($categories as $key => $item)
          @include('admin.shop.category.category-item', ["item" => $item])
          @endforeach
        </ol>
      </div><!-- /.nestable -->
    </div>
  </div>
  <!-- /. card-body -->
</div>