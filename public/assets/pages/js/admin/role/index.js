window.addEventListener('load', () => {
  //Recupero todos los formularios para eliminar
  const formDeletes = document.querySelectorAll('.form-delete');
  //A cada formulario le evito recargar
  formDeletes.forEach(form => {
    form.addEventListener('submit', e => {
      e.preventDefault();
      Swal.fire({
        title: '¿Estas seguro que deseas eliminar este registro?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, ¡eliminalo!'
      }).then((result) => {
        if (result.isConfirmed) {
          // Swal.fire('¡Eliminado!', 'El registro ha sido borrado', 'success');
          ajaxRequest(form);
        }
      })
    })
  });//end forEach
})


const ajaxRequest = async form => {
  let url = form.getAttribute('action');
  let formData = new FormData(form);

  const res = await fetch(url, {
    headers: {
      "Content-Type": "application/json",
      "Accept": "application/json, text-plain, */*",
      "X-Requested-With": "XMLHttpRequest",
      "X-CSRF-TOKEN": formData.get('_token')
    },
    credentials: "same-origin",
    method: 'DELETE',
    body: formData,
  })

  const data = await res.json();

  if(data.message === 'ok'){
    Swal.fire('¡Eliminado!', 'El registro ha sido borrado', 'success');
    const td = form.parentElement;
    const tr = td.parentElement;
    const tbody = tr.parentElement;
    tbody.removeChild(tr);
  }
}