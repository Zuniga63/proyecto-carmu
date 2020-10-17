<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-3">
        @include("admin.permission.$view")
      </div>

      <div class="col-md-9">
        @include('admin.permission.table')
      </div>
    </div>
  </div>
  <script type="text/javascript">
    document.addEventListener('livewire:load', function (){
      Livewire.on('triggerDelete', permissionId => {
          Swal.fire({
          title: '¿Desea eliminar este permiso?',
          text: 'Está accion no puede revertirse',
          icon: 'warning', 
          showCancelButton: true,
          confirmButtonColor: 'var(--success)',
          cancelButtonColor: 'var(--primary)',
          confirmButtonText: '¡Eliminar!',
        }).then((result) => {
          let type = 'success';
          if(result.value){
            @this.call('destroy', permissionId);
          }else{
            functions.notifications('', 'Operacion cancelada', type);
          }
        })
      });

      Livewire.on('triggerAssignment', (permissionID, roleId, checked) =>{
        console.log(permissionID, roleId, checked);
        @this.call('permissionAssignment', permissionID, roleId, checked);
      })

      Livewire.on('permissionCreated', ()=>{
        functions.notifications('El registro creado', '¡Permiso creado!', 'success');
      })

      Livewire.on('permissionDestroyed', ()=>{
        functions.notifications('', '¡Permiso eliminado!', 'success');
      })
      Livewire.on('permissionAssigned', ()=>{
        functions.notifications('', '¡Permiso asignado correctamente!', 'success');
      })
      Livewire.on('permissionRemoved', ()=>{
        functions.notifications('', '¡Permiso removido correctamente!', 'success');
      })
  });
    
  </script>

</section>