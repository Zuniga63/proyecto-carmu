<?php

namespace App\Http\Livewire\Admin\Shop;

use App\Models\Shop\Tag;
use Livewire\Component;
use Livewire\WithPagination;

class TagComponent extends Component
{
  use WithPagination;

  public $view = "create";
  public $tagId = null;
  public $name = '';
  public $slug = '';

  protected function rules()
  {
    return [
      'name' => 'required|max:50|unique:tag,name,' . $this->tagId,
      'slug' => 'required|max:50|unique:tag,slug,' . $this->tagId,
    ];
  }

  protected $attributes = [ 'name' => 'nombre'];

  public function updated($propertyName)
  {
    $this->validateOnly($propertyName, $this->rules(), [], $this->attributes);
  }

  public function render()
  {
    $tags = Tag::orderBy('name')->get();
    return view('livewire.admin.shop.tag-component', compact('tags'));
  }

  public function resetFields()
  {
    $this->reset('name', 'slug', 'view', 'tagId');
  }

  public function store()
  {
    $this->name = trim($this->name);
    $this->slug = trim($this->slug);
    $this->validate($this->rules(), [], $this->attributes);
    Tag::create([
      'name' => $this->name,
      'slug' => $this->slug,
    ]);
    $this->emit('tagStored', $this->name);
    $this->resetFields();
  }

  protected function findTag($id)
  {
    $tag = Tag::find($id, ['id', 'name', 'slug']);

    if($tag !== null)
    {
      return $tag;
    }else{
      $message = "El recurso no existe";
      $this->emit('tagNotFound', $message);
    }

    return null;
  }

  public function destroy($id)
  {
    // dd($id);
    $tag = $this->findTag($id);
    if($tag){
      $tag->delete();
      $this->emit('tagDeleted', $tag->name);
      $this->resetFields();
    }
  }

  public function edit($id)
  {
    $tag = $this->findTag($id);
    if($tag){
      $this->tagId = $tag->id;
      $this->name = $tag->name;
      $this->slug = $tag->slug;
      $this->view = 'edit';
    }else{
      $this->resetFields();
    }
  }

  public function update()
  {
    $this->name = trim($this->name);
    $this->slug = trim($this->slug);
    $this->validate($this->rules(), [], $this->attributes);
    $tag = $this->findTag($this->tagId);
    if($tag){
      $tag->update([
        'name' => $this->name,
        'slug' => $this->slug
      ]);

      $this->resetFields();
      $this->emit('tagUpdated');
    }
  }
}
