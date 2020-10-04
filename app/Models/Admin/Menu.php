<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
  use HasFactory;
  protected $table = 'menu';
  protected $fillable = ['name', 'url', 'icon'];
  protected $guarded = ['id'];

  /**
   * Este metodo se encarga de recuperar todas los
   * menus que no son hijos de otros menus segun su orden. 
   * Retorna un array de objetos y no una collection
   */
  public function getRootMenus()
  {
    /**
     * Con el metodo this, lo que hago es llamar a 
     * este modelo y aplicar una clase de Eloquent
     */
    return $this->whereNull('father_id')
      ->orderBy('order')
      ->get()
      ->toArray();
  }

  /**
   * Recuepera todas las tupla que tienen un valor asignado
   * para el campo father_id e incluye, hijos, nietos, etc.
   */
  public function getAllMenuChildren()
  {
    return $this->whereNotNull('father_id')
      ->orderBy('father_id')
      ->orderBy('order')
      ->get()
      ->toArray();
  }

  /**
   * Recupera todos los menus que descienden del menú
   * padre, lo que hace esto una funcion recursiva.
   */
  public function getChildrenOf($father, $allChildren)
  {
    $children = [];
    foreach ($allChildren as $child) {
      if ($child['father_id'] === $father['id']) {
        //Se buscan los hijos de este menú
        $grandChildren = $this->getChildrenOf($child, $allChildren);
        //Se agregan todos los descendientes
        $children = array_merge($children, [
          array_merge($child, ['submenus' => $grandChildren])
        ]);
      }
    }

    return $children;
  }

  /**
   * Recupera todos los menus raiz junto
   * con toda su descendencia.
   */
  public static function getMenus()
  {
    $allMenus = [];
    //Se crea una instancia  de Menu
    $menu = new Menu();
    $parents = $menu->getRootMenus();
    $allChildren = $menu->getAllMenuChildren();

    foreach($parents as $father){
      $chidren = $menu->getChildrenOf($father, $allChildren);
      $item = [array_merge($father, ['submenus' => $chidren])];
      $allMenus = array_merge($allMenus, $item);
    }

    return $allMenus;
  }

  public function saveOrder($menus){
    $menus = json_decode($menus);
    foreach($menus as $key => $menu){
      $this->assignOrder($menu, null, $key +1);
    }//End of forEach
  }//end saveOrder

  protected function assignOrder($menu, $fatherID, $order)
  {
    //Se actualiza el orden actual
    $this->where('id', $menu->id)->update(['father_id' => $fatherID, 'order' => $order]);
    if(!empty($menu->children)){
      $submenus = $menu->children;
      foreach($submenus as $index => $submenu){
        $this->assignOrder($submenu, $menu->id, $index +1);
      }
    }
  }//end assignOrder
}
