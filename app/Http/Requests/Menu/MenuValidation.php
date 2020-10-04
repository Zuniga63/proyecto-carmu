<?php

namespace App\Http\Requests\Menu;

use App\Rules\Menu\ValidateFieldUrl;
use Illuminate\Foundation\Http\FormRequest;

class MenuValidation extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'name' => 'required|max:50|unique:menu,name,' . $this->route('id'),
      'url' => ['required', 'max:100', new ValidateFieldUrl],
      'icon' => 'nullable|max:50'
    ];
  }

  public function attributes()
  {
    return [
      'name' => 'nombre',
      'icon' => 'icono'
    ];
  }
}
