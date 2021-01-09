{{-- INFORMACION DEL PRODUCTO --}}
<div class="card card-default" wire:ignore.self>

  <div class="card-header" wire:ignore>
    <h3 class="card-title">Informacion del Prodcuto</h3>
    <div class="card-tools">
      <button class="btn btn-tool" data-card-widget="collapse" style="color: #adb5bd">
        <i class="fas fa-minus"></i>
      </button>
    </div>
  </div><!--./end header -->

  <div class="card-body">
    {{-- IMAGEN DEL PRODCUTO --}}
    <div class="container mb-3">
      <div class="row justify-content-around align-items-center mb-2">
        {{-- Imagen actual en el servidor --}}
        @if($view === 'edit')
          <img src="{{$this->actualProductImagePath}}" alt="" class="img-thumbnail mx-auto col-5">
        @endif
        {{-- Imagen a relacionar --}}
        @if ($view === 'edit' && ($image || $deleteActualProductImage))
          <i class="fas fa-angle-double-right col-1"></i>
        @endif
        @if ($image)
          <img src="{{$this->imagePath}}" alt="" class="img-thumbnail d-block mx-auto col-5">
        @else
          @if($view !== 'edit' || ($view === 'edit' && $deleteActualProductImage))
            <img src="{{$this->imagePath}}" alt="" class="img-thumbnail d-block mx-auto col-5">
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

        @if ($image || ($view === 'edit' && $actualImage && !$deleteActualProductImage))
        <button type="button" class="btn btn-danger" wire:click="removeImage">
          <i class="fas fa-trash"></i>
          Eliminar
        </button>
        @endif

        @if ($view === 'edit' && $actualImage && $deleteActualProductImage && !$image)
        <button type="button" class="btn btn-info" wire:click="undoImageChange">
          <i class="fas fa-undo-alt"></i>
          Deshacer
        </button>
        @endif
      </div>
    </div>

    {{-- NOMBRE DEL PRODCUTO --}}
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

    {{-- SLUG, PROPIEDAD OCULTA --}}
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

    {{-- DESCRIPCIÓN DEL PRODUCTO --}}
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

  </div><!-- ./end body -->

</div>

{{-- REFERENCIAS Y PRECIO --}}
<div class="card card-default" wire:ignore.self>

  <div class="card-header" wire:ignore>
    <h3 class="card-title">Referencias y Precio</h3>
    <div class="card-tools">
      <button class="btn btn-tool" data-card-widget="collapse" style="color: #adb5bd">
        <i class="fas fa-minus"></i>
      </button>
    </div>
  </div><!--./end header -->

  <div class="card-body">
    {{-- REF AND BARCODE --}}
    <div class="row">
      {{-- REFERENCIA DEL PRODUCTO --}}
      <div class="form-group col-6">
        <label for="productRef">Referencia</label>
        <input 
          type="text" 
          name="productRef" 
          id="productRef"
          class="form-control {{$errors->has('ref') ? 'is-invalid' : ''}}" 
          placeholder="Escribe la referencia" 
          x-model="ref"
        >
  
        @error('ref')
        <div class="invalid-feedback" role="alert">
          {{$message}}
        </div>
        @enderror
      </div>

      {{-- BARCODE --}}
      <div class="form-group col-6">
        <label for="productBarcode">Codigo de barras</label>
        <input 
          type="text" 
          name="productBarcode" 
          id="productBarcode"
          class="form-control {{$errors->has('barcode') ? 'is-invalid' : ''}}" 
          placeholder="Escribelo o escanealo" 
          x-model="barcode"
        >
  
        @error('barcode')
        <div class="invalid-feedback" role="alert">
          {{$message}}
        </div>
        @enderror
      </div>
    </div>

    {{-- PRECIO D VENTA AL PUBLICO --}}
    <div class="form-group">
      <label for="productPrice" class="required">Precio de venta: </label>
      <input 
        type="text" 
        name="productPrice" 
        id="productPrice" 
        class="form-control text-right text-bold {{$errors->has('price') ? 'is-invalid' : ''}}" 
        style="font-size: 1.3rem;"
        autocomplete="off"
        placeholder="Escribe el precio aquí"
        x-on:input="formatInput($event.target)"
        x-on:change="$wire.price=price"
        wire:ignore
      >
      <input type="text" class="{{$errors->has('price') ? 'is-invalid' : ''}} d-none">
      @error('price')
      <div class="invalid-feedback" role="alert">
        {{$message}}
      </div>
      @enderror
    </div>

  </div><!-- ./end body -->

</div>

{{-- CARACTERISTICAS --}}
<div class="card card-default" wire:ignore.self>

  <div class="card-header" wire:ignore>
    <h3 class="card-title">Caracteristicas</h3>
    <div class="card-tools">
      <button class="btn btn-tool" data-card-widget="collapse" style="color: #adb5bd">
        <i class="fas fa-minus"></i>
      </button>
    </div>
  </div><!--./end header -->

  <div class="card-body">
    <div class="container-fluid">
      {{-- TALLA Y COLOR --}}
      <div class="row">
        {{-- TALLA --}}
        <div class="col-md-6">
          <div class="form-group">
            <label for="productSize">Talla:</label>
            <select name="productSize" id="productSize" class="form-control" x-model="sizeId">
              <option value="">Sin talla</option>
              <template x-for="(size, index) in allSize" :key="index">
                <option :value="size.id" x-text="size.value"></option>
              </template>
            </select>
          </div>
        </div>
        {{-- COLOR --}}
        <div class="col-md-6">
          <div class="form-group">
            <label for="productColor">Color: 
              <i class="fas fa-palette" x-show="colorHex" x-bind:style="'color:' + colorHex + ';'"></i>
            </label>
            <select 
              name="productColor" 
              id="productColor" 
              class="form-control" 
              x-on:change="getColorHex()"
              x-model="colorId"
            >
              <option value="" color-hex="">Sin Color</option>
              <template x-for="(color, index) in allColors" :key="index">
                <option :value="color.id" :color-hex="color.hex" x-text="color.name"></option>
              </template>
            </select>
          </div>
        </div>
      </div><!-- /.end row -->

      <!-- Marca e imagen del producto -->
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label for="productBrand">Marca</label>
            <select name="name" id="productBrand" class="form-control" x-model="brandId">
              <option value="" selected>Sin marca</option>
              <template x-for="(brand, index) in allBrands" :key="index">
                <option :value="brand.id" x-text="brand.name"></option> 
              </template>
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

      @error('brandId')
      <div class="text-danger" role="alert">
        {{$message}}
      </div>
      @enderror
      @error('colorId')
      <div class="text-danger" role="alert">
        {{$message}}
      </div>
      @enderror
      @error('sizeId')
      <div class="text-danger" role="alert">
        {{$message}}
      </div>
      @enderror

    </div><!--/.end container-->

  </div><!-- ./end body -->

</div>

{{-- CATEGORIAS Y ETIQUETAS --}}
<div class="card card-default" wire:ignore.self>
  <div class="card-header" wire:ignore>
    <h3 class="card-title">Categorías y Etiquetas</h3>
    <div class="card-tools">
      <button class="btn btn-tool" data-card-widget="collapse" style="color: #adb5bd">
        <i class="fas fa-minus"></i>
      </button>
    </div>
  </div><!--./end header -->

  <div class="card-body">
    @if ($view === "edit")
    <div class="border border-warning rounded p-2 bg-light"  style="font-size: 0.85rem">
      <p class="mb-2">Por problemas técnicos, las <span class="text-bold">etiquetas</span> y <aspan class="text-bold">categorías</aspan> no pueden ser asignadas automaticamente al tratar de editar el producto, por lo que se deben asignar de forma manual nuevamente.</p>
      <p class="mb-1">Etiquetas: <span class="text-bold">{{$temporalTags}}</span></p>
      <p class="mb-1">Categorías: <span class="text-bold">{{$temporalCategories}}</span></p>
    </div>
    @endif
    {{-- ETIQUETAS --}}
    <div class="form-group">
      <label for="productTags">Etiquetas</label>
      <div class="select2-blue" wire:ignore>
        <select class="select2" multiple="multiple" data-placeholder="Selecciona o escribelas!" data-dropdown-css-class="select2-blue" style="width: 100%;" id="productTags" >
          @foreach ($allTags as $tag)
          <option value="{{$tag['id']}}">{{$tag['name']}}</option>
          @endforeach
        </select>
      </div>
      @error('tags')
      <div class="text-danger" role="alert">
        {{$message}}
      </div>
      @enderror
    </div>
    {{-- CATEGORÍA PRINCIPAL --}}
    <div class="form_group mb-4">
      <label for="mainCategory" class="required">Categoría Principal</label>
      <select 
        name="categoryId" 
        id="mainCategory" 
        class="form-control" 
        x-model.number="mainCategoryId" 
        x-on:change="changeMainCategory"
      >
        <option value="0" disabled>Selecciona una Categoría</option>
        <template x-for="(category, index) in allCategories" :key="index">
          <option x-bind:value="category.id" x-text="category.name"></option>
        </template>
      </select>
    </div>
  
    <template x-if="actualCategory" wire:ignore>
      <div x-show.transition.duration.500ms="actualCategory">
        <div class="form_group mb-4" x-show.transition.duration.500ms="actualCategory.subcategories.length > 0">
          <label for="subcategoryId">Asignar subcategoría de &quot;<span x-text="actualCategory.name"></span>&quot;</label>
          <select name="subcategoryId" id="subcategoryId" class="form-control" x-model.number="subcategoryId" x-on:change="subcategorySelected">
            <option value="0" disabled selected>Selecciona una subcategoría</option>
            <template x-for="(category, index) in actualCategory.subcategories" :key="index">
              <option x-bind:value="category.id" x-text="category.name"></option>
            </template>
          </select>
        </div>

        {{-- Ruta de categorías --}}
        {{-- <div class="container-fluid" x-show.transition="categoryRoute.length > 0">
          <div class="row justify-content-center">
            <h2 class="h5">Ruta de categorías</h2>
          </div>
          <div class="row justify-content-between align-items-center">
            <div class="row col-10 border rounded p-2">
              @foreach ($categoryRoute as $category)
                @if($loop->first)
                  <p class="border border-primary rounded px-2 mr-2 mb-1">{{$category['name']}}</p>
                  @if(!$loop->last)
                    <i class="fas fa-arrow-circle-right p-1 mr-2 mb-1 text-primary"></i>
                  @endif
                @elseif($loop->last)
                  <p class="border border-success rounded px-2 mr-2 mb-1">{{$category['name']}}</p>
                @else
                  <p class="border border-info rounded px-2 mr-2 mb-1">{{$category['name']}}</p>
                  <i class="fas fa-arrow-circle-right p-1 mr-2 mb-1 text-info"></i>
                @endif
              @endforeach
            </div>
            <button class="btn btn-danger rounded-circle col-1 p-1">
              <i class="fas fa-trash"></i>
            </button>
            
          </div>
        </div> --}}
        <div class="container-fluid" x-show.transition="categoryRoute.length > 0">
          <div class="row justify-content-center">
            <h2 class="h5">Ruta de categorías</h2>
          </div>
          <div class="row justify-content-between align-items-center">
            <div class="row col-11 border rounded p-2">
              <template x-for="(category, index) in categoryRoute" :key="index">
                <div class="d-flex">
                  <p 
                    class="border rounded px-2 mr-2 mb-1"
                    x-bind:class="{
                      'border-primary':category.first && !category.last,
                      'border-success': (category.first && category.last) || category.last,
                      'border-secundary': !category.first && !category.last
                    }"
                    x-text="category.name"
                  >
                  </p>
                  <i 
                    class="mr-2 mb-1 text-primary text-bold"
                    x-show="!category.last"
                  >|</i>
                </div>
              </template>            
            </div>

            <button 
              type="button"
              class="btn btn-danger rounded-circle col-1 p-1" 
              x-bind:class="{'disabled':categoryRoute.length <= 1}"
              x-bind:disabled="categoryRoute.length <= 1"
              x-on:click="removeSubcategory"
            >
              <i class="fas fa-trash"></i>
            </button>
          </div>

        </div>
      </div>
    </template>
    @error('categoryRoute')
    <div class="text-danger" role="alert">
      {{$message}}
    </div>
    @enderror

  </div><!-- ./end body -->

</div>