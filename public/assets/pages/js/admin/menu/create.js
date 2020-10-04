window.addEventListener('load', ()=>{
  functions.generalValidation('form-general');
  document.getElementById('icon').addEventListener('input', ()=>{
    const element = document.getElementById('show-icon');
    element.classList = '';
    if(document.getElementById('icon').value){
      element.classList.add('fas');
      element.classList.add(document.getElementById('icon').value);
    }
  });
})