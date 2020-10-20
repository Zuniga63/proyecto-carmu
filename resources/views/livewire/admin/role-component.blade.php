<section class="content" x-data>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-4">
        @include("admin.role.$view")
      </div>

      <div class="col-md-8">
        @include('admin.role.roles-table')
      </div>
    </div>


    <script>
      window.addEventListener('livewire:load', ()=>{
        
        Livewire.on('roleStored', name =>{
          functions.notifications(
            `El rol "${name}" fue almacenado`,
            'Rol Registrado!',
            'success'
          );
        });

        $(function () {
          $('[data-toggle="tooltip"]').tooltip()
        })


        Livewire.on('roleUpdated', ()=>{
          functions.notifications(
            '',
            'Rol Acualizado!',
            'success'
          );
        });

        Livewire.on('roleDeleted', (name)=>{
          functions.notifications(
            `El rol "${name}" fue eliminado`,
            '¡Menú Eliminado!',
            'success'
          );
        })

        Livewire.on('roleNotFound', (message)=>{
          functions.notifications(
            message,
            '¡Oops, algo salío mal!',
            'error'
          );
        })

      })

      const showDeleteAlert = (id, name) => {
        Swal.fire({
          title:`¿Desea eliminar el rol?`,
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
