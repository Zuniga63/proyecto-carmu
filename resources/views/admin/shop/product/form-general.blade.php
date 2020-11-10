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
    
    <div class="form-group">
      <label for="productSlug" class="required">Slug</label>
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

    <div class="form_group mb-4">
      <label for="productCategory">Categoría Principal</label>
      <select name="categoryId" id="productCategory" class="form-control" x-model.number="categoryId">
        <option value="0" selected>Sin categoría</option>
        @foreach ($categories as $id => $name)
        <option value="{{$id}}">{{$name}}</option>
        @endforeach
      </select>
    </div>

    <div class="custom-file mb-2">
      <input 
        type="file" 
        class="custom-file-input {{$errors->has('image') ? 'is-invalid' : ''}}" 
        id="customFile"
        accept="image/*" 
        wire:model="image"
      >
      <label class="custom-file-label" for="customFile">Elige la imagen</label>
      @error('image')
      <div class="invalid-feedback" role="alert">
        {{$message}}
      </div>
      @enderror
    </div>
    {{-- <div wire:loading wire:target="image">Cargando imagen...</div> --}}
    <div x-show="isUploading">
      <progress max="100" x-bind:value="progress"></progress>
    </div>

    @if ($view==="edit")
      <div class="container">
        @if ($image)
          <div class="row">
            <div class="col-lg-6">
              <div class="card">
                <h5 class="card-header">Actual</h5>
              </div>
              <div class="card-body">
                <img src="{{url($actualImage ? "storage/$actualImage" : 'storage/img/no-image-available.png')}}" alt="{{$name}}" class="img-thumbnail mx-auto">
              </div>
            </div>
            <div class="col-lg-6">
              <div class="card">
                <h5 class="card-header">Nueva</h5>
              </div>
              <div class="card-body">
                <img src="{{$this->path}}" alt="{{$name}}" class="img-thumbnail mx-auto">
              </div>
            </div>
          </div>
        @else
          <div class="contaier">
            <img src="{{url($actualImage ? "storage/$actualImage" : 'storage/img/no-image-available.png')}}" alt="{{$name}}" class="img-thumbnail mx-auto">
          </div>
        @endif
      </div>
    @else
      @if ($image)
      <div class="contaier">
        <img src="{{$this->path}}" alt="" class="img-thumbnail mx-auto">
      </div>
      @endif
    @endif


  </div><!-- ./end body -->

</div>