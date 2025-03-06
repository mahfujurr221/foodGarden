@php
    
@endphp
<div class="form-group">
     <label for="">Brand</label>
     <select name="brand_id" id="" class="form-control">
     <option value="">Select Brand</option>
     @foreach (\App\Supplier::where('status', 1)->all() as $brand)
          <option 
          @isset($brand_id)
              {{ $brand_id == $brand->id ? 'selected' : '' }}
          @endisset 
          value="{{ $brand->id }}">{{ $brand->name }}</option>
     @endforeach
     </select>
     @if($errors->has('brand_id'))
          <span class="invalid-feedback">{{ $errors->first('brand_id') }}</span>
     @endif
</div>