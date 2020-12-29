<div class="card card-default">

  <div class="card-header">
    <h3 class="card-title">Informacion general</h3>
    <div class="card-tools">
      <button class="btn btn-tool" data-card-widget="collapse" style="color: #adb5bd">
        <i class="fas fa-minus"></i>
      </button>
    </div>
  </div><!--./end header -->

  <div class="card-body">
    {{-- Contenedor para administrar la imagen --}}
    <div class="container mb-3">
      <div class="row justify-content-around align-items-center mb-2">
        {{-- Imagen actual en el servidor --}}
        @if($view === 'edit')
          @if ($actualImage)
            <img src="{{asset("storage/$actualImage")}}" alt="" class="img-thumbnail mx-auto col-5">
          @else            
            <img src="{{asset('storage/img/products/no-image-available.png')}}" alt="" class="img-thumbnail mx-auto col-5">            
          @endif
        @endif
        {{-- Imagen a relacionar --}}
        @if ($image)
          @if ($view === 'edit')
            <i class="fas fa-angle-double-right col-1"></i>
          @endif
          <img src="{{$this->path}}" alt="" class="img-thumbnail d-block mx-auto col-5">
        @else
          @if($view !== 'edit')
            <img src="{{asset('storage/img/products/no-image-available.png')}}" alt="" class="img-thumbnail d-block mx-auto col-5">
          @endif
        @endif
      </div>

      <div class="row justify-content-center">
        <progress x-show.transition="isUploading" max="100" x-bind:value="progress" class="col-6"></progress>
        @error('image')
        <div class="text-danger border border-danger rounded px-2 mb-2" role="alert">
          {{$message}}
        </div>
        @enderror
      </div>

      <div class="row justify-content-around">
        <label class="btn btn-primary mb-0">
          <i class="fas fa-cloud-upload-alt"></i> Subir imagen
          <input type="file" accept="image/*" wire:model="image" class="d-none">
        </label>

        @if ($image || ($view === 'edit' && $actualImage))
        <button class="btn btn-danger">
          <i class="fas fa-trash"></i>
          Eliminar
        </button>
        @endif
      </div>
    </div>

    <div class="form-group">
      <label for="productName" class="required">Nombre</label>
      <input 
        id="productName" 
        type="text" 
        name="productName" 
        class="form-control {{$errors->has('name') ? 'is-invalid' : ''}}" 
        placeholder="Escribe el nombre del producto" 
        x-model.trim="name"
        x-on:input="slug = name.toLowerCase().replace(/\s/gi, '-').normalize('NFD').replace(/[\u0300-\u036f]/g, '')"
      >
      @error('name')
      <div class="invalid-feedback" role="alert">
        {{$message}}
      </div>
      @enderror
    </div>
    
    <div class="form-group d-none">
      <label for="productSlug" class="required" title="">Slug</label>
      <input 
        id="productSlug" 
        type="text" 
        name="productSlug" 
        class="form-control {{$errors->has('slug') ? 'is-invalid' : ''}}" 
        placeholder="Escribe el slug-del-producto"
        x-model.trim="slug"
      >
      @error('slug')
      <div class="invalid-feedback" role="alert">
        {{$message}}
      </div>
      @enderror
    </div>

    <div class="form-group">
      <label for="productDescription"  class="required">Descripción</label>
      <textarea 
        id="productDescription" 
        class="form-control {{$errors->has('description') ? 'is-invalid' : ''}}" 
        name="productDescription" 
        rows="3" 
        placeholder="Escribe una descripcion del producto"
        x-model.trim="description"
      ></textarea>
      @error('description')
      <div class="invalid-feedback" role="alert">
        {{$message}}
      </div>
      @enderror
      <template x-if="description.length > 0">
        <div class="float-right">
          <span>Longitud: </span>
          <span x-text="description.length"></span>
        </div>
      </template>
    </div>

    <div class="form-group">
      <label for="productPrice" class="required">Precio de venta: <span x-text="formatCurrencyLite(price, 0)"></span></label>
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

    <div class="form_group mb-4">
      <label for="productCategory">Categoría Principal</label>
      <select name="categoryId" id="productCategory" class="form-control" x-model.number="categoryId">
        <option value="0" selected>Sin categoría</option>
        @foreach ($categories as $id => $name)
        <option value="{{$id}}">{{$name}}</option>
        @endforeach
      </select>
    </div>

  </div><!-- ./end body -->

</div>