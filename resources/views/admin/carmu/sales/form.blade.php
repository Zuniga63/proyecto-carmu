<div class="form-group">
  <label for="saleMoment">Momento de la venta</label>
  <select name="saleMoment" id="transactionDate" class="form-control" x-model="moment">
    <option value="now">En este momento</option>
    <option value="other">En otra fecha</option>
  </select>
</div>

<div class="form-group" x-show.transition="moment=== 'other'">
  <label for="saleMoment">Selecciona una fecha</label>
  <div class="input-group">
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
    class="form-control text-right font-bold {{$errors->has('amount') ? 'is-invalid' : ''}}" 
    placeholder="$ 0.00" 
    x-on:input="formatInput($event.target)" 
    x-on:change="$wire.amount = deleteCurrencyFormat($event.target.value)"
  >
  @error('amount')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>