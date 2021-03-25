<div class="container-fluid">
  <div class="row">
    <div class="col-lg-4">
      @include("admin.shop.colors.$view")
    </div>
    <div class="col-lg-6">
      @include('admin.shop.colors.table')
    </div>
  </div>
</div>

@push('scripts')
<script>
  window.addEventListener('livewire:load', ()=>{
  
    Livewire.on('colorStored', ()=> {
      let title = `Los datos fueron guardados correctamente`;
      let body = '';
      let type = 'success';
      functions.notifications(body, title, type);
    });
    
    Livewire.on('colorUpdated', ()=>{
      functions.notifications('', '¡Datos actualizado!', 'success');
    })
    
    Livewire.on('colorNotFound', ()=>{
      let title = "¡Oops, algo salio mal!";
      let message = "Estes registro no existe";
      let type = "error";
      functions.notifications(message, title, type);
      // location.reload();
    })

    Livewire.on('error', ()=>{
      let title = "¡Oops, algo salio mal en el servidor!";
      let message = "";
      let type = "error";
      functions.notifications(message, title, type);
      // location.reload();
    })
    
    Livewire.on('colorDeleted', ()=> {
      let message = `La talla fue eliminada del sistema`;
      functions.notifications('', message, 'success');
    })
  })
  window.showDeleteAlert = (id, name) => {
    console.log(id, name);
    Swal.fire({
      title:`¿Desea eliminar esta talla?`,
      text: 'Está accion elimina todas las relaciones de esta talla con los producto y no puede revertirse',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: 'var(--success)',
      cancelButtonColor: 'var(--primary)',
      confirmButtonText: '¡Eliminar!',})//end primise
      .then(result => {
        if(result.value){
          @this.destroy(id);
        }//end if
      })//
    }
</script>
@endpush