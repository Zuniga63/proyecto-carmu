<section class="content" x-data>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-4">
        @include("admin.menu.$view")
      </div>

      <div class="col-md-8">
        @include('admin.menu.menu-nestable')
      </div>
    </div>

  </div>