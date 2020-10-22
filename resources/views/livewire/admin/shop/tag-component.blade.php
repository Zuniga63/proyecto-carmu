<section class="content">
  <div 
    class="container-fluid" 
    x-data="{name:@entangle('name').defer, slug:@entangle('slug')}"
  >
    <div class="row">
      <div class="col-md-4">
        @include("admin.shop.tag.$view")
      </div>

      <div class="col-md-8">
        @include('admin.shop.tag.table')
      </div>
    </div>
  </div>

  <script>

    
  window.addEventListener('livewire:load', ()=>{  
    
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })

    Livewire.on('tagStored', (name)=> {
      let title = `La etiqueta ${name} fue almacenada`;
      let body = '';
      let type = 'success';
      functions.notifications(body, title, type);
        });

    Livewire.on('tagUpdated', ()=>{
      functions.notifications('', '¡Datos actualizado!', 'success');
    })
    
    Livewire.on('tagNotFound', message=>{
      let title = "¡Oops, algo salio mal!";
      let type = "error";
      functions.notifications(message, title, type);
      location.reload();
    })
    
    Livewire.on('tagDeleted', tagName => {
      let message = `La etiqueta "${tagName}" fue borrada`;
      functions.notifications('', message, 'success');
    })
  })

    window.showDeleteAlert = (id, name) => {
      console.log(id);
        Swal.fire({
          title:`¿Desea eliminar el menú?`,
          text: 'Está accion no puede revertirse',
          icon: 'warning', 
          showCancelButton: true,
          confirmButtonColor: 'var(--success)',
          cancelButtonColor: 'var(--primary)',
          confirmButtonText: '¡Eliminar!',
        }).then(result => {
          if(result.value){
            @this.call('destroy', id);
          }//end if
        })//
      }
  </script>
</section>