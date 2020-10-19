<section class="content" x-data>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-4">
        @include("admin.shop.category.$view")
      </div>

      <div class="col-md-8">
        @include('admin.shop.category.nestable')
      </div>
    </div>

  </div>
  <script>
    window.addEventListener('livewire:load', ()=>{  
        
        $('#nestable').nestable().on('change', function() {
          let categories = JSON.stringify($('#nestable').nestable('serialize'));
          // console.log(categories)
          // $wire.saveOrder(categories);
          @this.saveOrder(categories);
        });//end of nestable
        
        Livewire.on('categoryStored', (name)=> {
          let title = "¡Categoría registrada!";
          let body = `La categoría ${name} fue almacenada`;
          let type = 'success';
          functions.notifications(body, title, type);
        });

        Livewire.on('categoryUpdated', ()=>{
          functions.notifications('', '¡Datos actualizado!', 'success');
        })

        Livewire.on('categoryNotFound', message=>{
          let title = "¡Oops, algo salio mal!";
          let type = "error";
          functions.notifications(message, title, type);
        })

        Livewire.on('categoryDeleted', message => {
          functions.notifications('', message, 'success');
        })

        Livewire.on('categoryOrderSaved', ()=>{
          const message = "La nueva distribucion se guardó correctamente";
          functions.notifications('', message, 'success');
        })
      })
  </script>