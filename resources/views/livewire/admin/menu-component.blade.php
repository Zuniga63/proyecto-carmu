<section class="content" x-data>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-4">
        @include("admin.menu.$view")
      </div>

      <div class="col-md-8">
        @include('admin.menu.menu-nestable')
      </div>
    </div>


    <script>
      window.addEventListener('livewire:load', ()=>{
        $('#nestable').nestable().on('change', function() {
          let menus = JSON.stringify($('#nestable').nestable('serialize'));
          @this.saveOrder(menus);
        })

        Livewire.on('menuStored', name =>{
          functions.notifications(
            `El menú "${name}" fue almacenado`,
            '¡Menú Registrado!',
            'success'
          );
        });

        Livewire.on('menuUpdated', ()=>{
          functions.notifications(
            '',
            '¡Menú Acualizado!',
            'success'
          );
        });

        Livewire.on('menuDeleted', (name)=>{
          functions.notifications(
            `El menú "${name}" fue eliminado`,
            '¡Menú Eliminado!',
            'success'
          );
        })

        Livewire.on('menuOrderSaved', ()=>{
          functions.notifications(
            ``,
            '¡La nueva distribucion fue almacenada!',
            'success'
          );
        })
      })

      const showDeleteAlert = (id, name) => {
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
  </div>