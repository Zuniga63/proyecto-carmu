<?php

namespace App\Http\Controllers;

use App\Models\Shop\Category;
use App\Models\Shop\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $categories = Category::whereNull('father_id')
      ->orderBy('order')
      ->get(['id', 'name', 'slug']);

    $products = Product::where('published', 1)
      ->where('is_new', 1)
      ->whereNotNull('img')
      ->inRandomOrder()
      ->take(8)
      ->get();

    $title = "Lo más reciente de nuestro catálogo";
    $metaTitle = "Home";
    $metaDescription = "Somos distribuidores oficiales de la marca TOTTO, ROTT+CO, BLUE INC entre otras y poseemos un amplio catálogo de relojería de la marca Q&Q en la jagua de Ibirico";
    $metaKeywords = "La jagua de Ibirico, ROTT+CO, TOTTO, Q&Q, Q&Q Superior, Ropa, Relojería, Accesorios";

    $home = true;

    return view('welcome', compact('products', 'title', 'categories', 'metaTitle', 'metaDescription', 'metaKeywords', 'home'));
  }

  public function catalog($categorySlug = null)
  {
    $title = "";
    $metaTitle = "Home";
    $metaDescription = "Catalogo de productos mas recientes.";
    $metaKeywords = "La jagua de Ibirico";
    $products = [];
    $categories = Category::whereNull('father_id')
      ->orderBy('order')
      ->get(['id', 'name', 'slug']);

    if ($categorySlug) {
      $category = Category::where('slug', 'like', '%' . $categorySlug . '%')->first();
      $metaTitle = $category->name;
      if ($category) {
        $title = "Catálogo de $category->name";
        $products = DB::table('product')
          ->join('category_has_product', 'product.id', '=', 'category_has_product.product_id')
          ->where('category_has_product.category_id', $category->id)
          ->where('product.published', 1)
          ->whereNotNull('product.img')
          ->latest('product.updated_at')
          ->get('product.*');
      } else {
        return redirect(url('/catalogo'));
      }
    } else {
      $title = "Catálogo General";
      $products = Product::where('published', 1)
        ->whereNotNull('img')
        ->latest('updated_at')
        ->get();
    }

    $home = false;
    return view('welcome', compact('products', 'title', 'categories', 'metaTitle', 'metaDescription', 'metaKeywords', 'home'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    //
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    //
  }
}
