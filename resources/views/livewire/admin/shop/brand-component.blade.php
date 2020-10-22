<div class="content">
  <div 
    class="container-fluid" 
    x-data="{
      name: @entangle('name').defer,
      slug: @entangle('slug'),
    }"
  >
    <div class="row">
      <div class="col-md-4">
        @include("admin.shop.brand.$view")
      </div>

      <div class="col-md-8">
        @include('admin.shop.brand.table')
      </div>
    </div>
  </div>

  <script>
    window.addEventListener('livewire:load', ()=>{  
      
      $(function () {
        $('[data-toggle="tooltip"]').tooltip()
      })
  
      Livewire.on('brandStored', ()=> {
        let title = `Los datos fueron guardados correctamente`;
        let body = '';
        let type = 'success';
        functions.notifications(body, title, type);
          });
  
      Livewire.on('brandUpdated', ()=>{
        functions.notifications('', '¡Datos actualizado!', 'success');
      })
      
      Livewire.on('brandNotFound', ()=>{
        let title = "¡Oops, algo salio mal!";
        let message = "Estes registro no existe";
        let type = "error";
        functions.notifications(message, title, type);
        location.reload();
      })
      
      Livewire.on('brandDeleted', tagName => {
        let message = `La marca fue eliminada del sistema`;
        functions.notifications('', message, 'success');
      })
    })
  
    window.showDeleteAlert = (id, name) => {
      console.log(id, name);
      Swal.fire({
        title:`¿Desea eliminar esta marca?`,
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

  </div>
