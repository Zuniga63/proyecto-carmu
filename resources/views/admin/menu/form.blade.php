@include('includes.form-error')
@include('includes.message')
<!-- Horizontal Form -->
<div class="card card-success">
  <div class="card-header">
    <h3 class="card-title">Crear Menú</h3>
  </div>
  <!-- /.card-header -->
  <!-- form start -->
  <form class="form-horizontal" id="form-general" method="POST" action="{{route('admin.menu_store')}}">
    @csrf
    <div class="card-body">
      <div class="form-group row">
        <label for="name" class="col-lg-2 col-form-label required">Nombre</label>
        <div class="col-lg-9">
          <input type="text" class="form-control" id="name" name="name" placeholder="Escribe el nombre aquí"
            value="{{old('name')}}" required>
        </div>
      </div>
      <div class="form-group row">
        <label for="url" class="col-lg-2 col-form-label required">Url</label>
        <div class="col-lg-9">
          <input type="text" class="form-control" id="url" name="url" placeholder="Escribe la ruta aquí" required
            value="{{old('url')}}">
        </div>
      </div>
      <div class="form-group row">
        <label for="icon" class="col-lg-2 col-form-label">Icono</label>
        <div class="col-lg-9">
          <input type="text" class="form-control" id="icon" name="icon" placeholder="Escribe la clase del icono"
            value="{{old('icon')}}">
        </div>
        <span class="col-lg-1">
          <i class="fas {{old('icon')}}" id="show-icon"></i>
        </span>
      </div>
    </div>
    <!-- /.card-body -->
    <div class="card-footer">
      <button type="submit" class="btn btn-success">Crear</button>
      <button type="reset" class="btn btn-default float-right">Cancelar</button>
    </div>
    <!-- /.card-footer -->
  </form>
</div>
<!-- /.card -->