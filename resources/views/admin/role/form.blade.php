@include('includes.form-error')
@include('includes.message')
<form class="form-horizontal" id="form-general" action="{{route('admin.store_role')}}" method="POST">
  @csrf
  <div class="card-body">
    <div class="form-group row">
      <label for="name" class="col-lg-2 col-form-label required">Nombre</label>
      <div class="col-lg-9">
        <input type="text" class="form-control" id="name" name="name" placeholder="Escribe el nombre aquí"
          value="{{old('name')}}" required>
      </div>
    </div>
  </div>
  <!-- /.card-body -->
  <div class="card-footer">
    <button type="submit" class="btn btn-success">Crear</button>
    <button type="reset" class="btn btn-default float-right">Cancelar</button>
  </div>
  <!-- /.card-footer -->
</form>