<?php
/**
 * Esta es una funcion global para poder definir si
 * un mnú esta en la ruta de su href. Esto se registra en
 * el archivo composer.json y despues
 * ejecutar composer dump-autoload
 */
if(!function_exists('getMenuActive')){
  function getMenuActive($url)
  {
    if(request()->is($url)){
      return 'active';
    }
    return '';
  }
}