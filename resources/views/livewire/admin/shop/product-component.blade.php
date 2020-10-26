<div>
  <div class="container-fluid" x-data="{
    name: @entangle('name'),
    slug: @entangle('slug').defer,
    description: @entangle('description'),
    stock: @entangle('stock'),
    price: @entangle('price'),
    brandId: @entangle('brandId'),
    outstanding: @entangle('outstanding'),
    isNew: @entangle('isNew'),
    published: @entangle('published'),
    isUploading: false,
    progress:0,
    }"
    x-on:livewire-upload-start="isUploading = true"
    x-on:livewire-upload-finish="isUploading = false"
    x-on:livewire-upload-error="isUploading = false"
    x-on:livewire-upload-progress="progress = $event.detail.progress"
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

  <script>
    /**
    * Esta funcion generica funciona para dar formato de moneda a los numeros pasados como parametros
    * @param {string} locales Es el leguaje Eje: es-CO
    * @param {string} currency Eltipo de moneda a utilizar ej: COP
    * @param {number} fractionDigits El numero de digitos decimales que se van a mostrar
    * @param {number} number Es la cantidad de dinero que se va a dar formato
    */
    function formatCurrency(locales, currency, fractionDigits, number) {
      var formatted = new Intl.NumberFormat(locales, {
        style: "currency",
        currency: currency,
        minimumFractionDigits: fractionDigits,
      }).format(number);
      return formatted;
    }

    /**
    * Esta es una version simplificada de formatCurreny para moneda colombiana
    * @param {number} number Numero para establecer formato
    * @param {number} fractionDigits Fracciones a mostrar
    */
    function formatCurrencyLite(number, fractionDigits) {
      return formatCurrency("es-CO", "COP", fractionDigits, number);
    }

    document.addEventListener("DOMContentLoaded", () => {
      $(function () {
        $('[data-toggle="tooltip"]').tooltip()
        console.log("funcionando")
      })
  
      Livewire.on('stored', ()=> {
        let title = `Los datos fueron guardados correctamente`;
        let body = '';
        let type = 'success';
        functions.notifications(body, title, type);
          });
  
      Livewire.on('updated', ()=>{
        functions.notifications('', '¡Datos actualizado!', 'success');
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

    window.initTooltips = ()=>{
      $('[data-toggle="tooltip"]').tooltip()
        console.log("funcionando")
    }
  </script>
</div>