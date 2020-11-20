<?php

namespace App\Http\Livewire\Admin\Carmu;

use App\Models\OldSystem\Customer;
use Livewire\Component;
use Livewire\WithPagination;

class CustomersComponent extends Component
{
  use WithPagination;
  public $view = "create";
  public $customerId = null;
  

  //------------------------------------------------------------------------------------
  //  PROPIEDADES DEL FORMULARIO
  //------------------------------------------------------------------------------------
  public $firstName = "";
  public $lastName = null;
  public $nit = null;
  public $phone = null;
  public $email = null;
  //------------------------------------------------------------------------------------
  //  PROPIEDADES COMPUTADAS
  //------------------------------------------------------------------------------------
  /**
   * Sirve para poder filtrar los datos de los clientes por nombre, apellidos o telefono
   */
  public $search = "";
  protected $queryString = ['search' => ['except' => '']];

  public function getCustomersProperty()
  {
    $attributes = ['customer_id', 'first_name', 'last_name', 'phone', 'archived'];
    $customers = [];

    if (!empty(trim($this->search))) {
      $customers = Customer::where('first_name', 'like', "%$this->search%")
        ->orWhere('last_name', 'like', "%$this->search%")
        ->orWhere('phone', 'like', "%$this->search%")
        ->orderBy('first_name')
        ->get($attributes);
    } else {
      $customers = Customer::orderBy('first_name')
        ->get($attributes);
    }


    foreach ($customers as $customer) {
      $customer->balance = $customer->credits()->sum('amount') - $customer->payments()->sum('amount');
    }
    return $customers;
  }

  /**
   * Obtiene el saldo de los clientes que no se encuentran archivados
   */
  public function getBalanceProperty()
  {
    $balance = 0;
    foreach ($this->customers as $customer) {
      if (!$customer->archived) {
        $balance += $customer->balance;
      }
    }

    return $balance;
  }

  public function getArchivedBalanceProperty()
  {
    $balance = 0;
    foreach ($this->customers as $customer) {
      if ($customer->archived) {
        $balance += $customer->balance;
      }
    }

    return $balance;
  }

  //------------------------------------------------------------------------------------
  //  REGLAS DE VALIDACION
  //------------------------------------------------------------------------------------
  public function rules()
  {
    $id = $this->customerId ? ",$this->customerId,customer_id" : '';
    return [
      'firstName' => 'required|string|max:45',
      'lastName' => 'nullable|string|max:45',
      'nit' => 'nullable|string|max:45|unique:carmu.customer,nit' . $id,
      'phone' => 'nullable|max:20|unique:carmu.customer,phone' . $id,
      'email' => 'nullable|max:45|email|unique:carmu.customer,email' . $id,
    ];
  }

  public $attributes = [
    'firstName' => 'Nombres',
    'lastName' => 'Apellidos',
    'phone' => 'Teléfono',
    'email' => 'Correo',
    'nit' => 'Nit/CC'
  ];

  public function updated($propertyName)
  {
    $this->validateOnly($propertyName, $this->rules(), [], $this->attributes);
  }

  public function updatedPhone($value)
  {
    if (!empty(trim($value))) {
      if (is_numeric($value)) {
        $this->validateOnly('phone', $this->rules(), [], $this->attributes);
      } else {
        $this->addError('phone', 'El campo teléfono debe ser un numero');
      }
    }
  }

  //------------------------------------------------------------------------------------
  //  METODOS PARA LA REDERIZACION DEL COMPONENTE
  //------------------------------------------------------------------------------------
  public function mount($id = null)
  {
    if($id){
      Customer::findOrFail($id);
      $this->edit($id);
    }
    $this->fill(request()->only('search'));
  }

  public function render()
  {
    return view('livewire.admin.carmu.customers-component')   
      ->layout("admin.carmu.customers.index");
  }

  //------------------------------------------------------------------------------------
  //  CRUD DE CLIENTES
  //------------------------------------------------------------------------------------
  public function store()
  {
    $this->trimFields();
    $this->validate($this->rules(), [], $this->attributes);
    try {
      Customer::create([
        'first_name' => $this->firstName,
        'last_name' => $this->lastName,
        'nit' => $this->nit,
        'phone' => $this->phone,
        'email' => $this->email,
      ]);
  
      $this->emit('stored');
      $this->resetFields();
    } catch (\Throwable $th) {
      $this->emit('error');
    }
  }

  public function destroy($id)
  {
    try {
      $customer = Customer::find($id, ['customer_id']);
      if ($customer) {
        if ($customer->credits()->sum('amount') - $customer->payments()->sum('amount') <= 0) {
          $customer->delete();
          $this->resetFields();
          $this->emit('deleted');
        }else{
          $this->emit('error', 'El cliente no se puede eliminar porque tiene saldo pendiente');
        }
      }else{
        $this->emit('error', 'Cliente no encontrado');
      }
    } catch (\Throwable $th) {
      $this->emit('notFound');
    }
  }

  public function edit($id)
  {
    $attributes = ['customer_id', 'first_name', 'last_name', 'phone', 'nit', 'email'];
    $customer = Customer::find($id, $attributes);

    if ($customer) {
      $this->view = 'edit';
      $this->customerId = $id;
      $this->firstName = $customer->first_name;
      $this->lastName = $customer->last_name;
      $this->email = $customer->email;
      $this->phone = $customer->phone;
      $this->nit = $customer->nit;
    } else {
      // $this->resetFields();
    }
  }

  public function update()
  {
    if ($this->customerId) {
      $this->trimFields();
      $this->validate($this->rules(), [], $this->attributes);

      $attributes = ['customer_id', 'first_name', 'last_name', 'phone', 'nit', 'email'];
      $customer = Customer::find($this->customerId, $attributes);
      if ($customer) {
        $customer->first_name = $this->firstName;
        $customer->last_name = $this->lastName;
        $customer->nit = $this->nit;
        $customer->phone = $this->phone;
        $customer->email = $this->email;
        $customer->save();
        $this->resetFields();
        $this->emit('updated');
      }
    }
  }

  public function archived($id)
  {
    $attributes = ['customer_id', 'archived'];
    $customer = Customer::find($id, $attributes);
    if($customer){
      $customer->archived = $customer->archived ? false : true;
      $customer->save();
      $message = $customer->archived ? 'El cliente ha sido archivado' : 'El cliente se ha sacado del archivo';
      $this->emit('archived', $message);
    }
  }
  public function resetFields()
  {
    $this->reset('customerId', 'view', 'firstName', 'lastName', 'nit', 'phone', 'email');
  }

  /**
   * Este metodo se encarga de cortar los espacios al inicio y al final
   * de los campos del formualario y se encarga de convertir los campos 
   * vacios de los campos que lo requiera en null
   */
  protected function trimFields()
  {
    //Corto los espacios en blanco
    $this->firstName = trim($this->firstName);
    $last = $this->lastName ? trim($this->lastName) : null;
    $nit = $this->nit ? trim($this->nit) : null;
    $phone = $this->phone ? trim($this->phone) : null;
    $email = $this->email ? trim($this->email) : null;

    // Formateo los campos vacíos  con null
    $this->lastName = empty($last) ? null : $last;
    $this->nit = empty($nit) ? null : $nit;
    $this->phone = empty($phone) ? null : $phone;
    $this->email = empty($email) ? null : $email;
  }
}
