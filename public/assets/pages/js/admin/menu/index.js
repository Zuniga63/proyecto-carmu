window.addEventListener('load', () => {
  $('#nestable').nestable().on('change', function () {
    let menu = JSON.stringify($('#nestable').nestable('serialize'));
    let token = document.querySelector('input[name=_token]').value;

    fetch('/admin/menu/guardar-orden', {
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json, text-plain, */*",
        "X-Requested-With": "XMLHttpRequest",
        "X-CSRF-TOKEN": token
      },
      method: 'post',
      credentials: "same-origin",
      body: JSON.stringify({menu})
    }).then((data) => data.json())
      .then(data => {
        let message = "La nueva distribucion se ha guardado en el sistema";
        let title = "Orden guardado!"
        functions.notifications(message, title, 'success');
        console.log(data);
      })
      .catch(function (error) {
        console.log(error);
      });

  })
})