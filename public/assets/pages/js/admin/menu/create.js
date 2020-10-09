window.addEventListener('load', ()=>{
  functions.generalValidation('form-general');
  document.getElementById('icon').addEventListener('input', ()=>{
    showIcon();
  });
})

const showIcon = () =>{
  /**
   * Se recupera el elemento y se limpia su listado
   * de clases
   */
  const element = document.getElementById('show-icon');
  element.classList = '';

  /**
   * Recupero las clases de los iconos
   * y las separo para signarlas al elemento
   */

  const classIcons = document.getElementById('icon').value;
  const array = classIcons.split(' ');
  array.forEach(item => {
    item = item.trim();
    if(item){
      element.classList.add(item);
    }
  });
}