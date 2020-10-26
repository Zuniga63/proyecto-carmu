<div class="card card-default">

  <div class="card-header">
    <h3 class="card-title">Precio, stock y marca</h3>
    <div class="card-tools">
      <button class="btn btn-tool" data-card-widget="collapse" style="color: #adb5bd">
        <i class="fas fa-minus"></i>
      </button>
    </div>
  </div><!--./end header -->

  <div class="card-body">
    <div class="container-fluid">
      <!-- Precio y unidades en stock -->
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label for="productPrice" class="required">Precio: <span x-text="formatCurrencyLite(price, 0)"></span></label>
            <input 
              type="text" 
              name="productPrice" 
              id="productPrice" 
              class="form-control {{$errors->has('price') ? 'is-invalid' : ''}}" 
              placeholder="Pj: 30000"
              x-model.number="price"
            >
            @error('price')
            <div class="invalid-feedback" role="alert">
              {{$message}}
            </div>
            @enderror
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="producStock" class="required">Unidades en stock</label>
            <input 
              type="number" 
              min="0"
              max="255"
              name="producStock" 
              id="productStock" 
              class="form-control {{$errors->has('stock') ? 'is-invalid' : ''}}" 
              placeholder="Pj: 100"
              x-model.number="stock"
            >
            @error('stock')
            <div class="invalid-feedback" role="alert">
              {{$message}}
            </div>
            @enderror
          </div>
        </div>
      </div><!-- /.end row -->

      <!-- Marca e imagen del producto -->
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label for="productBrand">Marca</label>
            <select name="name" id="productBrand" class="form-control" x-model.number="brandId">
              <option value="0" selected>Sin marca</option>
              @foreach ($brands as $brand)
              <option value="{{$brand->id}}">{{$brand->name}}</option>                  
              @endforeach
            </select>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <div class="custom-control custom-switch">
              <input type="checkbox" class="custom-control-input" id="outstanding" x-model="outstanding">
              <label for="outstanding" class="custom-control-label">Destacado</label>
            </div>
            <div class="custom-control custom-switch">
              <input type="checkbox" class="custom-control-input" id="isNew" x-model="isNew">
              <label for="isNew" class="custom-control-label">Nuevo</label>
            </div>
            <div class="custom-control custom-switch">
              <input type="checkbox" class="custom-control-input" id="published" x-model="published">
              <label for="published" class="custom-control-label">Publicar</label>
            </div>
          </div>
        </div>
      </div><!-- /.end row -->

    </div><!--/.end container-->

  </div><!-- ./end body -->

</div>