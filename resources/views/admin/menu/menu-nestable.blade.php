<div class="card {{$view === 'create' ? 'card-success' : 'card-primary'}}">
  <div class="card-header">
    <h3 class="card-title">Distribucion actual de los menús</h3>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <div class="row">
      <!-- Contenido de nestable -->
      <div class="dd" id="nestable">
        <ol class="dd-list">
          @foreach ($menus as $key => $item)
          @include('admin.menu.menu-item', ["item" => $item])
          @endforeach
        </ol>
      </div><!-- /.nestable -->
    </div>
  </div>
  <!-- /. card-body -->
</div>