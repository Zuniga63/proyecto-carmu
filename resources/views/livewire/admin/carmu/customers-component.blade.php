<div class="container-fluid">
  <div class="row">
    <div class="col-lg-4">
      @include("admin.carmu.customers.$view")
    </div>
    <div class="col-lg-8">
      @include('admin.carmu.customers.table')
    </div>
  </div>

  <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteModalLongTitle">¿Está seguro que desea eliminar al cliente?</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Esto eliminará los datos del cliente de la base datos de forma permanente e irreversible.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-danger" wire:click="$emit('destroy-customer')" data-dismiss="modal">Eliminar Cliente</button>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
    
<script>
  window.customerId = null;
  document.addEventListener("DOMContentLoaded", () => {
    Livewire.on('stored', ()=> {
      let title = `El cliente ha sido guardado`;
        let body = '';
        let type = 'success';
        functions.notifications(body, title, type);
    });

    Livewire.on('save-id', customerId => {
      window.customerId = customerId;
    })
      
    Livewire.on('destroy-customer', () =>{
      console.log('destroy customer')
      @this.destroy(window.customerId);
    })

    Livewire.on('updated', ()=>{
      functions.notifications('', '¡Datos actualizado!', 'success');
    })
        
    Livewire.on('error', (message)=>{
      let title = "¡Oops, algo salio mal!";
      let type = "error";
      functions.notifications(message, title, type);
    })
    
    Livewire.on('deleted', () => {
      let message = `El cliente fue eliminado correctamente`;
      functions.notifications('', message, 'success');
    })

    Livewire.on('archived', (message)=>{
      functions.notifications('', message, 'success');
    })
  });
  
</script>
@endpush