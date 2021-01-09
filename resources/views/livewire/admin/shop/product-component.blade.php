<div>
  <div class="container-fluid" x-data="formData()"
    x-on:livewire-upload-start="isUploading = true"
    x-on:livewire-upload-finish="isUploading = false"
    x-on:livewire-upload-error="isUploading = false"
    x-on:livewire-upload-progress="progress = $event.detail.progress"
    @category-route-ids.window="loadCategoryRoute(event.detail.categoryRoute)"
  >
    <div class="row">
      <div class="col-lg-5">
        @include("admin.shop.product.$view")
      </div>

      <div class="col-lg-7">
        @include('admin.shop.product.table')
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>

  window.formData = () =>{
    return {
      allCategories: @this.allCategories,
      allTags: @this.allTags,
      allBrands: @this.allBrands,
      allSize: @this.allSize,
      allColors: @this.allColors,
      name: @entangle('name'),
      slug: @entangle('slug').defer,
      description: @entangle('description').defer,
      ref: @entangle('ref').defer,
      barcode: @entangle('barcode').defer,
      stock: 0,
      price: @entangle('price'),
      brandId: @entangle('brandId'),
      sizeId: @entangle('sizeId'),
      colorId: @entangle('colorId'),
      colorHex: @entangle('colorHex'),
      actualCategory: @entangle('actualCategory'),
      categoryRoute: @entangle('categoryRoute'),
      mainCategoryId: @entangle('mainCategoryId'),
      subcategoryId: @entangle('subcategoryId'),
      outstanding: @entangle('outstanding'),
      isNew: @entangle('isNew'),
      published: @entangle('published'),
      isUploading: false,
      progress:0,
      formatInput(target){
        let value = target.value;
        value = deleteCurrencyFormat(value);
        this.price = value;
        target.value = formatCurrency(value, 0);
      },
      changeMainCategory(){
        if(this.mainCategoryId > 0){
          let coincidence = this.allCategories.filter(c => c.id === this.mainCategoryId);
          if(coincidence.length > 0){
            let data = coincidence[0];
            let category = {
              id: data.id,
              fatherId: data.father_id,
              name: data.name,
              subcategories: data.subcategories,
              first: true,
              last: true
            }

            this.actualCategory = category;
            this.categoryRoute = [category];
          }
        }
      },
      subcategorySelected(){
        if(this.subcategoryId > 0){
          let coincidence = this.actualCategory.subcategories.filter(c => c.id === this.subcategoryId);
          if(coincidence.length > 0){
            let routeLength = this.categoryRoute.length;
            let data = coincidence[0];
            let category = {
              id: data.id,
              fatherId: data.father_id,
              name: data.name,
              subcategories: data.subcategories,
              first: routeLength === 0,
              last: true
            }

            this.actualCategory = category;
            if(routeLength > 0){
              this.categoryRoute[routeLength -1].last = false;              
            }
            this.subcategoryId = 0;
            this.categoryRoute.push(category);
          }
        }
      },
      removeSubcategory(){
        if(this.mainCategoryId && this.mainCategoryId > 0 && this.categoryRoute.length > 1){
          let lastCategory = this.categoryRoute.pop();
          let length = this.categoryRoute.length;
          this.actualCategory = this.categoryRoute[length -1];
          this.categoryRoute[length -1].last = true;
          this.subcategoryId = 0;
        }
      },
      getColorHex(){
        // colorHex = $event.target.options[$event.target.selectedIndex].getAttribute('color-hex')
        if(this.colorId){
          let id = parseInt(this.colorId);
          let color = this.allColors.filter(c => c.id === id);
          color = color.length > 0 ? color[0].hex : '';
          this.colorHex = color;
        }else{
          this.colorHex = '';
        }
      },
      loadCategoryRoute(data){
        console.log(data);
        let first = true;
        data.forEach(item => {
          if(first){
            this.mainCategoryId = item;
            this.changeMainCategory();
            first = false;
          }else{
            this.subcategoryId = item;
            this.subcategorySelected();
          }
        });
      }
    }
  }

  window.formatCurrency = (number, fractionDigits) => {
    var formatted = new Intl.NumberFormat('es-CO', {
      style: "currency",
      currency: 'COP',
      minimumFractionDigits: fractionDigits,
    }).format(number);
    return formatted;
  }

  /**
   * Este metodo se encarga de eliminar el formateado 
   * que le proporciona el metodo formatcurrency
   * y retorna un numero float
   */
  window.deleteCurrencyFormat = text => {
    let value = text.replace("$", "");
    value = value.split(".");
    value = value.join("");

    value = parseFloat(value);

    return isNaN(value) ? 0 : value;
  }

  window.formatInput = (target) => {
    let value = target.value;
    value = deleteCurrencyFormat(value);

    target.value = formatCurrency(value, 0);
  }

  document.addEventListener("livewire:load", () => {
    // $(function () {
    //   $('[data-toggle="tooltip"]').tooltip()
    //   console.log("funcionando")
    // })

    //Initialize Select2 Elements
    $('.select2').select2()

    Livewire.on('edit', ()=>{
      $('.select2').select2()
      document.getElementById('productPrice').value = formatCurrency(@this.price, 0);
    })

    // Se actualiza los indices de las etiquetas a relacionar
    $('#productTags').on('change', e =>{
      let options = [...e.target.options];
      let result = options.filter(opt => opt.selected).map(x => x.value);
      @this.tags = result;
    })

    Livewire.on('reset', ()=>{
      $('#productTags').val('').trigger('change');
      document.getElementById('productPrice').value = "";
    })

    Livewire.on('stored', ()=> {
      let title = `Los datos fueron guardados correctamente`;
      let body = '';
      let type = 'success';
      functions.notifications(body, title, type);
        });

    Livewire.on('updated', ()=>{
      functions.notifications('', '¡Datos actualizado!', 'success');
      $('.select2').select2()
    })
    
    Livewire.on('notFound', ()=>{
      let title = "¡Oops, algo salio mal!";
      let message = "Estes registro no existe";
      let type = "error";
      functions.notifications(message, title, type);
      location.reload();
    })
    
    Livewire.on('deleted', () => {
      let message = `Los datos del producto fueron eliminados del sistema`;
      functions.notifications('', message, 'success');
    })

    Livewire.on('stateUpdated', ()=>{
      let message = `El estado ha sido actualizado`;
      functions.notifications('', message, 'success');
    })
  });

  window.showDeleteAlert = (id, name) => {
    console.log(id, name);
    Swal.fire({
      title:`¿Desea eliminar este producto?`,
      text: 'Está accion elimina todas las relaciones y no puede revertirse',
      icon: 'warning', 
      showCancelButton: true,
      confirmButtonColor: 'var(--success)',
      cancelButtonColor: 'var(--primary)',
      confirmButtonText: '¡Eliminar!',
    }).then(result => {
      if(result.value){
        @this.destroy(id);
      }//end if
    })//
  }

</script>
@endpush