<?php

namespace App\Http\Livewire\Admin\Carmu;

use Livewire\Component;

class SalesComponent extends Component
{
	public $view = 'create';
	
	public function render()
	{
		return view('livewire.admin.carmu.sales-component')
			->layout('admin.carmu.sales.index');
	}
}
