<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
  use HasFactory;
  protected $table = 'category';
  protected $fillable = ['name', 'slug', 'icon'];
  protected $guarded = ['id'];

  /**
   * Este metodo se encarga de recuperar todas las categorías base
   * que no tienen asignado un valor en father_id
   */
  public function getBaseCategories()
  {
    return $this->whereNull('father_id')
      ->orderBy('order')
      ->get()
      ->toArray();
  }

  /**
   * Recupera todas las categorías que ya tienen asignado una categoria 
   * padre, ordenadas por el id del padre y luego por la propiedad order
   */
  public function getAllChildCategories()
  {
    return $this->whereNotNull('father_id')
      ->orderBy('father_id')
      ->orderBy('order')
      ->get()
      ->toArray();
  }

  /**
   * Recupera todo el arbol de desendientes de la categoría 
   * por lo que se vuelve a llamar con cada hijo
   */
  public function getSubcategoriesOf($fatherCategory, $allSubcategories)
  {
    $fatherSubcategories = [];

    foreach($allSubcategories as $subcategory){
      if($subcategory['father_id'] === $fatherCategory['id']){
        //Ahora se buscan las subcategorias de este
        $subcategories = $this->getSubcategoriesOf($subcategory, $allSubcategories);

        /**
         * este codigo mezcla el array primario $fatherSubcategories[array] 
         * el array subcategory al cual se le agrega la columna subcatergories
         */
        $fatherSubcategories = array_merge($fatherSubcategories, [
          array_merge($subcategory, ['subcategories' => $subcategories])
        ]);
      }//end if
    }//end forEach

    return $fatherSubcategories;
  }//end method

  public static function getCategories()
  {
    $categories = [];

    //Se crea una instancia del modelo
    $categoryModel = new Category();
    //Recupero un array con todas las categorias base
    $baseCategories = $categoryModel->getBaseCategories();
    $allSubcategories = $categoryModel->getAllChildCategories();

    //Recorro cada categorias base para recuperar las subcategorias
    foreach($baseCategories as $category){
      $subcategories = $categoryModel->getSubcategoriesOf($category, $allSubcategories);
      //Agrego la columna al array original
      $item = [array_merge($category, ['subcategories' => $subcategories])];
      $categories = array_merge($categories, $item);
    }//end foreach

    return $categories;
  }//end method

  /**
   * @param {array} $categories Listado ordenado de categorias
   */
  public function saveOrder($categories)
  {
    $categories = json_decode($categories);
    foreach($categories as $key => $category){
      /**
       * Todas las cateorías en la raiz tienen el campo
       * father_id e null
       */
      $this->assingOrder($category, null, $key +1);
    }
  }

  /**
   * @param {object} $category Objeto json con el id de la categoría y los children
   * @param {int} $fatherId Identificador de la categoría padre
   * @param {int} $order Es la ubicacion con la que se muestra
   */
  public function assingOrder($category, $fatherId, $order)
  {
    $this->where('id', $category->id)->update([
      'father_id' => $fatherId,
      'order' => $order
    ]);

    if(!empty($category->children)){
      $subcategories = $category->children;
      foreach($subcategories as $index => $subcategory){
        $this->assingOrder($subcategory, $category->id, $index + 1);
      }
    }
  }
}//end class
