<div class="form-group">
  <label for="saleMoment">Momento de la venta</label>
  <select name="saleMoment" id="transactionDate" class="form-control" x-model="moment">
    <option value="now">En este momento</option>
    <option value="other">En otra fecha</option>
  </select>
</div>

<div class="form-group" x-show.transition="moment=== 'other'">
  <label for="saleMoment">Selecciona una fecha</label>
  <div class="input-group mb-2">
    <div class="input-group-prepend">
      <span class="input-group-text">
        <i class="far fa-calendar-alt"></i>
      </span>
    </div>
    <input 
      type="date" 
      name="transactionDate" 
      class="form-control {{$errors->has('date') ? 'is-invalid' : ''}}"
      x-model="date"
      max="{{$this->maxDate}}"
    >

    @error('date')
    <div class="invalid-feedback" role="alert">
      {{$message}}
    </div>
    @enderror
  </div>

  <div class="form-check">
    <input type="checkbox" name="saleTime" id="saleTimeCheck" class="form-check-input" x-model="setTime">
    <label for="saleTimeCheck" class="form-check-label">Establecer hora</label>
  </div>
</div>

<div class="form-group row" x-show.transition="setTime && moment === 'other'">
  <label class="col-3 col-form-label" for="saleTimeInput">Hora:</label>
  <div class="col-9">
    <input 
      type="time" 
      name="saleTime" 
      id="saleTimeInput" 
      class="form-control {{$errors->has('time') ? 'is-invalid' : ''}}" 
      x-model="time"
    >
    @error('time')
    <div class="invalid-feedback" role="alert">
      {{$message}}
    </div>
    @enderror
  </div>
</div>

<div class="form-group">
  <label for="saleCategory" class="required">Categoría</label>
  <select 
    name="saleCategory" 
    id="saleCategory"
    class="form-control {{$errors->has('categoryId') ? 'is-invalid' : ''}}"
    x-model="categoryId"
  >
    <option value=" " disabled>Selecciona una</option>
    @foreach ($this->categories as $key => $name)
    <option value="{{$key}}">{{$name}}</option>
    @endforeach
  </select>

  @error('categoryId')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>

<div class="form-group">
  <label for="saleDescription" class="required">Descripción</label>
  <textarea 
    name="saleDescription" 
    id="saleDescription" 
    cols="30" 
    class="form-control {{$errors->has('description') ? 'is-invalid' : ''}}"
    placeholder="Escribe los detalles de la venta"
    x-model="description"
  ></textarea>

  @error('description')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>

<div class="form-group">
  <label for="saleAmount" class="required">Importe de la venta</label>
  <input 
    type="text" 
    name="saleAmount" 
    id="saleAmount" 
    class="form-control text-right text-bold {{$errors->has('amount') ? 'is-invalid' : ''}}" 
    placeholder="$ 0.00" 
    x-on:input="formatInput($event.target)" 
    x-on:change="$wire.amount = deleteCurrencyFormat($event.target.value)"
    style="font-size: 1.5em;letter-spacing: 2px;"
  >
  @error('amount')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>