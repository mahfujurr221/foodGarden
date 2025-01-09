<div class="form-group">
     <label for="">Product Type</label>
     <select name="product_type" id="" class="form-control {{ $errors->has('product_type') ? 'is-invalid': '' }}">
          @foreach ($types as $type)
          <option @isset($type_key) {{ $type_key == $type->key ? 'selected' : '' }} @endisset @if($type->key !=
               'standard')
               disabled
               @endif
               value="{{ $type->key }}">{{ $type->name }}</option>
          @endforeach
     </select>
     @if($errors->has('product_type'))
     <span class="invalid-feedback">{{ $errors->first('product_type') }}</span>
     @endif
</div>